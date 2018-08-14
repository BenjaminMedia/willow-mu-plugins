<?php

namespace Bonnier\Willow\MuPlugins\Traits;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

trait LanguageTrait
{
    protected $currentLocale;

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

    public function getCurrentLanguage()
    {
        if ($this->languagesIsEnabled()) {
            return LanguageProvider::getCurrentLanguage('locale');
        }
        return null;
    }

    public function getCurrentLocale()
    {
        if ($locale = $this->currentLocale) {
            return $locale;
        }

        return $this->getCurrentLanguage() ?? null;
    }

    public function setCurrentLocale($locale)
    {
        $this->currentLocale = $locale;
    }
}
