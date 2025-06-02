<?php

namespace App\Providers;

use App\Repositories\Contracts\HomeRepositoryInterface;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Contracts\BlogRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\HomeRepository;
use App\Repositories\Eloquent\DashboardRepository;
use App\Repositories\Eloquent\InvoiceRepository;
use App\Repositories\Contracts\NewsLetterRepositoryInterface;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Repositories\Contracts\UserLoginRegisterInterface;
use App\Repositories\Eloquent\NewsLetterRepository;
use App\Repositories\Eloquent\ReviewRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\BlogRepository;
use App\Repositories\Eloquent\UserLoginRegisterRepository;
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
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(BlogRepositoryInterface::class, BlogRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);
        $this->app->bind(NewsLetterRepositoryInterface::class, NewsLetterRepository::class);
        $this->app->bind(UserLoginRegisterInterface::class, UserLoginRegisterRepository::class);
    }
}