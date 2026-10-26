<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->autoDetectDatabasePort();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || env('APP_ENV') === 'production' || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }

    /**
     * Automatically detect if MySQL is running on port 3306 or 3307 on localhost.
     * Prevents connection failures when moving between environments (e.g. laptop 3307 vs Laragon 3306).
     */
    protected function autoDetectDatabasePort(): void
    {
        if (config('database.default') !== 'mysql') {
            return;
        }

        $host = config('database.connections.mysql.host', '127.0.0.1');
        if (!in_array($host, ['127.0.0.1', 'localhost'])) {
            return;
        }

        $configuredPort = (int) config('database.connections.mysql.port', 3307);

        // Check configured port first (fast socket ping: max 50ms)
        $fp = @fsockopen($host, $configuredPort, $errno, $errstr, 0.05);
        if ($fp) {
            fclose($fp);
            return;
        }

        // If configured port is not responding, probe alternative local ports (3306, 3307)
        $candidatePorts = ($configuredPort === 3307) ? [3306, 3307] : [3307, 3306];
        foreach ($candidatePorts as $candidate) {
            $altFp = @fsockopen($host, $candidate, $errno, $errstr, 0.05);
            if ($altFp) {
                fclose($altFp);
                config(['database.connections.mysql.port' => (string) $candidate]);
                return;
            }
        }
    }
}
