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
         new TwigFunction('langSwitcher', [Switcher::getInstance()->switcherServices, 'constructLangSwitcher']),
         new TwigFunction('localeAlternate', [Switcher::getInstance()->switcherServices, 'constructAlternateLocale'])

      ];
   }
}
