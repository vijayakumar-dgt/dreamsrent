<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterRequest;
use App\Http\Requests\SendNewsLetterRequest;
use App\Models\NewsletterSubscriber;
use App\Models\UserDetail;
use App\Repositories\Contracts\NewsLetterRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Communication\Http\Controllers\EmailController;
use Modules\GeneralSetting\Models\EmailTemplate;
use Modules\GeneralSetting\Models\GeneralSetting;
use Spatie\Sitemap\Tags\News;

class NewsletterController extends Controller
{
    protected NewsLetterRepositoryInterface $NewsLetterRepository;

    public function __construct(NewsLetterRepositoryInterface $NewsLetterRepository)
    {
        $this->NewsLetterRepository = $NewsLetterRepository;
    }
    public function index(Request $request): View
    {
        return view('admin.newsletters');
    }

    public function save(NewsletterRequest $request): JsonResponse
    {
        $result = $this->NewsLetterRepository->save($request);
        return response()->json($result, $result['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $result = $this->NewsLetterRepository->list($request);
        return response()->json($result, $result['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $result = $this->NewsLetterRepository->delete($request);
        return response()->json($result, $result['code']);
    }

    public function sendNewsletter(SendNewsLetterRequest $request): JsonResponse
    {
        $result = $this->NewsLetterRepository->sendNewsletter($request);
        return response()->json($result, $result['code']);
    }
}
