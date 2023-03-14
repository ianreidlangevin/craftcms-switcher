<?php

namespace ianreid\switcher\services;

use Craft;
use craft\helpers\ArrayHelper;
use craft\base\Element;
use craft\base\Component;

class SwitcherServices extends Component
{

   /*
      * @var
   */
   private $_sites = [];


   /**
    * @return array
    */

   public function constructLangSwitcher(mixed $source, bool $removeCurrent, bool $onlyCurrentGroup, bool $redirectHomeIfMissing): array
   {
      $this->_sites = $this->getSitesForSwitcher($onlyCurrentGroup, $removeCurrent);

      if ($source instanceof Element) {
         $switcherValues = $this->setSwitcherForElement($source, $redirectHomeIfMissing);
      } elseif (is_array($source)) {
         $switcherValues = $this->setSwitcherForCustomSource($source);
      }

      return $switcherValues;
   }


   /**
    * Return all the sites or only ones from the current group
    *
    * @param bool $onlyCurrentGroup
    *
    * @return array
    */
   private function getSitesForSwitcher(bool $onlyCurrentGroup, bool $removeCurrent): array
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
    * If source is an array (ex: checkout pages, search results, etc.)
    *
    * @param array $sourceElements
    *
    * @return array
    */
   private function setSwitcherForCustomSource(array $sourceElements): array
   {

      $switcherItems = [];

      foreach ($sourceElements as $item) {

         $relatedSite = null;

         if (array_key_exists('siteId', $item)) {
            foreach ($this->_sites as $site) {
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

         $switcherItems = array_merge($switcherItems, [$urlAndSite]);
      }
      unset($item);

      return $switcherItems;
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
   private function setSwitcherForElement(Element $source, bool $redirectHomeIfMissing): array
   {

      $enabledSites = $this->_sites;
      $switcherItems = [];

      // to do -> if $source is not element, but array, filter the siteID from the array
      $enabledSitesIds = Craft::$app->elements->getEnabledSiteIdsForElement($source->id);

      // filter all sites with only the enabled one for this Element
      if ($redirectHomeIfMissing === false) {
         $enabledSites = array_filter($this->_sites, fn ($site) => in_array($site->id, $enabledSitesIds, true));
      }

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

      return $switcherItems;
   }
}
