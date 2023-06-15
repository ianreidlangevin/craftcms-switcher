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
         // legacy naming
         new TwigFunction('langSwitcher', [Switcher::getInstance()->switcherServices, 'constructLangSwitcher']),
         // new name
         new TwigFunction('getSwitcherSites', [Switcher::getInstance()->switcherServices, 'constructLangSwitcher'])
      ];
   }
}
