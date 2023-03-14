<?php

namespace ianreid\switcher\services;

use ianreid\switcher\Switcher;

use Craft;
use craft\helpers\ArrayHelper;
use craft\base\Component;
use craft\services\Elements;
use craft\models\Site;
use craft\models\SiteGroup;
use craft\events\SiteEvent;
use craft\events\SiteGroupEvent;

class SwitcherServices extends Component
{

   // https://github.com/craftcms/cms/blob/v3/src/web/UrlManager.php
   /*
      * @var
      */
   private $_sites = [];
   private $_switcherValues = [];
   private $_sourceUrls = [];

   // Constructor
   // --------------------------------------------------------------------------

   public function __construct()
   {
   }

   /** getSwitcherSites
    * @return array|null
    */

   public function buildSwitcher(mixed $source, bool $removeCurrent, bool $onlyCurrentGroup, bool $redirectHomeIfMissing)
   {
      $this->_sites = $this->getSitesForSwitcher($onlyCurrentGroup, $removeCurrent);
      $this->_switcherValues = $this->getEnabledSitesForSource($source, $redirectHomeIfMissing);

      return $this->_switcherValues;
   }

   /**
    * Return all the sites or only ones from the current group
    *
    * @param bool $onlyCurrentGroup
    *
    * @return array
    */
   public function getSitesForSwitcher($onlyCurrentGroup, $removeCurrent): array
   {
      $currentSite = Craft::$app->getSites()->currentSite;
      $sites = Craft::$app->getSites()->getAllSites();

      // if argument onlyCurrentGroup is true, filter array by the current group ID
      if ($onlyCurrentGroup === true) {
         $sites = array_filter($sites, fn ($site) => $site->groupId === $currentSite->groupId);
      }

      // if argument removeCurrent is true
      if ($removeCurrent === true) {
         ArrayHelper::removeValue($sites, $currentSite);
      }

      return $sites;
   }


   /**
    * Get the id of the enabled sites for the source
    * Filter the all sites array with these ids
    *
    * @param Element $source
    *
    * @return array in this form :
    *  [
    *    0 => [ "url" => "https://siteurl.com/page-uri", "site" => craft\models\Site ]
    *    1 => [ "url" => "https://siteurl.com/page-uri", "site" => craft\models\Site ]
    *   ]
    */
   public function getEnabledSitesForSource($source, $redirectHomeIfMissing): array
   {

      $enabledSites = $this->_sites;
      $switcherItems = [];

      // to do -> if $source is not element, but array, filter the siteID from the array
      $enabledSitesIds = Craft::$app->elements->getEnabledSiteIdsForElement($source->id);

      // filter all sites with only the enabled one for this Element
      if($redirectHomeIfMissing === false) {
         $enabledSites = array_filter($this->_sites, fn ($site) => in_array($site->id, $enabledSitesIds, true));
      }
      

      if (!empty($enabledSites)) {

         foreach ($enabledSites as $site) {
            $urlAndSite = [];
            $uri = Craft::$app->elements->getElementUriForSite($source->id, $site->id);
            // if source is not enabled for a site but exist, add the home redirect (baseUrl) 
            // for the site if $redirectHomeIfMissing is true
            if ($redirectHomeIfMissing === true and !in_array($site->id, $enabledSitesIds)) {
               $urlAndSite['url'] = $site->baseUrl;
            // else, build the url with the Uri
            } else {
               $urlAndSite['url'] = rtrim($site->baseUrl, '/') . '/' . $uri;
            }
            $urlAndSite['site'] = $site;
            // merge into array
            $switcherItems = array_merge($switcherItems, [$urlAndSite]);
         }
         unset($site);

      }

      return $switcherItems;
   }
}
