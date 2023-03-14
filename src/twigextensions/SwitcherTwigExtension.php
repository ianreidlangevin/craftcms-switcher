<?php

namespace ianreid\switcher\twigextensions;

use ianreid\switcher\Switcher;

use Craft;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class SwitcherTwigExtension extends AbstractExtension
{

   public function getName()
   {
      return 'Switcher';
   }

   public function getFunctions()
   {
      return [
         new TwigFunction('langSwitcher', [$this, 'getSwitcher'])
      ];
   }

   public function getSwitcher(
      mixed $source = null, 
      bool $removeCurrent = false, 
      bool $onlyCurrentGroup = false, 
      bool $redirectHomeIfMissing = false
   )
   {
      $langSwitcher = Switcher::getInstance()
         ->switcherServices
         ->constructLangSwitcher($source, $removeCurrent, $onlyCurrentGroup, $redirectHomeIfMissing);
      return $langSwitcher;
   }
}
