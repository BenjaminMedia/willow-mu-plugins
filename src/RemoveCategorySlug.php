<?php

namespace Bonnier\Willow\MuPlugins;

/**
 * Based on https://github.com/ezraverheijen/wp-remove-category-base
 *
 * Class RemoveCategorySlug
 * @package Bonnier\Willow\MuPlugins
 */
class RemoveCategorySlug
{
    public function __construct()
    {
        add_action('init', [$this, 'flushRules'], 999);

        foreach (['created_category', 'edited_category', 'delete_category'] as $action) {
            add_action($action, [$this, 'scheduleFlush']);
        };

        add_filter('query_vars', [$this, 'updateQueryVars' ]);
        add_filter('category_link', [$this, 'removeCategoryBase' ]);
        add_filter('request', [$this, 'redirectOldCategoryUrl' ]);
        add_filter('category_rewrite_rules', [$this, 'addCategoryRewriteRules' ]);

        register_activation_hook(
            'willow-mu-plugins/willow-mu-plugins.php',
            [$this, 'onActivationAndDeactivation' ]
        );
        register_deactivation_hook(
            'willow-mu-plugins/willow-mu-plugins.php',
            [$this, 'onActivationAndDeactivation' ]
        );
    }

    public function flushRules()
    {
        if (get_option('rcb_flush_rewrite_rules')) {
            add_action('shutdown', 'flush_rewrite_rules');
            delete_option('rcb_flush_rewrite_rules');
        }
    }

    public function scheduleFlush()
    {
        update_option('rcb_flush_rewrite_rules', 1);
    }

    public function removeCategoryBase($permalink)
    {
        $categoryBase = get_option('category_base') ? get_option('category_base') : 'category';

        // Remove initial slash, if there is one
        // (the trailing slash is removed in the regex replacement and we don't want to end up short a slash)
        if ('/' === substr($categoryBase, 0, 1)) {
            $categoryBase = substr($categoryBase, 1);
        }

        $categoryBase .= '/';

        $pattern = sprintf('`%s`u', preg_quote($categoryBase, '`'));

        return preg_replace($pattern, '', $permalink, 1);
    }

    public function updateQueryVars($queryVars)
    {
        $queryVars[] = 'rcb_category_redirect';

        return $queryVars;
    }

    public function redirectOldCategoryUrl($queryVars)
    {
        if (isset($queryVars['rcb_category_redirect'])) {
            $category_link = trailingslashit(get_option('home')) . user_trailingslashit(
                $queryVars['rcb_category_redirect'],
                'category'
            );
            wp_redirect($category_link, 301);
            exit;
        }

        return $queryVars;
    }

    public function addCategoryRewriteRules()
    {
        global $wp_rewrite;

        $categoryRewrite = array();

        if (function_exists('is_multisite') && is_multisite() && ! is_subdomain_install() && is_main_site()) {
            $blogPrefix = 'blog/';
        } else {
            $blogPrefix = '';
        }

        foreach (get_categories(['hide_empty' => false]) as $category) {
            $categoryNicename = $category->slug;

            if ($category->cat_ID == $category->parent) { // recursive recursion
                $category->parent = 0;
            } elseif (0 != $category->parent) {
                $categoryNicename = get_category_parents($category->parent, false, '/', true) . $categoryNicename;
            }
            $firstPattern = sprintf('%s(%s)/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$', $blogPrefix, $categoryNicename);
            $secondPattern = sprintf(
                '%s(%s)/%s/?([0-9]{1,})/?$',
                $blogPrefix,
                $categoryNicename,
                $wp_rewrite->pagination_base
            );
            $thirdPattern = sprintf('%s(%s)/?$', $blogPrefix, $categoryNicename);
            $categoryRewrite[$firstPattern] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
            $categoryRewrite[$secondPattern] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
            $categoryRewrite[$thirdPattern] = 'index.php?category_name=$matches[1]';
        }

        // Redirect support for `old` category base
        $old_base = $wp_rewrite->get_category_permastruct();
        $old_base = str_replace('%category%', '(.+)', $old_base);
        $old_base = trim($old_base, '/');

        $categoryRewrite[$old_base . '$'] = 'index.php?rcb_category_redirect=$matches[1]';

        return $categoryRewrite;
    }

    public function onActivationAndDeactivation()
    {
        flush_rewrite_rules();
    }
}
