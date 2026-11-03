<?php

namespace App\Notifications;

use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Models\Enquiry;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EnquiryReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Enquiry $enquiry) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('New quote request from '.$this->enquiry->name)
            ->body($this->enquiry->phone.' · '.$this->enquiry->job_type->getLabel())
            ->actions([
                Action::make('view')
                    ->label('Open enquiry')
                    ->url(EnquiryResource::getUrl('view', ['record' => $this->enquiry], panel: 'admin')),
            ])
            ->getDatabaseMessage();
    }
}
