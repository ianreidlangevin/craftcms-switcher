<p><img src="./src/icon.svg" width="100" height="100" alt="AWS Image Handler URLs icon"></p>

<h1>Switcher for Craft CMS</h1>


## Requirements

This plugin requires Craft CMS ^4.x or ^5.x


## Installation

To install the plugin, follow these instructions.

1. In your terminal, go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require ianreid/switcher -w && php craft plugin/install switcher


## Overview

The Switcher plugin for Craft CMS provides a Twig function for generating a site switcher on your webpage. This switcher can be configured to display sites from the current group or all groups. The plugin is compatible with Craft CMS 4.x.


## Twig function

The `getSwitcherSites()` Twig function comes with several parameters. 

### getSwitcherSites()

| Parameters    | Type | Default | Description |
| :-------- | :------- | :------- | :------- |
| source  | Element or Array  | null | Element or array from which the sites are extracted. |
| removeCurrent | Bool     | true | Determines whether the current site should be excluded from the output. |
| onlyCurrentGroup    | Bool    | true | Specifies whether only sites from the current group should be shown. |
| redirectHomeIfMissing    | Bool    | false | Decides whether to redirect to the home page if a site is missing. |


### Recommended usage

We suggest setting the source on a per-template basis. You can establish a fallback to `entry`, which allows you to set the variable for elements like categories, products, or an array of custom routes.

##### 1. Define an availableSites variable in your main layout file

`{% set availableSites = getSwitcherSites(switcherCustomSource|default(entry ?? null)) %}`

In this variable, we use the `switcherCustomSource` variable as the source, with `entry` as the fallback, and then `null`.

You can then pass this variable to your navbar or any file that includes a _site/languages_ switcher to prevent a redundant query.

By defining an `availableSites` variable at the top of your main layout file, you can also use it for the `og:locale:alternate` meta in the head, the `<link rel=alternate>`, etc.


##### 2. Determine your source in your page template

For any `craft\base\Element` __other than `Entry`__ (the default in the previous example), you can do the following in your page template :

```
{% set switcherCustomSource = product %}
```
or 

```
{% set switcherCustomSource = category %}
```

or for an array (ex: with custom routes)

```
{% set switcherCustomSource = [ 
      {'uri':'cart', 'siteId': 1},
      {'uri':'panier', 'siteId': 2}, 
      {'uri':'cesta', 'siteId': 3},
   ]
%}
```

### Output

The function returns an array of items with two keys: "url" and "site". The "url" key is the site's URL, and the "site" key is the site model.

```
array [
  0 => array [
    "url" => "http://yoursite.com/uri"
    "site" => craft\models\Site {}
  ]
  1 => array [
    "url" => "http://yoursite.com/en/uri"
    "site" => craft\models\Site {}
  ]
]
```

## Site Switcher Examples

##### Basic usage

```

{% if availableSites|length %}
   {% for item in availableSites %}
      <a 
         href="{{ url(item.url) }}" 
         hreflang="{{item.site.language}}" 
         lang="{{item.site.language}}" 
      >
         {{ item.site.language }}
      </a>
   {% endfor %}
{% endif %}
```

##### Grouping Sites by Groups

```

{% if availableSites|length %}

   {% set availableSitesByGroup = availableSites|group(lang => lang.site.group) %}

   {% for group, langs in availableSitesByGroup %}

      <h3 class="text-9 text-blue-500">{{ group }}</h3>
      {% for item in langs %}
         <a 
            href="{{ url(item.url) }}" 
            hreflang="{{item.site.language}}" 
            lang="{{item.site.language}}" 
         >
            {{ item.site.language }}
         </a>
      {% endfor %}

   {% endfor %}
{% endif %}
```

##### Displaying Only the First Two Letters of the Language

```
{% if availableSites|length %}
   {% for item in availableSites %}
      <a 
         href="{{ url(item.url) }}" 
         hreflang="{{item.site.language}}" 
         lang="{{item.site.language}}" 
      >
         {{ item.site.language [0:2]|capitalize }}
      </a>
   {% endfor %}
{% endif %}
```

##### Using for og:locale:alternate in `<head>`

Use your previously created `availableSites` variable if you wish to apply the same parameters, or create a different variable if not.

:bulb: You must define your variable __PRIOR__ to the following meta property.

```
<meta property="og:locale" content="{{ currentSite.language }}">

{% if availableSites|length %}
   {% for item in availableSites %}
      <meta property="og:locale:alternate" content="{{ item.site.language }}">
   {% endfor %}
{% endif %}

```
> :bulb: All these examples can be adjusted based on the function parameters.

---


This plugin is brought to you by [Ian Reid Langevin](https://www.reidlangevin.com)
