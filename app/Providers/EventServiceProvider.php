<?php

namespace App\Providers;

use App\Events\ShopCategoryOnDelete;
use App\Listeners\ConvertImages;
use Illuminate\Auth\Events\Registered;
use App\Listeners\ClearShopCategoryFiles;
use Alexusmai\LaravelFileManager\Events\FilesUploaded;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ShopCategoryOnDelete::class => [
            ClearShopCategoryFiles::class,
        ],
        FilesUploaded::class => [
            ConvertImages::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
