<?php

namespace App\Providers;

use App\Contracts\Interfaces\CategoryInterface;
use App\Contracts\Interfaces\InstrumentInterface;
use App\Contracts\Interfaces\RentalDetailInterface;
use App\Contracts\Interfaces\RentalInterface;
use App\Contracts\Interfaces\UserInterface;
use App\Contracts\Repositories\CategoryRepository;
use App\Contracts\Repositories\InstrumentRepository;
use App\Contracts\Repositories\RentalDetailRepository;
use App\Contracts\Repositories\RentalRepository;
use App\Contracts\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    private array $register = [
        UserInterface::class => UserRepository::class,
        CategoryInterface::class => CategoryRepository::class,
        InstrumentInterface::class => InstrumentRepository::class,
        RentalInterface::class => RentalRepository::class,
        RentalDetailInterface::class => RentalDetailRepository::class,
    ];
    public function register(): void
    {
        foreach ($this->register as $index => $value) $this->app->bind($index, $value);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
