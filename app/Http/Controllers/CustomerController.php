<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\User;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    protected CustomerRepositoryInterface $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function index(Request $request): View
    {
        $data = $this->customerRepository->index();
        return view('admin.customers', $data);
    }

    public function store(CustomerRequest $request): JsonResponse
    {
        $response = $this->customerRepository->store($request);
        return response()->json($response, $response['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $response = $this->customerRepository->list($request);
        return response()->json($response, $response['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->customerRepository->edit($id);
        return response()->json($response, $response['code']);
    }

    public function customerDetails(Request $request): View
    {
        $id = customDecrypt($request->id, User::$userSecretKey);
        $data = $this->customerRepository->getCustomerDetails($id);
        return view('admin.customer_details', $data);
    }

    public function delete(Request $request): JsonResponse
    {
        $response = $this->customerRepository->delete($request);
        return response()->json($response, $response['code'] ?? 200);
    }
}
