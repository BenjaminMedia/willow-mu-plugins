<?php

namespace Bonnier\Willow\MuPlugins;

class DefaultPlugins
{
    private $plugins = [
        'advanced-custom-fields-pro/acf.php',
        'amazon-s3-and-cloudfront/wordpress-s3.php',
        'contenthub-editor/contenthub-editor.php',
        'polylang-pro/polylang.php',
        'wp-bonnier-redirect/wp-bonnier-redirect.php',
        'wp-site-manager/wp-site-manager.php',
        'wp-cxense/wp-cxense.php',
    ];

    public function __construct()
    {
        if (env('WP_ENV') !== 'testing') {
            add_action('option_active_plugins', [$this, 'activatePlugin']);
        }
    }

    public function activatePlugin($activePlugins)
    {
        $warnings = collect([]);
        collect($this->plugins)->each(function ($plugin) use (&$activePlugins, &$warnings) {
            if (file_exists(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin)) {
                if(!collect($activePlugins)->contains($plugin)) {
                    // First time activation of plugin, so we must run activate_plugin to ensure db migration i run
                    add_action('admin_init', function() use($plugin){
                        activate_plugin($plugin);
                    });
                } else {
                    // Plugin has been enabled before continue to enforce enable
                    array_push($activePlugins, $plugin);
                }
            } else {
                $warnings->push(sprintf('<p>The plugin \'%s\' was not found!</p>', $plugin));
            }
        });

        if ($warnings->isNotEmpty()) {
            add_action('admin_notices', function () use ($warnings) {
                echo sprintf(
                    '<div class="error notice">%s</div>',
                    $warnings->implode('')
                );
            });
        }

        return array_unique($activePlugins);
    }
}
