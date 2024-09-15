<?php

namespace App\Filament\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEmployees = User::all()->count();
        $totalHolidays = Holiday::where('type', 'pending')->count();
        $totalTimesheet = Timesheet::all()->count();
        return [
            //aqui añadi widget para el home o dashboard 
            Stat::make('Employees', $totalEmployees),
            Stat::make('Pending Holidays', $totalHolidays),
            Stat::make('Timesheet', $totalTimesheet),

        ];
    }
}
