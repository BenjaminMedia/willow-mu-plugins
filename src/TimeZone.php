<?php

namespace Bonnier\Willow\MuPlugins;

class TimeZone
{
    public function __construct()
    {
        add_filter('pre_option_timezone_string', [$this, 'setTimezone']);
    }

    public function setTimezone()
    {
        return 'Europe/Copenhagen';
    }
}
