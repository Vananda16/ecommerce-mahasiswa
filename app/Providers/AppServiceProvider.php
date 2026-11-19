<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\CartItem;
use App\Models\Message;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // @rupiah($angka) => Rp 1.250.000
        Blade::directive('rupiah', function (string $expression) {
            return "<?php echo 'Rp '.number_format((float) ({$expression}), 0, ',', '.'); ?>";
        });

        // Data yang dibutuhkan navbar & banner pengumuman di layout utama.
        View::composer('layouts.app', function ($view) {
            $user = auth()->user();

            $view->with([
                'navAnnouncements' => ($user && $user->isAdmin())
                    ? collect()
                    : Announcement::active()->latest()->take(3)->get(),
                'cartCount' => ($user && $user->isBuyer())
                    ? CartItem::where('user_id', $user->id)->count()
                    : 0,
                'unreadChat' => ($user && ! $user->isAdmin())
                    ? Message::where('receiver_id', $user->id)->whereNull('read_at')->count()
                    : 0,
            ]);
        });
    }
}
