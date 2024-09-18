<?php

namespace App\Filament\Personal\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PersonaWidgetStats extends BaseWidget
{
    protected function getStats(): array
    {

        return [
            //
            Stat::make('Pending Holidays', $this->getPendingHolidays(Auth::user())),
            Stat::make('Approved Holidays', $this->getApprovedHolidays(Auth::user())),
            Stat::make('Total Work', $this->getTotalWork(Auth::user())),
        ];
    }

    protected function getPendingHolidays($user)
    {
        $totalPendingHolidays = Holiday::where('user_id', $user->id)
            ->where('type', 'pending')->get()->count();
        return $totalPendingHolidays;
    }

    protected function getApprovedHolidays($user)
    {
        $totalApprovedHolidays = Holiday::where('user_id', $user->id)
            ->where('type', 'approved')->get()->count();
        return $totalApprovedHolidays;
    }

    protected function getTotalWork(User $user)
    {
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type', 'work')->get();
        $sumSeconds = 0;
        foreach ($timesheets as $timesheet) {
            $starTime = Carbon::parse($timesheet->day_in);
            $finishTime = Carbon::parse($timesheet->day_out);

            $totalDuration = $starTime->diffInSeconds($finishTime);
            
            $sumSeconds = $sumSeconds + $totalDuration;
            
        }
        $tiempoCarbon = gmdate('H:i:s',$sumSeconds);
        return $tiempoCarbon;
    }
}
