<?php
/**
 * Craft REX plugin for Craft CMS 3.x
 *
 * A plugin that syncs REX data with Craft.
 *
 * @link      https://www.headjam.com.au
 * @copyright Copyright (c) 2020 Ben Norman
 */

namespace headjam\craftrex\controllers;

use headjam\craftrex\CraftRex;

use Craft;
use craft\web\Controller;

/**
 * Queries the REX api for data.
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Ben Norman
 * @package   CraftRex
 * @since     1.0.0
 */
class DefaultController extends Controller
{
  // Protected Properties
  // =========================================================================
  /**
   * @var bool|array Allows anonymous access to this controller's actions. The actions must be in 'kebab-case'
   * @access protected
   */
  protected $allowAnonymous = ['index'];


    
  // Public Methods
  // =========================================================================
  /**
   * Get all entries in the REX database.
   * @return mixed
   */
  public function actionIndex()
  {
    $success = CraftRex::getInstance()->RexSyncService->syncRexListings();
    if ($success) {
      $entries = CraftRex::getInstance()->RexListingService->findAll();
      return $this->asJson($entries);
    }
  }
}
