<?php

namespace App\Filament\Personal\Resources\HolidayResource\Pages;

use App\Filament\Personal\Resources\HolidayResource;
use App\Mail\HolidayPending;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;

class CreateHoliday extends CreateRecord
{
    protected static string $resource = HolidayResource::class;

    //valores predeterminados para guardar, en el panel personal no se puede editar o guardar estos datos
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::user()->id;
        $data['type'] = 'pending';

        //permite buscar al administrador y enviar un correo de HolidayPending
        $userAdmin = User::find(1);
        $dataToSend = [
            'day' => $data['day'],
            'name' => User::find($data['user_id'])->name,
            'email' => User::find($data['user_id'])->email,
        ];
        Mail::to($userAdmin)->send(new HolidayPending($dataToSend));

        $recipient = auth()->user();

        Notification::make()
            ->title('Solicitud de vacaciones')
            ->body("El día " . $data['day'] . ' esta pendiente de aprobar')
            ->sendToDatabase($recipient);

        return $data;
    }
}
