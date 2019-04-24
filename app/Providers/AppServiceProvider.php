<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;

//Paginate on collections: https://gist.github.com/simonhamp/549e8821946e2c40a617c85d2cf5af5e
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Blade::if('admin', function () {
            return auth()->check() && auth()->user()->isAdmin();
        });

        /**
         * Paginate a standard Laravel Collection.
         *
         * @param int $perPage
         * @param int $total
         * @param int $page
         * @param string $pageName
         * @return array
         */
        Collection::macro('paginate', function($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });

        Collection::macro('simple_paginate', function (int $perPage = 15, string $pageName = 'page', int $page = null, int $total = null, array $options = []): Paginator {
            $page = $page ?: Paginator::resolveCurrentPage($pageName);
    
            $results = $this->slice(($page - 1) * $perPage)->take($perPage + 1);
    
            $options += [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ];
    
            return new Paginator($results, $perPage, $page, $options);
        });

        Collection::macro('link_function', function ( $view = null, $data = [] ) {
            return $this->simple_paginate()->links( $view, $data );
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
