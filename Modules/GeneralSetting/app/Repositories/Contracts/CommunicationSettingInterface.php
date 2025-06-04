<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface CommunicationSettingInterface
{
    public function smsGateway(): \Illuminate\View\View;
    public function emailSettings(): \Illuminate\View\View;
    public function statusUpdate(array $data): array;
    public function smsList(array $filters): array;
    public function storeCommunicationSetting(array $data): array;
    public function sendTestMail(array $data): array;
}
