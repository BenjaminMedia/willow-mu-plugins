<?php

namespace Bonnier\Willow\MuPlugins\Helpers;

class LanguageProvider
{
    /**
     * True if Polylang is enabled and has a language.
     *
     * @return bool
     */
    public static function enabled(): bool
    {
        return function_exists('PLL') && PLL()->model->get_languages_list();
    }

    /**
     * Wrapper for pll_current_language
     * Returns the current language
     *
     * @param string $format Either 'name', 'locale' or 'slug'
     *
     * @return string|null
     */
    public static function getCurrentLanguage(string $format = 'slug'): ?string
    {
        if (function_exists('pll_current_language')) {
            return pll_current_language($format);
        }

        return null;
    }

    /**
     * Wrapper for pll_languages_list
     * Returns the list of languages
     *
     * @param array $args Argument list
     *
     * @return array|null
     */
    public static function getSimpleLanguageList(array $args = []): ?array
    {
        if (function_exists('pll_languages_list')) {
            return pll_languages_list($args);
        }

        return null;
    }

    /**
     * Wrapper for PLL()->model->get_languages_list()
     * Returns the detailed list of languages
     *
     * @param array $args Argument list
     *
     * @return array|null
     */
    public static function getLanguageList(array $args = []): ?array
    {
        if (function_exists('PLL')) {
            return PLL()->model->get_languages_list($args);
        }

        return null;
    }

    /**
     * Wrapper for pll_default_language
     * Returns the default language
     *
     * @param string $format Either 'name', 'locale' or 'slug'
     *
     * @return null|string
     */
    public static function getDefaultLanguage(string $format = 'slug'): ?string
    {
        if (function_exists('pll_default_language')) {
            return pll_default_language($format);
        }

        return null;
    }

    /**
     * Wrapper for pll_get_post
     * Returns the post (or page) translation
     *
     * @param int $postId Post ID
     * @param null|string $languageCode 2-letter language code, for instance 'da'
     *
     * @return \WP_Post|null
     */
    public static function getPost(int $postId, ?string $languageCode = null): ?\WP_Post
    {
        if (function_exists('pll_get_post')) {
            return pll_get_post($postId, $languageCode);
        }

        return null;
    }

    /**
     * Wrapper for pll_get_term
     * Returns the category (or post tag) translation
     *
     * @param int $termId Term ID
     * @param null|string $langaugeCode 2-letter language code, for instance 'da'
     *
     * @return \WP_Term|null
     */
    public static function getTerm(int $termId, ?string $langaugeCode = null) : ?\WP_Term
    {
        if (function_exists('pll_get_term')) {
            return pll_get_term($termId, $langaugeCode);
        }

        return null;
    }

    /**
     * Wrapper for pll_get_post_language
     * Gets the language of a post or page (or custom post type post)
     *
     * @param int $postId Post ID
     * @param string $format Either 'name', 'locale' or 'slug'
     *
     * @return string|null
     */
    public static function getPostLanguage(int $postId, string $format = 'slug'): ?string
    {
        if (function_exists('pll_get_post_language')) {
            return pll_get_post_language($postId, $format);
        }

        return null;
    }

    /**
     * Wrapper for pll_set_post_language
     * Sets the language of a post or page (or custom post type post)
     *
     * @param int $postId Post ID
     * @param string $languageCode Language Code
     */
    public static function setPostLanguage(int $postId, string $languageCode): void
    {
        if (function_exists('pll_set_post_language')) {
            pll_set_post_language($postId, $languageCode);
        }
    }

    /**
     * Wrapper for pll_get_term_language
     * Gets the language of a category or post tag (or custom taxonomy term)
     *
     * @param int $termId Term ID
     * @param string $format Either 'name', 'locale' or 'slug'
     *
     * @return string|null
     */
    public static function getTermLanguage(int $termId, string $format = 'slug'): ?string
    {
        if (function_exists('pll_get_term_language')) {
            return pll_get_term_language($termId, $format);
        }

        return null;
    }

    /**
     * Wrapper for pll_set_term_language
     * Sets the language of a category or post tag (or custom taxonomy term)
     *
     * @param int $termId Term ID
     *
     * @param string $languageCode Language Code
     */
    public static function setTermLanguage(int $termId, string $languageCode): void
    {
        if (function_exists('pll_set_term_language')) {
            pll_set_term_language($termId, $languageCode);
        }
    }

    /**
     * Wrapper for pll_save_post_translations
     * Defines which posts are translations of each other
     *
     * @param array $postTranslations Associative array with language codes and post ids
     * ['da' => 123, 'se' => 456, 'fi' => 789]
     */
    public static function savePostTranslations(array $postTranslations): void
    {
        if (function_exists('pll_save_post_translations')) {
            pll_save_post_translations($postTranslations);
        }
    }

