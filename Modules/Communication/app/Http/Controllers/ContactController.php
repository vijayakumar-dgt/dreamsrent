<?php

namespace Modules\Communication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Communication\Models\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Modules\Communication\Http\Requests\ContactMessagesRequest;
use Modules\Communication\Repositories\Contracts\ContactMessagesRepositoryInterface;

class ContactController extends Controller
{
    protected ContactMessagesRepositoryInterface $contactMessagesRepository;

    public function __construct(ContactMessagesRepositoryInterface $contactMessagesRepository)
    {
        $this->contactMessagesRepository = $contactMessagesRepository;
    }
    public function index(): View
    {
        return view('communication::contact-message.index');
    }

    public function store(ContactMessagesRequest $request): JsonResponse
    {
        $result = $this->contactMessagesRepository->store($request);
        return response()->json($result, $result['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $result = $this->contactMessagesRepository->list($request);
        return response()->json($result, $result['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $result = $this->contactMessagesRepository->delete($request);
        return response()->json($result, $result['code']);
    }
}
