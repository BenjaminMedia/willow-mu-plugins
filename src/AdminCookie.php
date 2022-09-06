<?php

namespace Bonnier\Willow\MuPlugins;

class AdminCookie
{
    public function __construct()
    {
        $topLevelDomain = getenv('WP_HOME');
        if (preg_match('/([a-z0-9-]+\.[a-z]+$)/', parse_url($topLevelDomain, PHP_URL_HOST), $matches)) {
            $topLevelDomain = $matches[1];
        }
        if (!defined('COOKIE_DOMAIN')) {
            define('COOKIE_DOMAIN', $topLevelDomain);
        }
    }
}
