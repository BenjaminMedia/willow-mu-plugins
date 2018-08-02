<?php

namespace Tests;

use Bonnier\Willow\MuPlugins\Helpers\AbstractSettingsPage;
use Codeception\TestCase\WPTestCase;
use Tests\Models\SettingsPage;

class SettingsPageTest extends WPTestCase
{
    protected $settingsKey = 'Test Key';
    protected $settingsGroup = 'Test Group';
    protected $settingsPage = 'Test Page';
    protected $settingsSection = 'Test Section';
    protected $noticePrefix = 'Test Notice Prefix';
    protected $toolbarName = 'Test Toolbar Name';
    protected $site = 'Test Site';
    protected $title = 'Test Title';

    protected $constructorArgs;
    protected $settingsFields;

    /** @var SettingsPage */
    protected $model;

    private $textKey = 'test_setting_text';
    private $checkboxKey = 'test_setting_checkbox';
    private $selectKey = 'test_setting_select';

    public function setUp()
    {
        parent::setUp();
        $this->constructorArgs = [
            'settingsKey' => $this->settingsKey,
            'settingsGroup' => $this->settingsGroup,
            'settingsFields' => $this->settingsFields,
            'settingsPage' => $this->settingsPage,
            'settingsSection' => $this->settingsSection,
            'noticePrefix' => $this->noticePrefix,
            'toolbarName' => $this->toolbarName,
            'site' => $this->site,
            'title' => $this->title,
        ];
        $this->settingsFields = [
            $this->textKey => [
                'type' => 'text',
                'name' => 'Test Setting Text',
            ],
            $this->checkboxKey => [
                'type' => 'checkbox',
                'name' => 'Test Setting Checkbox'
            ],
            $this->selectKey => [
                'type' => 'select',
                'name' => 'Test Setting Select'
            ]
        ];

        $this->model = new SettingsPage($this->constructorArgs, $this->settingsFields);
    }

    public function testThrowsErrorOnMissingProperties()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageRegExp('/Settings Page is missing required properties:.+$/');

        new SettingsPage([], $this->settingsFields);
    }

    public function testThrowsErrorOnMissingSettingsFields()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Settings Page is missing required settings fields!');

        new SettingsPage($this->constructorArgs);
    }

    public function testCanBeConstructedWithoutExceptions()
    {
        $settingsPage = new SettingsPage($this->constructorArgs, $this->settingsFields);

        $this->assertInstanceOf(AbstractSettingsPage::class, $settingsPage);
    }

    public function testCanPrintError()
    {
        $error = 'TEST ERROR!';

        $expectedString = sprintf(
            '<div class="error settings-error notice is-dismissible"><strong>%s</strong><p>%s</p></div>',
            $this->noticePrefix,
            $error
        );

        $this->expectOutputString($expectedString);

        $this->model->printError($error);
    }

    public function testCanCreateAdminPage()
    {
        $expectedStringRegex = '#<div class="wrap"><form method="post" action="(.*options\.php)">.+?</form></div>#';
        $this->expectOutputRegex($expectedStringRegex);

        $this->model->createAdminPage();
    }

    public function testCanPrintSectionInfo()
    {
        $this->expectOutputString('Enter your settings below:');

        $this->model->printSectionInfo();
    }

    public function testCanPrintTextInput()
    {
        $name = sprintf(
            '%s[%s]',
            $this->settingsKey,
            $this->textKey
        );
        $expectedString = sprintf(
            '<input type="text" name="%s" value="" class="regular-text" />',
            $name
        );
        $this->expectOutputString($expectedString);

        $this->model->{$this->textKey}();
    }

    public function testCanPrintCheckboxInput()
    {
        $name = sprintf(
            '%s[%s]',
            $this->settingsKey,
            $this->checkboxKey
        );
        $expectedString = sprintf(
            '<input type="hidden" value="0" name="%s"><input type="checkbox" value="1" name="%s" >',
            $name,
            $name
        );

        $this->expectOutputString($expectedString);

        $this->model->{$this->checkboxKey}();
    }

    public function testCanPrintSelectInput()
    {
        $fieldName = sprintf(
            '%s[%s]',
            $this->settingsKey,
            $this->selectKey
        );

        $options = collect([
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
            ])
            ->map(function ($option) {
                return sprintf('<option value="%s" >%s</option>', $option['value'], $option['label']);
            })
            ->implode('');

        $expectedString = sprintf(
            '<select name="%s">%s</select>',
            $fieldName,
            $options
        );

        $this->expectOutputString($expectedString);

        $this->model->{$this->selectKey}();
    }

    public function testCanSanitizeInput()
    {
        $goodInput = [
            'test_setting_text' => 'Test Value',
            'test_setting_checkbox' => '1'
        ];
        $sanitized = $this->model->sanitize($goodInput);

        $this->assertEquals([
            'test_setting_text' => 'Test Value',
            'test_setting_checkbox' => 1
        ], $sanitized);

        $badInput = [
            'test_setting_text' => '<script type="text/javascript">alert("XSS Attack!");</script>',
            'test_setting_checkbox' => 'not an integer',
            'this_value' => 'is not a defined setting!'
        ];
        $sanitized = $this->model->sanitize($badInput);

        $this->assertEquals([
            'test_setting_text' => '',
            'test_setting_checkbox' => 0
        ], $sanitized);

        $this->assertArrayNotHasKey('this_value', $sanitized);
    }

    public function testCanRegisterSettingsPage()
    {
        $this->model->registerSettings();

        $this->assertEquals($this->settingsFields, $this->model->getSettingsFields());
    }
}
