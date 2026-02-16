<?php

namespace Botble\Installer\Providers;

use Botble\Base\Events\FinishedSeederEvent;
use Botble\Base\Events\UpdatedEvent;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

class InstallerServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('packages/installer')
            ->loadHelpers()
            ->loadAndPublishConfigurations('installer')
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->publishAssets();

        // Installer disabled: no redirect to install page, /install redirects to home
        $this->registerInstallRedirectRoutes();

        $this->app['events']->listen([UpdatedEvent::class, FinishedSeederEvent::class], function () {
            if (defined('INSTALLED_SESSION_NAME')) {
                BaseHelper::saveFileData(storage_path(INSTALLED_SESSION_NAME), Carbon::now()->toDateTimeString());
            }
        });
    }

    protected function registerInstallRedirectRoutes(): void
    {
        Route::middleware('web')->group(function () {
            Route::any('install', fn () => redirect()->to('/'))->name('installers.welcome');
            Route::any('install/{path}', fn () => redirect()->to('/'))->where('path', '.*');
        });
    }
}
