<?php

namespace Modules\Communication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Modules\Communication\Emails\Samplemail;
use Modules\Communication\Helpers\MailConfigurator;

class EmailController extends Controller
{
    public function sendEmail(Request $request): JsonResponse
    {
        // ✅ Validate input to enforce correct types
        $validated = $request->validate([
            'to_email' => 'required|array',
            'to_email.*' => 'email',
            'subject' => 'required|string',
            'content' => 'required|string',
            'attachment' => 'nullable|string',
        ]);

        MailConfigurator::configureMail();

        $tomail = $validated['to_email'];
        $data = [
            'message' => $validated['content'],
            'subject' => $validated['subject'],
            'attachment' => $validated['attachment'] ?? null,
        ];

        foreach ($tomail as $email) {
            Mail::to($email)->send(new Samplemail($data));
        }

        return response()->json([
            'code' => 200,
            'message' => __('Email sent successfully.'),
            'data' => [],
        ], 200);
    }
}
