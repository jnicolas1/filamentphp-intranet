<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;



    protected function getHeaderActions(): array
    {
        $lastTimesheet = Timesheet::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
        if ($lastTimesheet == null) {
            return [
                Action::make('inWork') //indice unico
                    ->label('Entrar a trabajar') //muestra el texto que ira al boton
                    ->color('success') //color verde del boton
                    ->requiresConfirmation() //muestra una pantalla alert para confirmar si acepta o cancela
                    ->keyBindings(['command+s', 'ctrl+s']) //sirve para poder añadir algun comando y se active el boton
                    ->action(function () {
                        // code to be executed when the button is clicked
                        $user = Auth::user();
                        $timesheet = new Timesheet();
                        $timesheet->calendar_id = 1;
                        $timesheet->user_id = $user->id;
                        $timesheet->type = 'work';
                        $timesheet->day_in = Carbon::now();
                        $timesheet->save();
                    }),
                Actions\CreateAction::make(),
            ];
        }
        return [
            Action::make('inWork') //indice unico
                ->label('Entrar a trabajar') //muestra el texto que ira al boton
                ->color('success') //color verde del boton

                ->visible(!$lastTimesheet->day_out == null) //cuando day_out no es nulo sera invisible
                ->disabled($lastTimesheet->day_out == null) //cuando day_out es nulo sera deshabilitado
                ->requiresConfirmation() //muestra una pantalla alert para confirmar si acepta o cancela
                ->keyBindings(['command+1', 'ctrl+1']) //sirve para poder añadir algun comando y se active el boton
                ->action(function () {
                    // code to be executed when the button is clicked
                    $user = Auth::user();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = $user->id;
                    $timesheet->type = 'work';
                    $timesheet->day_in = Carbon::now();
                    $timesheet->save();

                    Notification::make()
                        ->title('Has entrado a trabajar')
                        ->success()
                        ->color('success')
                        ->send();
                }),
            Action::make('stopWork') //indice unico
                ->label('Parar de trabajar') //muestra el texto que ira al boton
                ->color('success') //color verde del boton
                ->keyBindings(['command+o', 'ctrl+o']) //sirve para poder añadir algun comando y se active el boton

                ->requiresConfirmation() //muestra una pantalla alert para confirmar si acepta o cancela
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type != 'pause') //cuando day_out es nulo y no es de tipo pause sera invisible
                ->disabled(!$lastTimesheet->day_out == null) //cuando day_out esta disponible sera deshabilitado
                ->action(function () use ($lastTimesheet) {
                    $lastTimesheet->day_out = Carbon::now(); //edita para que la fecha final sea caundo presione en pausa
                    $lastTimesheet->save(); //graba el registro editado
                    Notification::make()
                        ->title('Has parado de trabajar')
                        ->body('Has comenzado a trabajar a las:' . Carbon::now())
                        ->success()
                        ->send();
                }),

            Action::make('inPause')
                ->label('Comenzar Pausa')
                ->color('info')

                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type != 'pause') //cuando day_out es nulo y no es de tipo pause sera invisible
                ->disabled(!$lastTimesheet->day_out == null) //cuando day_out esta disponible sera deshabilitado

                ->requiresConfirmation()
                ->keyBindings(['command+u', 'ctrl+u']) //sirve para poder añadir algun comando y se active el boton
                ->action(function () use ($lastTimesheet) { //usa la variable que creamos arriba
                    $lastTimesheet->day_out = Carbon::now(); //edita para que la fecha final sea caundo presione en pausa
                    $lastTimesheet->save(); //graba el registro editado

                    $timesheet = new Timesheet(); //nuevo registro de estado en pausa
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->type = 'pause';
                    $timesheet->day_in = Carbon::now();
                    $timesheet->save();

                    Notification::make()
                    ->title('Comienzas tu pausa')
                    ->color('info')
                    ->info()
                    ->send();
                }),
            Action::make('stopPause')
                ->label('Parar Pausa')
                ->color('info')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type == 'pause') //cuando day_out es nulo y de tipo pause sera invisible
                ->disabled(!$lastTimesheet->day_out == null) //cuando day_out esta disponible sera deshabilitado
                ->requiresConfirmation()
                ->action(function () use ($lastTimesheet) { //usa la variable que creamos arriba
                    $lastTimesheet->day_out = Carbon::now(); //edita para que la fecha final sea caundo presione en pausa
                    $lastTimesheet->save(); //graba el registro editado

                    $timesheet = new Timesheet(); //nuevo registro de estado en pausa
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->type = 'work';
                    $timesheet->day_in = Carbon::now();
                    $timesheet->save();

                    Notification::make()
                    ->title('Comienzas de nuevo a trabajar')
                    ->color('info')
                    ->info()
                    ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
