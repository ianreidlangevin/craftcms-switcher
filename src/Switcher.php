<?php

namespace ianreid\switcher;

use ianreid\switcher\models\Settings;
use ianreid\switcher\services\SwitcherServices as Service;
use ianreid\switcher\twigextensions\SwitcherTwigExtension;

use Craft;
use craft\base\Plugin;
use craft\helpers\FileHelper;

class Switcher extends Plugin
{

   // Public Methods
   // --------------------------------------------------------------------------

   public function init()
   {
      parent::init();

      // Services
      $this->setComponents([
         'switcherServices' => Service::class,
      ]);
      // Twig Extension
      Craft::$app->view->registerTwigExtension(new SwitcherTwigExtension());
   }

}
