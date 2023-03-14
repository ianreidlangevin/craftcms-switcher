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
         new TwigFunction('langSwitcher', [$this, 'getSwitcher']),
         // add more here
      ];
   }

   public function getSwitcher($source = null, $removeCurrent = false, $onlyCurrentGroup = false, $redirectHomeIfMissing = false)
   {
      $langSwitcher = Switcher::getInstance()
         ->switcherServices
         ->buildSwitcher($source, $removeCurrent, $onlyCurrentGroup, $redirectHomeIfMissing);
      return $langSwitcher;
   }
}
