<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;



    protected function getHeaderActions(): array
    {
        return [
            Action::make('inWork')//indice unico
                ->label('Entrar a trabajar')//muestra el texto que ira al boton
                ->color('success')//color verde del boton
                ->requiresConfirmation()//muestra una pantalla alert para confirmar si acepta o cancela
                ->keyBindings(['command+s', 'ctrl+s'])//sirve para poder añadir algun comando y se active el boton
                ->action(function () {
                    // code to be executed when the button is clicked
                    $user = Auth::user();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = $user->id;
                    $timesheet->type = 'work';
                    $timesheet->day_in = Carbon::now();
                    $timesheet->day_out = Carbon::now();
                    $timesheet->save();

                })
                ,
            Action::make('inPause')
                ->label('Comenzar Pausa')
                ->color('info')
                ->requiresConfirmation(),
            Actions\CreateAction::make(),
        ];
    }
}
