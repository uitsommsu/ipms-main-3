<?php

namespace App\Providers;

use App\Models\Patent;
use App\Observers\PatentObserver;
use Illuminate\Support\ServiceProvider;
use App\Filament\Pages\Auth\RegistrationResponse;
use App\Models\AssistanceForm;
use App\Models\UtilityModel;
use App\Observers\AssistanceFormObserver;
use App\Observers\UtilityModelObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            RegistrationResponse::class,
            \App\Filament\Pages\Auth\RegistrationResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Patent::observe(PatentObserver::class);
        UtilityModel::observe(UtilityModelObserver::class);
        AssistanceForm::observe(AssistanceFormObserver::class);
    }
}
