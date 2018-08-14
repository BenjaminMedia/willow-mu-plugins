<?php

namespace Bonnier\Willow\MuPlugins;

class AdminCookie
{
    public function __construct()
    {
        $topLevelDomain = env('WP_HOME');
        if (preg_match('/([a-z0-9-]+\.[a-z]+$)/', parse_url($topLevelDomain, PHP_URL_HOST), $matches)) {
            $topLevelDomain = $matches[1];
        }
        define('COOKIE_DOMAIN', $topLevelDomain);
    }
}