<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;

use Filament\Management\Widgets\PatentStats;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use App\Http\Middleware\VerifyIsManagement;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class ManagementPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('management')
            ->path('management')
            ->userMenuItems([
                MenuItem::make()
                    ->label('Dashboard')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url('/dashboard')

            ])
            ->colors([
                'primary' => Color::Emerald,
            ])
            //->brandName('IPMS - Management')
            ->brandLogo(asset('images/ipms.png'))
            ->brandLogoHeight('4rem')
            ->favicon(asset('images/ipms.jpg'))
            ->discoverResources(in: app_path('Filament/Management/Resources'), for: 'App\\Filament\\Management\\Resources')
            ->discoverPages(in: app_path('Filament/Management/Pages'), for: 'App\\Filament\\Management\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Management/Widgets'), for: 'App\\Filament\\Management\\Widgets')
            ->widgets([
                //PatentStats::class,
                // Widgets\FilamentInfoWidget::class,
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
                VerifyIsManagement::class
            ])
            ->unsavedChangesAlerts()
            ->databaseNotifications()
            ->databaseNotificationsPolling('60s')
            ->spa();
    }
}
