<?php
/*
Plugin Name: Willow Must Use Plugins
Plugin URI: https://github.com/BenjaminMedia/willow-mu-plugins
Description: A collection of Must Use WordPress Plugins for Willow
Author: Bonnier Publications
Version: 1.2.1
*/

new \Bonnier\Willow\MuPlugins\AdminCookie();
new \Bonnier\Willow\MuPlugins\OffloadS3();
new \Bonnier\Willow\MuPlugins\RemoveCategorySlug();
new \Bonnier\Willow\MuPlugins\TimeZone();

add_action('muplugins_loaded', [\Bonnier\Willow\MuPlugins\Helpers\LanguageProvider::class, 'registerSubdomain'], 1000);
