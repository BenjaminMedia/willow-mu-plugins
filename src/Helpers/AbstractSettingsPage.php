<?php

namespace Bonnier\Willow\MuPlugins\Helpers;

abstract class AbstractSettingsPage
{
    protected $settingsKey;
    protected $settingsGroup;
    protected $settingsSection;
    protected $settingsPage;
    protected $noticePrefix;
    protected $toolbarName;
    protected $site;
    protected $title;

    /** @var array */
    protected $settingsFields = [];

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $settingsValues;

    /**
     * Start up
     */
    public function __construct()
    {
        $missing = collect([
            'settingsKey',
            'settingsGroup',
            'settingsSection',
            'settingsPage',
            'noticePrefix',
            'toolbarName',
            'site',
            'title'
        ])->reject(function ($requiredProperty) {
            return !is_null($this->{$requiredProperty});
        });
        if ($missing->isNotEmpty()) {
            throw new \RuntimeException(sprintf(
                'Settings Page is missing required properties: %s',
                $missing->implode(', ')
            ));
        }
        if (empty($this->settingsFields)) {
            throw new \RuntimeException('Settings Page is missing required settings fields!');
        }
    }

    public function printError($error)
    {
        echo '<div class="error settings-error notice is-dismissible">';
        echo sprintf('<strong>%s</strong><p>%s</p>', $this->noticePrefix, $error);
        echo '</div>';
    }

    /**
     * Add options page
     */
    public function addPluginPage()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            $this->toolbarName,
            'manage_options',
            $this->settingsPage,
            [$this, 'createAdminPage']
        );
    }

    /**
     * Options page callback
     */
    public function createAdminPage()
    {
        // Set class property
        echo '<div class="wrap">';
        echo sprintf('<form method="post" action="%s">', get_admin_url(null, 'options.php'));
        // This prints out all hidden setting fields
        settings_fields($this->settingsGroup);
        do_settings_sections($this->settingsPage);
        submit_button();
        echo '</form>';
        echo '</div>';
    }

    /**
     * Register and add settings
     */
    public function registerSettings()
    {
        if ($this->languagesIsEnabled()) {
            $this->enableLanguageFields();
        }

        register_setting(
            $this->settingsGroup, // Option group
            $this->settingsKey, // Option name
            [$this, 'sanitize'] // Sanitize
        );

        add_settings_section(
            $this->settingsSection, // ID
            $this->title, // Title
            [$this, 'printSectionInfo'], // Callback
            $this->settingsPage // Page
        );

        foreach ($this->settingsFields as $settingsKey => $settingField) {
            add_settings_field(
                $settingsKey, // ID
                $settingField['name'], // Title
                [$this, $settingsKey], // Callback
                $this->settingsPage, // Page
                $this->settingsSection // Section
            );
        }
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     * @return array
     */
    public function sanitize($input)
    {
        $sanitizedInput = [];

        foreach ($this->settingsFields as $fieldKey => $settingsField) {
            if (isset($input[$fieldKey])) {
                if ($settingsField['type'] === 'checkbox') {
                    $sanitizedInput[$fieldKey] = absint($input[$fieldKey]);
                }
                if ($settingsField['type'] === 'text' || $settingsField['type'] === 'select') {
                    $sanitizedInput[$fieldKey] = sanitize_text_field($input[$fieldKey]);
                }
            }
        }

        return $sanitizedInput;
    }

    /**
     * Print the Section text
     */
    public function printSectionInfo()
    {
        print 'Enter your settings below:';
    }

    /**
     * Catch callbacks for creating setting fields
     * @param string $function
     * @param array $arguments
     * @return bool
     */
    public function __call($function, $arguments)
    {
        if (!isset($this->settingsFields[$function])) {
            return false;
        }

        $field = $this->settingsFields[$function];
        return $this->createSettingsField($field, $function);
    }

    public function getSettingValue($settingKey, $locale = null)
    {
        if (!$this->settingsValues) {
            $this->settingsValues = get_option($this->settingsKey);
        }

        if ($locale) {
            $settingKey = $locale . '_' . $settingKey;
        }

        if (isset($this->settingsValues[$settingKey]) && !empty($this->settingsValues[$settingKey])) {
            return $this->settingsValues[$settingKey];
        }
        return null;
    }

    private function enableLanguageFields()
    {
        $languageFields = [];

        foreach ($this->getLanguages() as $language) {
            foreach ($this->settingsFields as $fieldKey => $settingsField) {
                $localeFieldKey = $language->locale . '_' . $fieldKey;
                $languageFields[$localeFieldKey] = $settingsField;
                $languageFields[$localeFieldKey]['name'] .= ' ' . $language->locale;
                $languageFields[$localeFieldKey]['locale'] = $language->locale;
            }
        }

        $this->settingsFields = $languageFields;
    }

    public function languagesIsEnabled()
    {
        return LanguageProvider::enabled();
    }

    public function getLanguages()
    {
        if ($this->languagesIsEnabled()) {
            return LanguageProvider::getLanguageList();
        }
        return false;
    }

    /**
     * Get the current language by looking at the current HTTP_HOST
     *
     * @return null|string
     */
    public function getCurrentLanguage()
    {
        if ($this->languagesIsEnabled()) {
            return LanguageProvider::getCurrentLanguage('locale');
        }
        return null;
    }

    public function getCurrentLocale()
    {
        return $this->getCurrentLanguage() ?? null;
    }

    protected function getSelectFieldOptions($field)
    {
        if (isset($field['options_callback'])) {
            $options = call_user_func($field['options_callback'], $field['locale']);
            if ($options) {
                return $options;
            }
        }

        return [];
    }

    protected function createSettingsField($field, $fieldKey)
    {
        if (!isset($field['type'])) {
            return false;
        }

        $name = $this->settingsKey . "[$fieldKey]";
        $value = isset($this->settingsValues[$fieldKey]) ? esc_attr($this->settingsValues[$fieldKey]) : '';
        $checked = $value ? 'checked' : '';

        switch ($field['type']) {
            case 'text':
                $this->printTextInput($name, $value);
                return true;
            case 'checkbox':
                $this->printCheckbox($name, $checked);
                return true;
            case 'select':
                $this->printSelectInput($field, $name, $value);
                return true;
        }
        return false;
    }

    protected function printTextInput($name, $value)
    {
        echo sprintf(
            '<input type="text" name="%s" value="%s" class="regular-text" />',
            $name,
            $value
        );
    }

    protected function printCheckbox($name, $checked)
    {
        echo sprintf(
            '<input type="hidden" value="0" name="%s"><input type="checkbox" value="1" name="%s" %s>',
            $name,
            $name,
            $checked
        );
    }

    protected function printSelectInput($field, $name, $value)
    {
        $fieldOutput = sprintf('<select name="%s">', $name);
        $options = $this->getSelectFieldOptions($field);
        foreach ($options as $option) {
            $selected = ($option['value'] == $value) ? 'selected' : '';
            $fieldOutput .= sprintf(
                '<option value="%s" %s>%s</option>',
                $option['value'],
                $selected,
                $option['label']
            );
        }
        $fieldOutput .= "</select>";

        echo $fieldOutput;
    }
}
