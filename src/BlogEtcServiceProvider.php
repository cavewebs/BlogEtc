<?php

namespace WebDevEtc\BlogEtc;

use Illuminate\Support\ServiceProvider;
use Swis\LaravelFulltext\ModelObserver;
use WebDevEtc\BlogEtc\Models\BlogEtcPost;

class BlogEtcServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // if full text is not enabled, disable it:
        $this->disableFulltextSyncing();

        // include routes:
        $this->includeRoutes();

        // for vendor:publish
        $this->publishFiles();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // for the admin backend views ( view("blogetc_admin::BLADEFILE") )
        $this->loadViewsFrom(__DIR__ . '/Views/blogetc_admin', 'blogetc_admin');

        // for public facing views (view("blogetc::BLADEFILE")):
        // if you do the vendor:publish, these will be copied to /resources/views/vendor/blogetc anyway
        $this->loadViewsFrom(__DIR__ . '/Views/blogetc', 'blogetc');
    }

    /**
     * If full text search is not enabled in config, then disable syncing for the BlogEtcPost model
     * @return void
     */
    protected function disableFulltextSyncing(): void
    {
        if (config('blogetc.search.search_enabled')) {
            return;
        }
        // if search is disabled, don't allow it to sync full text.
        ModelObserver::disableSyncingFor(BlogEtcPost::class);
    }

    /**
     * If config is set to include default routes, load the routes file
     * @return void
     */
    protected function includeRoutes(): void
    {
        if (!config('blogetc.include_default_routes', true)) {
            return;
        }
        include __DIR__ . '/routes.php';
    }

    /**
     * Setup the files for vendor:publish
     * @return void
     */
    protected function publishFiles(): void
    {
        foreach ([
                     '2018_05_28_224023_create_blog_etc_posts_table.php',
                     '2018_09_16_224023_add_author_and_url_blog_etc_posts_table.php',
                     '2018_09_26_085711_add_short_desc_textrea_to_blog_etc.php',
                     '2018_09_27_122627_create_blog_etc_uploaded_photos_table.php',
                 ] as $migration) {
            $this->publishes([
                __DIR__ . '/../migrations/' . $migration => database_path('migrations/' . $migration),
            ]);
        }

        $this->publishes([
            __DIR__ . '/Views/blogetc' => base_path('resources/views/vendor/blogetc'),
            __DIR__ . '/Config/blogetc.php' => config_path('blogetc.php'),
            __DIR__ . '/css/blogetc_admin_css.css' => public_path('blogetc_admin_css.css'),
        ]);
    }
}
