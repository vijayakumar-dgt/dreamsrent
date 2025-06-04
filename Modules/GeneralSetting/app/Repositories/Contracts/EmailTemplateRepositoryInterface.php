<?php

namespace  Modules\GeneralSetting\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\GeneralSetting\Models\EmailTemplate;

interface EmailTemplateRepositoryInterface
{
    public function getAllNotificationTags(): Collection;

    public function getAllNotificationTypes(): Collection;

    public function getEmailTemplates(array $params): array;

    public function getEmailTemplateById(int $id): ?EmailTemplate;

    public function createOrUpdateEmailTemplate(array $data): EmailTemplate;

    public function deleteEmailTemplate(int $id): void;

    public function getTagsByNotificationType(int $notificationTypeId): array;
}
