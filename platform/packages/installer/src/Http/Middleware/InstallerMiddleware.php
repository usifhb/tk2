<?php

namespace Botble\Installer\Http\Middleware;

abstract class InstallerMiddleware
{
    public function alreadyInstalled(): bool
    {
        // Installer disabled: always consider app installed (no install wizard).
        return true;
    }
}
