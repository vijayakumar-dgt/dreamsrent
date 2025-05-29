<?php

namespace Modules\GeneralSetting\Repositories;

use Modules\GeneralSetting\Models\EmailTemplate;
use Modules\GeneralSetting\Models\NotificationTag;
use Modules\GeneralSetting\Models\NotificationType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EmailTemplateSettingRepository
{
    public function getAllNotificationTags(): Collection
    {
        return NotificationTag::where('status', true)->get();
    }

    public function getAllNotificationTypes(): Collection
    {
        return NotificationType::where('status', true)->get();
    }

    public function getEmailTemplates(array $params): array
    {
        $query = EmailTemplate::query();
        
        if (!empty($params['keyword'])) {
            $query->where('title', 'like', '%' . $params['keyword'] . '%');
        }

        $totalRecords = $filteredRecords = $query->count();
        
        $emailTemplates = $query->orderBy('id', 'desc')
            ->skip($params['start'] ?? 0)
            ->take($params['length'] ?? 10)
            ->get()
            ->map(function ($emailTemplate) {
                return [
                    'id' => $emailTemplate->id,
                    'title' => $emailTemplate->title,
                    'notification_type' => $emailTemplate->notification_type,
                    'subject' => $emailTemplate->subject,
                    'sms_content' => $emailTemplate->sms_content,
                    'notification_content' => $emailTemplate->notification_content,
                    'description' => $emailTemplate->description,
                    'status' => $emailTemplate->status,
                    'formated_date' => formatDateTime($emailTemplate->created_at)
                ];
            });

        return [
            'data' => $emailTemplates,
            'totalRecords' => $totalRecords,
            'filteredRecords' => $filteredRecords
        ];
    }

    public function getEmailTemplateById(int $id): ?EmailTemplate
    {
        return EmailTemplate::find($id);
    }

    public function createOrUpdateEmailTemplate(array $data): EmailTemplate
    {
        if (!empty($data['id'])) {
            $emailTemplate = EmailTemplate::findOrFail($data['id']);
            $emailTemplate->status = !empty($data['status']) && $data['status'] === 'on' ? 1 : 0;
        } else {
            $emailTemplate = new EmailTemplate();
            $emailTemplate->status = 1;
        }

        $emailTemplate->title = $data['title'];
        $emailTemplate->notification_type = $data['notification_type'];
        $emailTemplate->subject = $data['subject'];
        $emailTemplate->sms_content = $data['sms_content'];
        $emailTemplate->notification_content = $data['notification_content'] ?? null;
        $emailTemplate->description = $data['description'];
        $emailTemplate->save();

        return $emailTemplate;
    }

    public function deleteEmailTemplate(int $id): void
    {
        EmailTemplate::findOrFail($id)->delete();
    }

    public function getTagsByNotificationType(int $notificationTypeId): array
    {
        $notificationType = NotificationType::find($notificationTypeId);
        $defaultTags = NotificationTag::where('status', true)->pluck('title')->toArray();
        
        return [
            'notification_type' => $notificationType,
            'tags' => $notificationType && $notificationType->tags 
                ? json_decode($notificationType->tags) 
                : $defaultTags
        ];
    }
}