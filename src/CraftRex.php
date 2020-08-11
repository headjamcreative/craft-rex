<?php
/**
 * Craft REX plugin for Craft CMS 3.x
 *
 * A plugin that syncs REX data with Craft.
 *
 * @link      https://www.headjam.com.au
 * @copyright Copyright (c) 2020 Ben Norman
 */

namespace headjam\craftrex;

use headjam\craftrex\services\RexApiService as RexApiService;
use headjam\craftrex\services\RexListingService as RexListingService;
use headjam\craftrex\services\RexSyncService as RexSyncService;
use headjam\craftrex\variables\CraftRexVariable;
use headjam\craftrex\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;


/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Ben Norman
 * @package   CraftRex
 * @since     1.0.2
 *
 * @property  RexApiService $rexApiService
 * @property  RexListingService $rexListingService
 * @property  RexSyncService $rexSyncService
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class CraftRex extends Plugin
{
  // Static Properties
  // =========================================================================

  /**
   * Static property that is an instance of this plugin class so that it can be accessed via
   * CraftRex::$plugin
   *
   * @var CraftRex
   */
  public static $plugin;

  // Public Properties
  // =========================================================================

  /**
   * To execute your plugin’s migrations, you’ll need to increase its schema version.
   *
   * @var string
   */
  public $schemaVersion = '1.0.2';

  /**
   * Set to `true` if the plugin should have a settings view in the control panel.
   *
   * @var bool
   */
  public $hasCpSettings = true;

  /**
   * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
   *
   * @var bool
   */
  public $hasCpSection = false;



  // Public Methods
  // =========================================================================
  /**
   * A customer logger for the plugin.
   */
  public static function log($message){
    Craft::getLogger()->log($message, \yii\log\Logger::LEVEL_INFO, 'craft-rex');
  }

  /**
   * Set our $plugin static property to this class so that it can be accessed via
   * CraftRex::$plugin
   *
   * Called after the plugin class is instantiated; do any one-time initialization
   * here such as hooks and events.
   *
   * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
   * you do not need to load it in your init() method.
   *
   */
  public function init()
  {
    parent::init();
    self::$plugin = $this;

    // Set the services
    $this->setComponents([
      'RexApiService' => RexApiService::class,
      'RexListingService' => RexListingService::class,
      'RexSyncService' => RexSyncService::class
    ]);

    // Register the variable
    Event::on(
      CraftVariable::class,
      CraftVariable::EVENT_INIT,
      function (Event $event) {
        /** @var CraftVariable $variable */
        $variable = $event->sender;
        $variable->set('craftrex', CraftRexVariable::class);
      }
    );

    // Init the customer logger
    $fileTarget = new \craft\log\FileTarget([
      'logFile' => Craft::getAlias('@storage/logs/craftrex.log'),
      'categories' => ['craft-rex']
    ]);
    Craft::getLogger()->dispatcher->targets[] = $fileTarget;
  }

  // Protected Methods
  // =========================================================================

  /**
   * Creates and returns the model used to store the plugin’s settings.
   *
   * @return \craft\base\Model|null
   */
  protected function createSettingsModel()
  {
    return new Settings();
  }

  /**
   * Returns the rendered settings HTML, which will be inserted into the content
   * block on the settings page.
   *
   * @return string The rendered settings HTML
   */
  protected function settingsHtml(): string
  {
    return Craft::$app->view->renderTemplate(
      'craft-rex/settings',
      [
        'settings' => $this->getSettings()
      ]
    );
  }
}
