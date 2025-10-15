<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\GeneralSetting\Repositories\Contracts\PaymentAndStorageSettingsInterface;
use Modules\GeneralSetting\Repositories\Contracts\SettingsManagementInterface;
use Modules\GeneralSetting\Repositories\Contracts\SettingsRetrievalInterface;
use Modules\GeneralSetting\Repositories\Contracts\UserSecuritySettingsInterface;

abstract class GeneralSettingBaseController extends Controller
{
    protected SettingsManagementInterface $settingsManager;

    protected SettingsRetrievalInterface $settingsRetriever;

    protected UserSecuritySettingsInterface $userSecuritySettings;

    protected PaymentAndStorageSettingsInterface $paymentSettings;

    public function __construct(
        SettingsManagementInterface $settingsManager,
        SettingsRetrievalInterface $settingsRetriever,
        UserSecuritySettingsInterface $userSecuritySettings,
        PaymentAndStorageSettingsInterface $paymentSettings
    ) {
        $this->settingsManager = $settingsManager;
        $this->settingsRetriever = $settingsRetriever;
        $this->userSecuritySettings = $userSecuritySettings;
        $this->paymentSettings = $paymentSettings;
    }
}
