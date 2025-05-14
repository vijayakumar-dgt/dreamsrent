<?php

namespace App\Http\Controllers;

use App\Models\WalletHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class WalletController extends Controller
{
    /**
     * @var PayPalClient|null
     */
    protected ?PayPalClient $provider;

    public function __construct()
    {
        if (empty(env('PAYPAL_SANDBOX_CLIENT_ID')) || empty(env('PAYPAL_SANDBOX_CLIENT_SECRET'))) {
            $this->provider = null;
        } else {
            $this->provider = new PayPalClient();

            if ($this->provider) {
                $this->provider->getAccessToken();
            }
        }
    }

    public function wallet(Request $request): View
    {
        $seo_title = __('web.user.my_wallet');
        return view('frontend.user.wallet', compact('seo_title'));
    }

    public function addWallet(Request $request): JsonResponse
    {
        $request->validate([
            'wallet_amount' => 'required|numeric|min:1',
            'payment_type' => 'required|in:paypal,stripe,wallet_one',
        ]);

        $user = Auth::guard('web')->user();
        $amount = $request->wallet_amount;
        $paymentType = ucfirst($request->payment_type);

        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized access.',
            ], 401);
        }

        if ($paymentType === "Paypal") {
            try {
                if (!$this->provider) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PayPal is currently unavailable. Please choose another payment method.',
                    ], 422);
                }
                $this->provider->getAccessToken();

                $order = [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => "USD",
                                'value' => $amount,
                            ],
                        ],
                    ],
                    'application_context' => [
                        'return_url' => url('user/paypal-payment-success-wallet'),
                        'cancel_url' => url('payment-failed'),
                    ],
                ];

                $response = $this->provider->createOrder($order);

                if ($response instanceof \Psr\Http\Message\StreamInterface) {
                    $response = json_decode($response->getContents(), true);
                } elseif (is_string($response)) {
                    $response = json_decode($response, true);
                }

                if (!$response || !isset($response['id'])) {
                    return response()->json([
                        'code' => 500,
                        'message' => 'Failed to create PayPal order.',
                    ]);
                }

                WalletHistory::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'payment_type' => $paymentType,
                    'status' => 'Pending',
                    'transaction_id' => $response['id'],
                    'transaction_date' => now(),
                ]);

                if (!isset($response['links'][1]['href'])) {
                    return response()->json([
                        'code' => 500,
                        'message' => 'Failed to generate PayPal payment link.',
                    ]);
                }
                return response()->json([
                    'code' => 200,
                    'message' => 'PayPal payment initiated. Redirecting...',
                    'paypal_url' => $response['links'][1]['href'],
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'code' => 500,
                    'message' => 'PayPal authentication failed: ' . $e->getMessage(),
                ]);
            }
        }

        if ($request->payment_type == "stripe") {
            Stripe::setApiKey(config('services.stripe.secret'));
            $currency_details = "USD";

            $session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency_details,
                        'product_data' => ['name' => "Wallet Top-up"],
                        'unit_amount' => intval($amount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('user.stripe.payment.success.wallet') . "?session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => route('payment-failed'),
            ]);

            WalletHistory::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'payment_type' => $paymentType,
                'status' => 'Pending',
                'transaction_id' => $session->id,
                'transaction_date' => now(),
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Stripe payment initiated. Redirecting...',
                'stripe_url' => $session->url,
            ]);
        }

        WalletHistory::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'payment_type' => $paymentType,
            'status' => 'Pending',
            'transaction_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment added successfully!',
        ]);
    }

    public function paypalPaymentSuccessWallet(Request $request): JsonResponse | RedirectResponse
    {
        try {
            $accessToken = $this->provider->getAccessToken();
            if (!$accessToken) {
                return response()->json([
                    'code' => 401,
                    'message' => 'PayPal authentication failed.',
                ], 401);
            }

            $response = $this->provider->capturePaymentOrder($request->get('token'));

            if ($response instanceof \Psr\Http\Message\StreamInterface) {
                $response = json_decode($response->getContents(), true);
            } elseif (is_string($response)) {
                $response = json_decode($response, true);
            }

            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                WalletHistory::where('transaction_id', $response['id'])->update(['status' => 'Completed']);

                return redirect()->route('user.wallet', ['transaction_id' => $response['id']]);
            } else {
                return response()->json([
                    'code' => 400,
                    'message' => 'Wallet payment capture failed.',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function stripePaymentSuccessWallet(Request $request): JsonResponse | RedirectResponse
    {
        try {
            Stripe::setApiKey(config('stripe.test.sk'));
            $sessionId = $request->get('session_id');

            WalletHistory::where('transaction_id', $sessionId)->update(['status' => 'Completed']);

            return redirect()->route('user.wallet', ['transaction_id' => $sessionId]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function paymentFailed(Request $request): JsonResponse
    {
       return redirect()->route('user.wallet');        
    }

    public function walletHistoryList(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('web')->user();

            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthorized access.',
                ], 401);
            }

            $walletHistory = WalletHistory::where('user_id', $user->id)
                ->where('type', '1')
                ->orderBy('transaction_date', 'desc')
                ->get();

            $totalCredit = WalletHistory::where('user_id', $user->id)
                ->where('status', 'Completed')
                ->where('type', '1')
                ->sum('amount');

            $totalDebit = WalletHistory::where('user_id', $user->id)
                ->where('status', 'Completed')
                ->where('type', '2')
                ->sum('amount');

            $totalBalance = $totalCredit - $totalDebit;
            $currencySymbol = getDefaultCurrencySymbol();
            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Wallet transaction history retrieved successfully.',
                'data' => $walletHistory,
                'total_credit' => $totalCredit,
                'total_debit' => $totalDebit,
                'total_balance' => $totalBalance,
                'currency_symbol' => $currencySymbol,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => 'An error occurred while retrieving wallet history.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
