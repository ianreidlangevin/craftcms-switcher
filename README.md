<p><img src="./src/icon.svg" width="100" height="100" alt="AWS Image Handler URLs icon"></p>

<h1>Switcher for Craft CMS</h1>


## Requirements

This plugin requires Craft CMS 4.x


## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require ianreid/switcher -w && php craft plugin/install switcher


## Overview

This plugin adds a Twig function to easily add a site switcher. It allows you to output sites from the current group or sites from all groups. See the parameters bellow for all possibilities.



## Twig function

### langSwitcher()

```
{% set languages = langSwitcher(entry, false, false, false) %}
```

#### Parameters

| Parameters    | Type | Default |
| -------- | ------- | ------- |
| source  | Element or Array  | null |
| removeCurrent | Bool     | true |
| onlyCurrentGroup    | Bool    | true |
| redirectHomeIfMissing    | Bool    | false |

#### Returns

The function returns an array of items with the following keys : "url" and "site".

```
array [
  0 => array [
    "url" => "http://yoursite:8888/uri"
    "site" => craft\models\Site {}
  ]
  1 => array [
    "url" => "http://yoursite:8888/en/uri"
    "site" => craft\models\Site {}
  ]
]
```

#### Examples

##### Basic usage without width default params

```
{% set languages = langSwitcher(entry) %}

{% if languages|length %}
   {% for item in languages %}
      <a 
         href="{{ url(item.url) }}" 
         hreflang="{{item.site.language}}" 
         lang="{{item.site.language}}" 
      >
         {{ item.site.name [0:2]|capitalize }} // the 2 first letters only
      </a>
   {% endfor %}
{% endif %}
```

##### Usage grouping sites by groups

```
{% set languages = langSwitcher(entry, false, false, true) %}

{% if languages|length %}

   {% set languagesByGroup = languages|group(link => link.site.group) %}

   {% for group, langs in languagesByGroup %}

      <h3 class="text-9 text-blue-500">{{ group }}</h3>
      {% for item in langs %}
         <a 
            href="{{ url(item.url) }}" 
            hreflang="{{item.site.language}}" 
            lang="{{item.site.language}}" 
         >
            {{ item.site.name }}
         </a>
      {% endfor %}

   {% endfor %}
{% endif %}
```

#### Usage with an array as source

You can use switcher to change sites on pages that are not Craft Elements. For example, __checkout pages (made with custom routes), search results, etc.__ View the following example :

```

{% set customSource = [ 
      {'uri':'cart', 'siteId': 1},
      {'uri':'panier', 'siteId': 2}, 
      {'uri':'cesta', 'siteId': 3},
   ]
%}

{% set languages = langSwitcher(customSource, false, false, true) %}

{% if languages|length %}
   {% for item in languages %}
      <a 
         href="{{ url(item.url) }}" 
         hreflang="{{item.site.language}}" 
         lang="{{item.site.language}}" 
      >
         {{ item.site.name [0:2]|capitalize }} // the 2 first letters only
      </a>
   {% endfor %}
{% endif %}
```

## Hreflang in head

The plugin also provides the ability to easily set the alternate languages. It can be very useful for the `og:locale:alternate` property.

### langSwitcher()

```
{% set localeAlternate = localeAlternate(false) %}
```

#### Parameters

| Parameters    | Type | Default |
| -------- | ------- | ------- |
| onlyCurrentGroup    | Bool    | true |

##### Usage for the og:locale:alternate in `<head>`

```
<meta property="og:locale" content="{{ currentSite.language }}">
{% set otherLocales = localeAlternate(false) %}
{% if otherLocales|length %}
   {% for site in otherLocales %}
      <meta property="og:locale:alternate" content="{{ site.language }}">
   {% endfor %}
{% endif %}

```

#### Returns

This function returns an array of `craft\models\Site` without the current one.

---


Brought to you by [Ian Reid Langevin](https://www.reidlangevin.com)
