<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Exceptions\DumpFailed;
use Illuminate\Support\Facades\Log;
use Modules\GeneralSetting\Models\Dbbackup;


class DbbackupController extends Controller
{
    public function datebaseSettings(Request $request): View
    {
        return view('generalsetting::other_settings.database-backup');
    }

    public function systemBackupSettings(Request $request): View
    {
        return view('generalsetting::other_settings.system-backup');
    }

    public function backupDatabase(Request $request)
    {
        try {
            $backupDir = storage_path('app/public/dbbackups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0777, true);
            }

            $fileName = 'backup_' . now()->format('Y_m_d_His') . '.sql';
            $backupPath = $backupDir . DIRECTORY_SEPARATOR . $fileName;

            $dbName = config('database.connections.mysql.database');
            $dbUserName = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host', '127.0.0.1'); // Ensure using 127.0.0.1
            $dbPort = config('database.connections.mysql.port', 3306);

            $mysqldumpPath = 'C:\\newxampp\\mysql\\bin\\mysqldump.exe'; // Ensure the path is correct

            // Command to execute
            $command = "\"{$mysqldumpPath}\" --user={$dbUserName} --password={$dbPassword} --host={$dbHost} --port={$dbPort} --protocol=TCP {$dbName} > \"{$backupPath}\"";

            // Execute the command
            exec($command, $output, $result);

            if ($result !== 0) {
                Log::error("Database backup failed with exit code {$result}");
                return redirect()->route('admin.database-settings')->with('error', __('admin.general_settings.retrieve_error'));
            }

            // Save backup details in database
            Dbbackup::create(['name' => $fileName]);

            return redirect()->route('admin.datebase-settings')->with('success',  __('admin.general_settings.backup_successfull'));
        } catch (\Exception $e) {
            Log::error('An unexpected error occurred during database backup: ' . $e->getMessage());
            return redirect()->route('admin.datebase-settings')->with('error',  __('admin.general_settings.retrieve_error') . $e->getMessage());
        }
    }

    public function listBackups()
    {
        try {
            $backups = Dbbackup::where('type', 1)->orderBy('created_at', 'desc')->get();

            $baseUrl = asset('storage/dbbackups');

            // Format data for response
            $formattedBackups = $backups->map(function ($backup) use ($baseUrl) {
                return [
                    'id' => $backup->id,
                    'name' => $backup->name,
                    'created_on' => $backup->created_at ? $backup->created_at->format('d M Y') : null,
                    'download_url' => "{$baseUrl}/{$backup->name}",
                ];
            });

            return response()->json([
                'success' => true,
                'message' => __('admin.general_settings.backup_successfull'),
                'data' => $formattedBackups,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Fetching backups failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' =>__('admin.general_settings.retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function listSystemBackups()
    {
        try {
            $backups = Dbbackup::where('type', 2)->orderBy('created_at', 'desc')->get();

            $baseUrl = asset('storage/backups');

            // Format data for response
            $formattedBackups = $backups->map(function ($backup) use ($baseUrl) {
                return [
                    'id' => $backup->id,
                    'name' => $backup->name,
                    'created_on' => $backup->created_at ? $backup->created_at->format('d M Y') : null,
                    'download_url' => "{$baseUrl}/{$backup->name}",
                ];
            });

            return response()->json([
                'success' => true,
                'message' =>__('admin.general_settings.backup_successfull'),
                'data' => $formattedBackups,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Fetching backups failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('admin.general_settings.retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function deleteSystemBackup(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer|exists:dbbackups,id'
            ]);

            $backup = Dbbackup::findOrFail($request->id);
            $backup->delete();

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.deleted_successfull'),
                'totalRecords' => Dbbackup::count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.retrieve_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteBackup(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer|exists:dbbackups,id'
            ]);

            $backup = Dbbackup::findOrFail($request->id);
            $backup->delete();

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.deleted_successfull'),
                'totalRecords' => Dbbackup::count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
