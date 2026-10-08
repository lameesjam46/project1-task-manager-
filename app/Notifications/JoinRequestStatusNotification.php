<?php

namespace App\Notifications;

use App\Models\CompanyJoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JoinRequestStatusNotification extends Notification
{
    use Queueable;

    public $joinRequest;

    public function __construct(CompanyJoinRequest $joinRequest)
    {
        $this->joinRequest = $joinRequest;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'status' => $this->joinRequest->status,
            'company_id' => $this->joinRequest->company_id,
            'company_name' => $this->joinRequest->company->name,
            'response_note' => $this->joinRequest->response_note,
            'message' => $this->joinRequest->status === 'approved'
                ? 'تمت الموافقة على طلب انضمامك إلى الشركة'
                : 'تم رفض طلب انضمامك إلى الشركة',
        ];
    }
} 