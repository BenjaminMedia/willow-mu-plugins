<?php

namespace Bonnier\Willow\MuPlugins\Tests\Models;

use Bonnier\Willow\MuPlugins\Helpers\AbstractSettingsPage;

class SettingsPage extends AbstractSettingsPage
{
    public function __construct($properties = [], $settingsFields = [])
    {
        foreach ($properties as $key => $value) {
            $this->{$key} = $value;
        }
        $this->settingsFields = $settingsFields;
        parent::__construct();
    }

    public function getSettingsFields()
    {
        return $this->settingsFields;
    }

    protected function getSelectFieldOptions($field)
    {
        return [
            [
            'value' => 1,
            'label' => 'Test Label 1'
            ],
            [
                'value' => 2,
                'label' => 'Test Label 2'
            ],
            [
                'value' => 3,
                'label' => 'Test Label 3'
            ],
        ];
    }
}
