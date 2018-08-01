# Willow Must Use Plugins
This is a collection of Must Use Plugins for the WordPress part of the Willow Platform

## Installation
Simply require the package with composer:
```
composer require bonnier/willow-mu-plugins
```

## Functionality
#### - DefaultPlugins
This plugin makes sure the necessary plugins are available an activated.

#### - LanguageProvider
This plugin wraps Polylang in static methods,
allowing other plugins to call these methods without having to worry about Polylang.

#### - OffloadS3
This plugin ensures that specific uploaded files are marked as downloadable
and that plugin configurations are set through the environmentfile, instead of through the settings page.  

#### - RemoveCategorySlug
This plugin ensures that WordPress doesn't include the slug `category` in URLs.

#### - TimeZone
This plugin forces WordPress Timezone setting to be set to `Europe/Copenhagen`.
