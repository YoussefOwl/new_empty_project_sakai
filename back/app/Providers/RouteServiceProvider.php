<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers';
    public function boot()
    {
        parent::boot();
    }
    public function map()
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
        $this->mapApiUserRoutes();
        $this->mapApiParametrageRoutes();
        $this->mapApiConfigurationRoutes();
        $this->mapApiDevisesRoutes();
    }
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
    protected function mapApiUserRoutes()
    {
        Route::prefix('api_user')
            ->middleware('api_user')
            ->namespace($this->namespace)
            ->group(base_path('routes/api_user.php'));
    }
    protected function mapApiParametrageRoutes()
    {
        Route::prefix('api_parametrage')
            ->middleware('api_parametrage')
            ->namespace($this->namespace)
            ->group(base_path('routes/api_parametrage.php'));
    }
    protected function mapApiConfigurationRoutes()
    {
        Route::prefix('api_configurations')
            ->middleware('api_configurations')
            ->namespace($this->namespace)
            ->group(base_path('routes/api_configurations.php'));
    }
    protected function mapApiDevisesRoutes()
    {
        Route::prefix('api_devise')
            ->middleware('api_devise')
            ->namespace($this->namespace)
            ->group(base_path('routes/api_devise.php'));
    }
}