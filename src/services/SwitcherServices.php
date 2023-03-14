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
   private $_switcherLinks = [];
   private $_sites = [];
   private $_switcherValues = [];
   private $_sourceUrls = [];
   private $_currentSite;

   // Constructor
   // --------------------------------------------------------------------------

   public function __construct()
   {
      $this->_currentSite = Craft::$app->getSites()->currentSite;
   }

   /** getSwitcherSites
    * @return array|null
    */

   public function buildSwitcher(mixed $source, bool $removeCurrent, bool $onlyCurrentGroup, bool $redirectHomeIfMissing)
   {
      $this->_sites = $this->getSwitcherSites($onlyCurrentGroup, $removeCurrent);
      $this->_switcherValues = $this->getEnabledSitesForSource($source);

      return $this->_switcherValues;
   }

   /**
    * Return all the sites or only ones from the current group
    *
    * @param bool $onlyCurrentGroup
    *
    * @return array
    */
   public function getSwitcherSites($onlyCurrentGroup, $removeCurrent): array
   {
      $sites = [];

      if ($onlyCurrentGroup === true) {
         $sites = Craft::$app->getSites()->getGroupById($this->_currentSite->groupId)->getSites();
      } else {
         $sites = Craft::$app->getSites()->getAllSites();
      }
      if ($removeCurrent === true) {
         ArrayHelper::removeValue($sites, $this->_currentSite);
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
   public function getEnabledSitesForSource($source): array
   {

      $enabledSites = [];
      $urlsWithSites = [];

      // to do -> if $source is not element, but array, filter the siteID from the array
      $enabledSitesIds = Craft::$app->elements->getEnabledSiteIdsForElement($source->id);

      // filter all sites with only the enabled one for this Element
      if (!empty($enabledSitesIds)) {
         $enabledSites = array_filter($this->_sites, fn ($site) => in_array($site->id, $enabledSitesIds, true));
      }


      if (!empty($enabledSites)) {
         foreach ($enabledSites as $site) {
            $urlSite = [];
            $uri = Craft::$app->elements->getElementUriForSite($source->id, $site->id);
            // push value in array keys
            $urlSite['url'] = rtrim($site->baseUrl, '/') . '/' . $uri;
            $urlSite['site'] = $site;
            // merge into array
            $urlsWithSites = array_merge($urlsWithSites, [$urlSite]);
         }
         unset($site);
      }

      return $urlsWithSites;
   }
}
