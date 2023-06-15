<?php

namespace ianreid\switcher\services;

use Craft;
use craft\helpers\ArrayHelper;
use craft\base\Element;
use craft\models\Site;
use craft\base\Component;

class SwitcherServices extends Component
{

   // Vars
   // --------------------------------------------------------------------------

   private $_switcherSites = [];
   private $_allSites = [];
   private $_currentSite;


   // Constructor
   // --------------------------------------------------------------------------

   public function __construct()
   {
      $this->_allSites = Craft::$app->getSites()->getAllSites();
      $this->_currentSite = Craft::$app->getSites()->currentSite;
   }


   // Pulic methods
   // --------------------------------------------------------------------------

   /**
    * Public method used by the Twig function
    *
    * @return array
    */

   public function constructLangSwitcher(mixed $source = null, bool $removeCurrent = true, bool $onlyCurrentGroup = true, bool $redirectHomeIfMissing = false): array
   {
      $switcherValues = [];
      $this->_switcherSites = $this->getSitesBasedOnParams($onlyCurrentGroup, $removeCurrent);

      if ($source instanceof Element) {
         $switcherValues = $this->buildDataForElement($source, $redirectHomeIfMissing);
      } elseif (is_array($source)) {
         $switcherValues = $this->buildDataForCustomSource($source);
      }

      return $switcherValues;
   }

   // Private methods
   // --------------------------------------------------------------------------

   /**
    * Returns all the sites or only ones from the current site group
    *
    * @param bool $onlyCurrentGroup
    * @param bool $removeCurrent
    * @return array
    */
   private function getSitesBasedOnParams(bool $onlyCurrentGroup, bool $removeCurrent): array
   {
      $sites = $this->_allSites;
      // if argument onlyCurrentGroup is true, filter array by the current group ID
      if ($onlyCurrentGroup === true) {
         $sites = $this->getOnlyCurrentGroupSites($sites, $this->_currentSite);
      }
      // if argument removeCurrent is true
      if ($removeCurrent === true) {
         ArrayHelper::removeValue($sites, $this->_currentSite);
      }

      return $sites;
   }

   /**
    * Returns switcher data if source is a Craft Element
    *
    * @param Element $source - Any Craft Element (entry, category, product, etc.)
    * @param bool $redirectHomeIfMissing - if element is missing or disable - show site but link to baseUrl
    * @return array
    */
   private function buildDataForElement(Element $sourceElement, bool $redirectHomeIfMissing): array
   {
      $enabledSites = $this->_switcherSites;
      $switcherData = [];

      $enabledSitesIds = Craft::$app->elements->getEnabledSiteIdsForElement($sourceElement->id);

      if ($redirectHomeIfMissing === false) {
         $enabledSites = array_filter($this->_switcherSites, fn ($site) => in_array($site->id, $enabledSitesIds, false));
      }

      foreach ($enabledSites as $site) {
         $urlAndSite = [];
         // if source is not enabled for a site but exists, add the home redirect (baseUrl) 
         if ($redirectHomeIfMissing === true and !in_array($site->id, $enabledSitesIds)) {
            $urlAndSite['url'] = $site->baseUrl;
         } elseif ($sourceElement->uriFormat === "__home__") {
            $urlAndSite['url'] = $site->baseUrl;
         } else {
            $uri = Craft::$app->elements->getElementUriForSite($sourceElement->id, $site->id);
            $urlAndSite['url'] = rtrim($site->baseUrl, '/') . '/' . $uri;
         }
         $urlAndSite['site'] = $site;

         $switcherData = array_merge($switcherData, [$urlAndSite]);
      }
      unset($site);

      return $switcherData;
   }

   /**
    * Returns switcher data if source is an array (ex: checkout pages, search results, etc.)
    *
    * @param array $sourceArray
    * @return array
    */
   private function buildDataForCustomSource(array $sourceArray): array
   {
      $switcherData = [];

      foreach ($sourceArray as $item) {

         $relatedSite = null;

         if (array_key_exists('siteId', $item)) {
            foreach ($this->_switcherSites as $site) {
               if ($site->id === $item['siteId']) {
                  $relatedSite = $site;
                  break;
               }
            }
            unset($site);
         }

         if ($relatedSite === null) continue;

         $urlAndSite = [];
         $urlAndSite['site'] = $relatedSite;

         if (array_key_exists('uri', $item)) {
            $urlAndSite['url'] = rtrim($relatedSite->baseUrl, '/') . '/' . $item['uri'];
         } else {
            $urlAndSite['url'] = $site->baseUrl;
         }

         $switcherData = array_merge($switcherData, [$urlAndSite]);
      }
      unset($item);

      return $switcherData;
   }

   /**
    * Util method to return only the sites of the same group of the current site
    *
    * @param array $sites
    * @param Site $currentSite
    * @return array
    */
   private function getOnlyCurrentGroupSites(array $sites, Site $currentSite): array
   {
      return array_filter($sites, fn ($site) => $site->groupId === $currentSite->groupId);
   }
}
