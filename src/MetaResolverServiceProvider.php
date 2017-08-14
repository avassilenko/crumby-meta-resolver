<?php
namespace Crumby\MetaResolver;

use Illuminate\Support\ServiceProvider;

class MetaResolverServiceProvider extends ServiceProvider
{
    const META_RESOLVER_VAR_NAME = 'MetaResolver';
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(self::META_RESOLVER_VAR_NAME, function ($app) {
            $resolver = new MetaResolver();

            \View::share(self::META_RESOLVER_VAR_NAME, $resolver);
            return $resolver;
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/crumby-crumbs/meta-resolver.php' => config_path('crumby-crumbs/meta-resolver.php')
            ], 'config');
        }
        
        $this->app->alias(self::META_RESOLVER_VAR_NAME, 'Crumby\MetaResolver\MetaResolver');
        \MetaResolver::loadConfig();
    }
}
