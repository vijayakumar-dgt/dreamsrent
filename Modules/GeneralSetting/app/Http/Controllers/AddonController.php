<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\GeneralSetting\Models\Addon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AddonController extends Controller
{
    public function addonIndex(Request $request): View
    {
        $jsonPath = base_path('addon_modules.json');

        $jsonData = [];
        if (file_exists($jsonPath)) {
            $fileContents = file_get_contents($jsonPath);
            if ($fileContents !== false) {
                $jsonData = json_decode($fileContents, true);
            }
        }
        $addonModules = Addon::get(['id', 'name', 'slug', 'version', 'price', 'status']);
        /** @var array<string, mixed> $jsonData */
        $jsonCollection = collect($jsonData);
        $modules = $jsonCollection->map(function ($jsonModule) use ($addonModules) {
            $dbModule = $addonModules->firstWhere('name', $jsonModule['module_name']);
            return [
                'module_name' => $jsonModule['module_name'],
                'module_image' => $jsonModule['module_image'],
                'module_version' => $jsonModule['module_version'],
                'module_price' => $jsonModule['module_price'],
                'status' => $dbModule ? 1 : 0,
                'git_link' => $jsonModule['git_link'] ?? '',
                'purchase_link' => $jsonModule['purchase_link'] ?? '',
            ];
        });

        return view('generalsetting::addon.index', compact('modules'));
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $orderBy = $request->order_by ?? 'desc';

            $addonModules = Addon::orderBy('id', $orderBy)
                ->get(['id', 'slug', 'name', 'version', 'price', 'status']);

            $jsonPath = base_path('addon_modules.json');
            $fileContents = file_exists($jsonPath) ? file_get_contents($jsonPath) : '';
            $jsonData = $fileContents !== false ? json_decode($fileContents, true) : [];
            /** @var array<int, array<string, mixed>> $jsonData */
            $jsonCollection = collect($jsonData);
            /** @var \Illuminate\Support\Collection $addonModules */
            /** @var \Illuminate\Support\Collection $jsonCollection */
            /**
             * Maps over the $addonModules collection and applies a callback function.
             *
             * @param \Illuminate\Support\Collection<TKey, TValue> $addonModules The collection of addon modules.
             * @param \Illuminate\Support\Collection<TKey, TValue> $jsonCollection The JSON collection used within the callback.
             *
             * @return array The transformed array after mapping over the collection.
             */
            $data = $addonModules->map(function ($dbModule) use ($jsonCollection): array {
                $jsonModule = $jsonCollection->firstWhere('module_name', $dbModule->name);
                return [
                    'id' => $dbModule->id,
                    'slug' => $dbModule->slug,
                    'name' => $dbModule->name,
                    'version' => $dbModule->version,
                    'price' => $dbModule->price,
                    'status' => $dbModule->status,
                    'module_image' => $jsonModule['module_image'] ?? null,
                ];
            });

            return response()->json([
                'code' => 200,
                'message' =>  __('admin.general_settings.addon_retrieve_success'),
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' =>  __('admin.general_settings.retrive_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function changeAddonStatus(Request $request): JsonResponse
    {
        $id = $request->id;
        $status = $request->status;

        try {
            Addon::where('id', $id)->update([
                'status' => $status
            ]);

            Cache::forget('addonModules');

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.addon_update_success'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.addon_update_success'),
            ], 500);
        }
    }

    public function listNewAddonModules(Request $request): JsonResponse
    {
        try {
            $jsonPath = base_path('addon_modules.json');

            $fileContents = file_exists($jsonPath) ? file_get_contents($jsonPath) : '';
            $jsonData = $fileContents !== false ? json_decode($fileContents, true) : [];
            $addonModules = Addon::get(['id', 'name', 'slug', 'version', 'price', 'status']);
            /** @var array<int, array<string, mixed>> $jsonData */
            $jsonCollection = collect($jsonData);
            $modules = $jsonCollection->map(function ($jsonModule) use ($addonModules) {
                $dbModule = $addonModules->firstWhere('name', $jsonModule['module_name']);

                return [
                    'module_name' => $jsonModule['module_name'],
                    'module_image' => $jsonModule['module_image'],
                    'module_version' => $jsonModule['module_version'],
                    'module_price' => $jsonModule['module_price'],
                    'status' => $dbModule ? 1 : 0,
                    'git_link' => $jsonModule['git_link'] ?? '',
                    'purchase_link' => $jsonModule['purchase_link'] ?? '',
                ];
            });
            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.addon_retrieve_success'),
                'data' => $modules,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function purchaseModule(Request $request): JsonResponse
    {
        try {
            $data = [
                'name' => $request->module_name,
                'version' => $request->module_version,
                'price' => $request->module_price,
                'status' => 1,
                'slug' => Str::slug(Str::plural($request->module_name))
            ];
            $repoUrl = $request->input('git_link');
            $moduleName = $request->module_name;
            $modulesPath = base_path("Modules/{$moduleName}");
            $moduleNameLower = strtolower($moduleName);
            if (!file_exists($modulesPath)) {
                $command = "git clone \"$repoUrl\" \"$modulesPath\" 2>&1";
                $output = shell_exec($command);
            }
            if (file_exists($modulesPath)) {
                $this->copyJsFiles($moduleName, 'admin', public_path('assets/js/'));
                $this->copyJsFiles($moduleName, 'provider', public_path('front/js/'));
                $this->executeSqlFiles($moduleName);
                $this->updateModuleStatus($moduleName);
                $this->updateModulesConfig($moduleName, $moduleNameLower);
                Addon::create($data);
                clearCache();
            } else {
                return response()->json([
                    'code' => 200,
                    'message' => __('admin.general_settings.module_activated_successfully'),
                ], 500);
            }
            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.module_activated_successfully'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.retrive_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    private function copyJsFiles(string $moduleName, string $type, string $destinationDir): void
    {
        $sourceDir = base_path("Modules/{$moduleName}/js/{$type}/");
        if (File::exists($sourceDir)) {
            $files = File::glob($sourceDir . '*.js');
            if (!empty($files)) {
                foreach ($files as $file) {
                    $fileName = basename($file);
                    $destinationPath = $destinationDir . '/' . $fileName;

                    if (!File::exists($destinationPath) || File::lastModified($file) > File::lastModified($destinationPath)) {
                        File::copy($file, $destinationPath);
                    }
                }
            }
        }
    }
    private function executeSqlFiles(string $moduleName): void
    {
        $sqlPath = base_path("Modules/{$moduleName}/sql");
        if (File::exists($sqlPath)) {
            $sqlFiles = File::files($sqlPath);
            foreach ($sqlFiles as $file) {
                if ($file->getExtension() == 'sql') {
                    $sql = File::get($file);
                    if (preg_match('/CREATE TABLE\s+`?(\w+)`?/i', $sql, $matches)) {
                        $tableName = $matches[1];
                        $tableExists = DB::select("SHOW TABLES LIKE '{$tableName}'");
                        if ($tableExists) {
                            // Drop the table before recreating it
                            DB::statement("DROP TABLE IF EXISTS `{$tableName}`");
                        }
                        $sql = preg_replace('/CREATE TABLE\s+`?(\w+)`?/i', 'CREATE TABLE IF NOT EXISTS `$1`', $sql);
                    }
                    if (!is_null($sql)) {
                        DB::unprepared($sql);
                    }
                }
            }
        }
    }
    private function updateModuleStatus(string $moduleName): void
    {
        $moduleStatusPath = base_path('modules_statuses.json');
        if (!File::exists($moduleStatusPath)) {
            $encodedData = json_encode([], JSON_PRETTY_PRINT);
            if ($encodedData !== false) {
                File::put($moduleStatusPath, $encodedData);
            } else {
                throw new \RuntimeException('Failed to encode JSON data.');
            }
        }
        $modules = json_decode(File::get($moduleStatusPath), true);
        $modules[$moduleName] = true;
        $encodedModules = json_encode($modules, JSON_PRETTY_PRINT);
        if ($encodedModules === false) {
            throw new \RuntimeException('Failed to encode JSON data.');
        }
        File::put($moduleStatusPath, $encodedModules);
    }
    private function updateModulesConfig(string $moduleName, string $moduleNameLower): void
    {
        $moduleClass = "Modules\\" . ucfirst($moduleName) . "\\Providers\\" . ucfirst($moduleName) . "ServiceProvider::class";
        $configPath = config_path('modules.php');
        if (File::exists($configPath)) {
            $configContent = File::get($configPath);
            if (strpos($configContent, "'$moduleNameLower' => [") === false) {
                $newModuleEntry = "\n        '$moduleNameLower' => [\n            'active' => true,\n            'providers' => [\n                $moduleClass,\n            ],\n        ],";
                $newConfigContent = preg_replace('/(\'modules\'\s*=>\s*\[)/s', "$1$newModuleEntry", $configContent, 1);
                if (!is_null($newConfigContent)) {
                    File::put($configPath, $newConfigContent);
                }
            }
        }
    }
    public function updateModule(Request $request): string
    {
        $moduleName = $request->module ?? '';
        $moduleName = ucfirst($moduleName);
        $modulesPath = base_path("Modules/{$moduleName}");
        $moduleNameLower = strtolower($moduleName);
        if (file_exists($modulesPath)) {
            $command = "cd {$modulesPath} && git reset --hard && git pull origin main 2>&1";
            $output = shell_exec($command);
            $this->copyJsFiles($moduleName, 'admin', public_path('assets/js/'));
            $this->copyJsFiles($moduleName, 'provider', public_path('front/js/'));
            $this->executeSqlFiles($moduleName);
            return __('admin.general_settings.module_updated_successfully');
        } else {
            abort(404, __('admin.general_settings.module_not_found'));
        }
    }
}
