<?php

namespace Modules\Installer\Http\Controllers;

use Closure;
use Exception;
use App\Models\Administrator;
use App\Enums\UserStatus;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\Installer\Enums\InstallerInfo;
use Modules\Installer\Models\Configuration;
use Modules\Installer\Traits\InstallerMethods;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;



class InstallerController extends Controller
{
    use InstallerMethods;

    /**
     * @return View
     */
    public function requirements(): View
    {
        [$checks, $success, $failedChecks] = $this->checkMinimumRequirements();
        session()->put('step-2-complete', true);

        /** @var view-string $view */
        $view = 'installer::requirements';
        return view($view, compact('checks', 'success', 'failedChecks'));
    }
    /**
     * @return View|RedirectResponse
     */
    public function database(): View|RedirectResponse
    {
        if ($this->requirementsCompleteStatus()) {
            session()->put('requirements-complete', true);

            /** @var view-string $view */
            $view = 'installer::database';
            return view($view, ['isLocalHost' => InstallerInfo::isRemoteLocal()]);
        }

        return redirect()->route('setup.requirements')->withInput()->withErrors([
            'errors' => 'Your server does not meet the minimum requirements.',
        ]);
    }

    public function databaseSubmit(Request $request): JsonResponse|RedirectResponse
    {
        if (!$this->requirementsCompleteStatus()) {
            return redirect()->route('setup.requirements')
                ->withInput()
                ->withErrors(['errors' => 'Your server does not meet the minimum requirements.']);
        }

        try {
            $request->validate([
                'host' => 'required|ip',
                'port' => 'required|integer',
                'database' => 'required',
                'user' => 'required',
            ]);

            if (!InstallerInfo::isRemoteLocal()) {
                $request->validate([
                    'password' => 'required',
                ]);
            }

            $databaseDetails = [
                'host' => $request->input('host'),
                'port' => $request->input('port'),
                'database' => $request->input('database'),
                'user' => $request->input('user'),
                'password' => $request->input('password'),
                'reset_database' => $request->input('reset_database'),
            ];

            $databaseCreate = $this->createDatabaseConnection($databaseDetails);

            if ($databaseCreate !== true) {
                if ($databaseCreate === 'not-found') {
                    return response()->json([
                        'create_database' => true,
                        'message' => 'Database not found! Please create the database first.'
                    ], 200);
                } elseif ($databaseCreate === 'table-exist') {
                    return response()->json([
                        'reset_database' => true,
                        'message' => 'This database has tables already. Please create a new database or reset existing tables first to continue'
                    ], 200);
                }
                return response()->json([
                    'success' => false,
                    'message' => $databaseCreate
                ], 200);
            }

            $deleteDummyData = false;
            if ($request->boolean('fresh_install')) {
                $deleteDummyData = true;
                Cache::put('fresh_install', true, now()->addMinutes(60));
                $migration = $this->importDatabase(InstallerInfo::getFreshDatabaseFilePath());
            } else {
                $migration = $this->importDatabase(InstallerInfo::getDummyDatabaseFilePath());
            }

            if ($migration !== true) {
                return response()->json([
                    'success' => false,
                    'message' => $migration
                ], 200);
            }

            // Create properly typed config array
            $envConfig = [
                'host' => (string)$request->input('host'),
                'port' => $request->input('port'), // int|string
                'database' => (string)$request->input('database'),
                'user' => (string)$request->input('user'),
                'password' => (string)$request->input('password', ''),
            ];
            $this->changeEnvDatabaseConfig($envConfig);

            if ($deleteDummyData) {
                $this->removeDummyFiles();
            }

            Cache::forget('fresh_install');
            session()->put('step-3-complete', true);
            Configuration::updateStep(1);

            return response()->json([
                'success' => true,
                'message' => 'Successfully setup the database'
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Database connection failed! Look like you have entered wrong database credentials (host, port, database, user or password).'
            ], 500);
        }
    }

/**
 * @param array<string, string> $data
 */
protected function updateEnv(array $data): void
{
    $envPath = app()->environmentFilePath();
    $rawContent = file_get_contents($envPath);

    if ($rawContent === false) {
        throw new \RuntimeException("Failed to read .env file at: {$envPath}");
    }

    $content = (string) $rawContent;

    foreach ($data as $key => $value) {
        $content = preg_replace(
            "/^{$key}=.*/m",
            "{$key}={$value}",
            $content
        ) ?? $content; // fallback in case of null
    }

    file_put_contents($envPath, $content);
}



