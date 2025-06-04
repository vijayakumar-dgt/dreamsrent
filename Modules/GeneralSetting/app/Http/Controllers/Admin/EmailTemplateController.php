<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Modules\GeneralSetting\Http\Requests\EmailTemplateRequest;
use Modules\GeneralSetting\Repositories\Contracts\EmailTemplateRepositoryInterface;

class EmailTemplateController extends Controller
{
    protected EmailTemplateRepositoryInterface $repository;

    public function __construct(EmailTemplateRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(): View
    {
        return view('generalsetting::system_settings.email_template', [
            'tags' => $this->repository->getAllNotificationTags(),
            'notificationTypes' => $this->repository->getAllNotificationTypes()
        ]);
    }

    public function store(EmailTemplateRequest $request): JsonResponse
    {
        try {
            $emailTemplate = $this->repository->createOrUpdateEmailTemplate($request->all());

            $message = $request->has('id')
                ? __('admin.general_settings.email_template_updated_success')
                : __('admin.general_settings.email_templated_success');

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $message
            ]);
        } catch (\Throwable $th) {
            return $this->jsonErrorResponse($th);
        }
    }

    public function getEmailTemplates(Request $request): JsonResponse
    {
        try {
            $result = $this->repository->getEmailTemplates($request->all());

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $result['totalRecords'],
                'recordsFiltered' => $result['filteredRecords'],
                'data' => $result['data']
            ]);
        } catch (\Throwable $th) {
            return $this->jsonErrorResponse($th);
        }
    }

    public function getEmailTemplate(int $id): JsonResponse
    {
        try {
            $emailTemplate = $this->repository->getEmailTemplateById($id);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $emailTemplate
            ]);
        } catch (\Throwable $th) {
            return $this->jsonErrorResponse($th);
        }
    }

    public function deleteEmailTemplate(Request $request): JsonResponse
    {
        try {
            $this->repository->deleteEmailTemplate($request->id);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.email_template_deleted_success')
            ]);
        } catch (\Throwable $th) {
            return $this->jsonErrorResponse($th);
        }
    }

    public function getTags(int $id): JsonResponse
    {
        try {
            $data = $this->repository->getTagsByNotificationType($id);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $data['notification_type'],
                'tags' => $data['tags']
            ]);
        } catch (\Throwable $th) {
            return $this->jsonErrorResponse($th);
        }
    }

    protected function jsonErrorResponse(\Throwable $exception, int $code = 422): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'code' => $code,
            'message' => $exception->getMessage()
        ], $code);
    }
}
