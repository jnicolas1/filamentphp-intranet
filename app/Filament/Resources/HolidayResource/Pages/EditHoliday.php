<?php

namespace App\Filament\Resources\HolidayResource\Pages;

use App\Filament\Resources\HolidayResource;
use App\Mail\HolidayApproved;
use App\Mail\HolidayDecline;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class EditHoliday extends EditRecord
{
    protected static string $resource = HolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        $user = User::find($record->user_id);

        $recipient = $user;

        //SEND EMAIL SOLO SI ES APROBADO
        if ($record->type == 'approved') {
            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'day' => $record->day,
            ];
            Mail::to($user)->send(new HolidayApproved($data));

            Notification::make()
                ->title('Solicitud de vacaciones')
                ->body("El día " . $data['day'] . ' esta aprobado')
                ->sendToDatabase($recipient);
        } else if ($record->type == 'decline') {
            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'day' => $record->day,
            ];
            Mail::to($user)->send(new HolidayDecline($data));


            Notification::make()
                ->title('Solicitud de vacaciones')
                ->body("El día " . $data['day'] . ' esta rechazada')
                ->sendToDatabase($recipient);
        }
        return $record;
    }
}