    /**
     * Wrapper for pll_register_string
     * Allows plugins to add their own strings in the “strings translation” panel.
     * The function must be called on admin side (the functions.php file is OK for themes).
     * It is possible to register empty strings (for example when they come from options),
     * but they won’t appear in the list table.
     *
     * @param string $name Name of provider
     * @param string $string String to be translated
     * @param string $context Group the translation belongs to
     * @param bool $multiline True if the text field should be multiline
     */
    public static function registerStringTranslation(
        string $name,
        string $string,
        string $context = 'Willow',
        bool $multiline = false
    ): void {
        if (function_exists('pll_register_string')) {
            pll_register_string($name, $string, $context, $multiline);
        }
    }

    /**
     * Wrapper for pll_the_languages
     * Displays a language switcher.
     *
     * @param array $args
     * ‘dropdown’ => displays a list if set to 0, a dropdown list if set to 1 (default: 0)
     * ‘show_names’ => displays language names if set to 1 (default: 1)
     * ‘display_names_as’ => either ‘name’ or ‘slug’ (default: ‘name’)
     * ‘show_flags’ => displays flags if set to 1 (default: 0)
     * ‘hide_if_empty’ => hides languages with no posts (or pages) if set to 1 (default: 1)
     * ‘force_home’ => forces link to homepage if set to 1 (default: 0)
     * ‘echo’ => echoes if set to 1, returns a string if set to 0 (default: 1)
     * ‘hide_if_no_translation’ => hides the language if no translation exists if set to 1 (default: 0)
     * ‘hide_current’=> hides the current language if set to 1 (default: 0)
     * ‘post_id’ => if set, displays links to translations of the post (or page) defined by post_id (default: null)
     * ‘raw’ => use this to create your own custom language switcher (default:0)
     *
     * @return null|string
     */
    public static function echoLanguages(array $args = []): ?string
    {
        if (function_exists('pll_the_languages')) {
            return pll_the_languages($args);
        }

        return null;
    }

    /**
     * Wrapper for pll_home_url
     * Returns the home page url
     *
     * @param string $path
     * @param null|string $languageCode
     *
     * @return string
     */
    public static function getHomeUrl(string $path = '', ?string $languageCode = null): string
    {
        if (function_exists('pll_home_url')) {
            return pll_home_url($languageCode) . $path;
        }

        return home_url($path);
    }

    /**
     * Wrapper for pll__
     * Translates a string previously registered with pll_register_string
     *
     * @param string $string Unique translation key
     *
     * @return string
     */
    public static function translate(string $string): string
    {
        if (function_exists('pll__')) {
            return pll__($string);
        }

        return $string;
    }

    /**
     * Wrapper for pll_e
     * Echoes a translated string previously registered with pll_register_string
     *
     * @param string $string Unique translation key
     */
    public static function echoTranslation(string $string): void
    {
        if (function_exists('pll_e')) {
            pll_e($string);
        }
    }

    /**
     * Wrapper for pll_translate_string
     * Translates a string previously registered with pll_register_string in a given language.
     * Unlike ‘pll__()’ and ‘pll_e()’ which allow to get the translation only in the current language
     * (as do the WordPress localization functions ‘__()’ and ‘_e()’),
     * this function allows to get the translation in any language.
     *
     * @param string $string Unique translation key
     * @param string $languageCode 2-letter language code
     *
     * @return string
     */
    public static function getTranslation(string $string, string $languageCode): string
    {
        if (function_exists('pll_translate_string')) {
            return pll_translate_string($string, $languageCode);
        }
        return $string;
    }

    /**
     * Wrapper for pll_get_post_translations
     * Returns an associative array of translations with language code as key and translation post_id as value
     *
     * @param int $postId Post ID
     *
     * @return array|null
     */
    public static function getPostTranslations(int $postId): ?array
    {
        if (function_exists('pll_get_post_translations')) {
            return pll_get_post_translations($postId);
        }

        return null;
    }

    /**
     * Wrapper for pll_get_term_translations
     * Returns an associative array of translations with language code as key and translation term_id as value
     *
     * @param int $termId Term ID
     *
     * @return array|null
     */
    public static function getTermTranslations(int $termId): ?array
    {
        if (function_exists('pll_get_term_translations')) {
            return pll_get_term_translations($termId);
        }

        return null;
    }

    /**
     * Wrapper for pll_save_term_translations
     *
     * @param array $terms Associative array with language codes and term ids
     * ['da' => 123, 'se' => 456, 'fi' => 789]
     */
    public static function saveTermTranslations(array $terms): void
    {
        if (function_exists('pll_save_term_translations')) {
            pll_save_term_translations($terms);
        }
    }

    /**
     * Wrapper for pll_count_posts
     * Counts posts in a defined language
     *
     * @param string $languageCode
     * @param array $args Allows to restrict the count to a certain class of post archive
     * [‘post_type’, ‘m,’ ‘year’, ‘monthnum’, ‘day’, ‘author’, ‘author_name’, ‘post_format’]
     *
     * @return int
     */
    public static function countPosts(string $languageCode, array $args): int
    {
        if (function_exists('pll_count_posts')) {
            return pll_count_posts($languageCode, $args);
        }
        return 0;
    }
}