    public function account(): View|RedirectResponse
    {
        session()->put('step-1-complete', true);
        session()->put('step-2-complete', true);
        session()->put('step-3-complete', true);
        $step = Configuration::stepExists();

        if ($step >= 1 && $step < 5 && $this->requirementsCompleteStatus()) {
            $admin = $step >= 2 ? User::select('name', 'email')->first() : null;
            /** @var view-string $view */
            $view = 'installer::account';
            return view($view, compact('admin'));
        }

        if ($step == 5 || !$this->requirementsCompleteStatus()) {
            return redirect()->route('setup.requirements');
        }

        return redirect()->route('setup.database');
    }

    public function accountSubmit(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|same:confirm_password',
            ]);

            $admin = User::updateOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name,
                    'password' => Hash::make($request->password),
                    'user_type' => 1,
                    'role_id' => 1,
                ]
            );

            UserDetail::updateOrCreate(['user_id' => $admin->id]);

            Configuration::updateStep(2);
            session()->put('step-4-complete', true);

            return response()->json(['success' => true, 'message' => 'Admin Account Successfully Created'], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to Create Admin Account',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function configuration(): View|RedirectResponse
    {
        $step = Configuration::stepExists();

        if ($step == 5 || !$this->requirementsCompleteStatus()) {
            return redirect()->route('setup.requirements');
        }

        if ($step < 2) {
            return redirect()->route('setup.account');
        }

        $app_name = $step >= 3 ? optional(GeneralSetting::where('key', 'organization_name')->first())->value : null;

        /** @phpstan-var view-string $view */
        $view = 'installer::config';
        return view($view, compact('app_name'));
    }

    public function configurationSubmit(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'config_app_name' => 'required|string',
            ]);

            Configuration::updateStep(3);

            GeneralSetting::where('key', 'organization_name')->update(['value' => $request->config_app_name]);

            if (Cache::has('last_updated_at')) {
                GeneralSetting::where('key', 'last_update_date')->update(['value' => Cache::get('last_updated_at')]);
                Cache::forget('last_updated_at');
            }

            Cache::forget('setting');
            Configuration::updateStep(4);
            session()->put('step-5-complete', true);
            session()->put('step-6-complete', true);

            return response()->json(['success' => true, 'message' => 'Configuration Successfully Saved'], 200);
        } catch (Exception $e) {
            Log::error($e);

            return response()->json(['success' => false, 'message' => 'Configuration Failed'], 200);
        }
    }
    public function smtp(): RedirectResponse
    {
        $step = Configuration::stepExists();

        if ($step == 4 || !$this->requirementsCompleteStatus()) {
            return redirect()->route('setup.complete');
        }
        return redirect()->route('setup.complete');
    }
    /**
     * Skip the SMTP setup and move to the next setup step.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function smtpSkip(): RedirectResponse
    {
        Configuration::updateStep(4);
        session()->put('step-6-complete', true);
        return redirect()->route('setup.complete');
    }
    public function setupComplete(): Response|RedirectResponse
    {
        session()->put('step-7-complete', true);

        if (Configuration::setupStepCheck(4) && $this->requirementsCompleteStatus()) {
            $envContent = File::get(base_path('.env'));
            $envContent = preg_replace(
                ['/APP_ENV=(.*)\s/', '/APP_DEBUG=(.*)\s/'],
                ['APP_ENV=production' . "\n", 'APP_DEBUG=false' . "\n"],
                $envContent
            );
            if ($envContent !== null) {
                File::put(base_path('.env'), $envContent);
            }

            /** @var view-string $view */
            $view = 'installer::complete';
            return response(view($view));
        }

        if (Configuration::setupStepCheck(5) && $this->requirementsCompleteStatus()) {
            return $this->completedSetup('home');
        }

        if (Configuration::stepExists() < 4) {
            return redirect()->route('setup.smtp');
        }

        return redirect()->back()->withInput()->withErrors(['errors' => 'Setup Is Incomplete hh']);
    }

    /**
     * Launch the website after setup completion.
     *
     * @param string $type
     * @return mixed
     */
    public function launchWebsite(string $type): mixed
    {
        $result = $this->completedSetup($type);
        $filePath = base_path('modules_statuses.json');

        if (file_exists($filePath)) {
            $fileContent = file_get_contents($filePath);

            if ($fileContent !== false) {
                $statuses = json_decode($fileContent, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $statuses['Installer'] = false;
                    $updatedContent = json_encode($statuses, JSON_PRETTY_PRINT);
                    file_put_contents($filePath, $updatedContent);
                }
            }
        }

        return $result;
    }
}
