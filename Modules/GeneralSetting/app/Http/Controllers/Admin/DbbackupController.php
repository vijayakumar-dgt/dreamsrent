<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\DeleteBackupRequest;
use Modules\GeneralSetting\Repositories\Contracts\DbbackupInterface;

class DbbackupController extends Controller
{
    protected $dbbackupRepository;

    public function __construct(DbbackupInterface $dbbackupRepository)
    {
        $this->dbbackupRepository = $dbbackupRepository;
    }

    public function datebaseSettings(): View
    {
        return view('generalsetting::other_settings.database-backup');
    }

    public function systemBackupSettings(): View
    {
        return view('generalsetting::other_settings.system-backup');
    }

    public function listBackups(): JsonResponse
    {
        try {
            $backups = $this->dbbackupRepository->getDatabaseBackups();
            $baseUrl = asset('storage/database');

            $formattedBackups = $backups->map(function ($backup) use ($baseUrl) {
                return [
                    'id'           => $backup->id,
                    'name'         => $backup->name,
                    'created_on'   => $backup->created_at ? formatDateTime($backup->created_at) : null,
                    'download_url' => "{$baseUrl}/{$backup->name}",
                ];
            });

            return response()->json([
                'success' => true,
                'message' => __('admin.general_settings.backup_successfull'),
                'data'    => $formattedBackups,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin.general_settings.retrieve_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function listSystemBackups(): JsonResponse
    {
        try {
            $backups = $this->dbbackupRepository->getSystemBackups();
            $baseUrl = asset('storage/backups');

            $formattedBackups = $backups->map(function ($backup) use ($baseUrl) {
                return [
                    'id'           => $backup->id,
                    'name'         => $backup->name,
                    'created_on'   => $backup->created_at ? formatDateTime($backup->created_at) : null,
                    'download_url' => "{$baseUrl}/{$backup->name}",
                ];
            });

            return response()->json([
                'success' => true,
                'message' => __('admin.general_settings.backup_successfull'),
                'data'    => $formattedBackups,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin.general_settings.retrieve_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteSystemBackup(DeleteBackupRequest $request): JsonResponse
    {
        try {
            $this->dbbackupRepository->deleteBackup($request->id);

            return response()->json([
                'code'         => 200,
                'message'      => __('admin.general_settings.deleted_successfull'),
                'totalRecords' => $this->dbbackupRepository->getTotalBackupCount()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.retrieve_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function deleteBackup(DeleteBackupRequest $request): JsonResponse
    {
        try {
            $this->dbbackupRepository->deleteBackup($request->id);

            return response()->json([
                'code'         => 200,
                'message'      => __('admin.general_settings.deleted_successfull'),
                'totalRecords' => $this->dbbackupRepository->getTotalBackupCount()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
