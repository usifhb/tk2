<?php

namespace Botble\Installer\Providers;

use Botble\Base\Supports\ServiceProvider;

/**
 * Installer package stub – installer UI removed. Store and dashboard work without it.
 */
class InstallerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // No routes, no middleware, no install wizard.
    }
}
