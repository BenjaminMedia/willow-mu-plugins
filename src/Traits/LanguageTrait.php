<?php

namespace Bonnier\Willow\MuPlugins\Traits;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

trait LanguageTrait
{
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
        return $this->getCurrentLanguage() ?? null;
    }
}
