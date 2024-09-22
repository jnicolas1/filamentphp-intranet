<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use BezhanSalleh\PanelSwitch\PanelSwitch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //aqui para configurar la vista de los panel Switch
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            //aqui se configura para los permisos por roles de admin, general_manager, super_admin
            $panelSwitch
                ->slideOver()
                ->visible(fn(): bool => auth()->user()?->hasAnyRole([
                    'admin',
                    'general_manager',
                    'super_admin',
                ]));
        });
    }
}
