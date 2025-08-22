<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

use Illuminate\Http\Request;

interface CommunicationSettingInterface
{
    public function smsGateway(): \Illuminate\View\View;

    public function emailSettings(): \Illuminate\View\View;

    public function statusUpdate(array $data): array;

    public function smsList(array $filters): array;

    public function storeCommunicationSetting(array $data): array;

    public function sendTestMail(Request $request): array;
}
