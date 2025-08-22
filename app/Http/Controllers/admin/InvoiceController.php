<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    protected InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function index(): View
    {
        $data = $this->invoiceRepository->index();
        return view("admin.invoice.index", [...$data]);
    }

    public function addInvoice(): View
    {
        $data = $this->invoiceRepository->addInvoice();
        return view("admin.invoice.add-invoice", [...$data]);
    }

    public function store(InvoiceRequest $request): JsonResponse
    {
        $response = $this->invoiceRepository->store($request);
        return response()->json($response);
    }

    public function edit(?int $id): View
    {
        $data = $this->invoiceRepository->edit($id);
        return view("admin.invoice.edit-invoice", [...$data]);
    }

    public function destroy(?int $id): JsonResponse
    {
        return $this->invoiceRepository->delete($id);
    }

    public function update(InvoiceRequest $request, ?int $id): RedirectResponse
    {
        return $this->invoiceRepository->update($request, $id);
    }
}
