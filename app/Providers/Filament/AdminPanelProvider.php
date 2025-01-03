<?php

namespace App\Providers\Filament;

use Awcodes\Overlook\OverlookPlugin;
use Awcodes\Overlook\Widgets\OverlookWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Tables\View\TablesRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use ReflectionClass;
use Illuminate\Support\Str;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->default()
            ->id('admin')
            ->path('/app')
            ->login()
            ->profile(isSimple: false)
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->databaseNotifications()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                OverlookWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->plugins([
                OverlookPlugin::make()
                    ->sort(2)
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'md' => 3,
                        'lg' => 4,
                        'xl' => 5,
                        '2xl' => null,
                    ]),
            ]);

        // $panelHooks = new ReflectionClass(PanelsRenderHook::class);
        // $tableHooks = new ReflectionClass(TablesRenderHook::class);
        // $widgetHooks = new ReflectionClass(Widgets\View\WidgetsRenderHook::class);

        // $panelHooks = $panelHooks->getConstants();
        // $tableHooks = $tableHooks->getConstants();
        // $widgetHooks = $widgetHooks->getConstants();

        // foreach ($panelHooks as $hook) {
        //     $panel->renderHook($hook, function () use ($hook) {
        //         return Blade::render('<div style="border: solid red 1px; padding: 2px;">{{ $name }}</div>', [
        //             'name' => Str::of($hook)->remove('tables::'),
        //         ]);
        //     });
        // }

        // foreach ($tableHooks as $hook) {
        //     $panel->renderHook($hook, function () use ($hook) {
        //         return Blade::render('<div style="border: solid red 1px; padding: 2px;">{{ $name }}</div>', [
        //             'name' => Str::of($hook)->remove('tables::'),
        //         ]);
        //     });
        // }

        // foreach ($widgetHooks as $hook) {
        //     $panel->renderHook($hook, function () use ($hook) {
        //         return Blade::render('<div style="border: solid red 1px; padding: 2px;">{{ $name }}</div>', [
        //             'name' => Str::of($hook)->remove('tables::'),
        //         ]);
        //     });
        // }

        return $panel;
    }
}
