<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PersonalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('personal')
            ->path('personal')//http://127.0.0.1:8000/personal
            ->login()
            ->default()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Personal/Resources'), for: 'App\\Filament\\Personal\\Resources')
            ->discoverPages(in: app_path('Filament/Personal/Pages'), for: 'App\\Filament\\Personal\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Personal/Widgets'), for: 'App\\Filament\\Personal\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->databaseNotifications()
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarFullyCollapsibleOnDesktop()//sirve para añadir un icono para minimizar el slide izquierdo
            //plugin para usar shield
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
            ])
            ->navigationItems([
                NavigationItem::make('Repositorio Github')
                    ->url('https://github.com/jnicolas1/filamentphp-intranet', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-heart')
                    ->sort(3)
            ])
            ->topNavigation()//permite poner la barra lateral que sea ahora el top
            //aqui creamos un nuevo item al lado derecho  y es solo visible para administrador
            ->userMenuItems([
                MenuItem::make()
                    ->label('Admin')
                    ->url('admin')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->visible(fn(): bool => auth()->user()?->hasAnyRole([
                        'admin',
                        'general_manager',
                        'super_admin',
                    ]))
            ])
            ;
    }
}
