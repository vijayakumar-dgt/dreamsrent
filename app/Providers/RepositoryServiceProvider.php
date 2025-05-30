<?php

namespace App\Providers;

use App\Repositories\Contracts\HomeRepositoryInterface;
use App\Repositories\Contracts\NewsLetterRepositoryInterface;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\HomeRepository;
use App\Repositories\Eloquent\NewsLetterRepository;
use App\Repositories\Eloquent\ReviewRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void 
    {
        $this->registerBindings();
    }

    protected function registerBindings(): void
    {
        $this->app->bind(HomeRepositoryInterface::class, HomeRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);
        $this->app->bind(NewsLetterRepositoryInterface::class, NewsLetterRepository::class);
    }
}