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
 * @since     1.0.2
 */
class DefaultController extends Controller
{
  // Protected Properties
  // =========================================================================
  /**
   * @var bool|array Allows anonymous access to this controller's actions. The actions must be in 'kebab-case'
   * @access protected
   */
  protected $allowAnonymous = ['index', 'sync-all', 'live-results', 'all-live-results', 'stored-results'];



  // Public Methods
  // =========================================================================
  /**
   * Query the most recently modified live results of the REX endpoint and sync them.
   */
  public function actionIndex()
  {
    CraftRex::getInstance()->RexSyncService->syncRexListings();
    return true;
  }

  /**
   * Query all the live results of the REX endpoint and sync them.
   */
  public function actionSyncAll()
  {
    CraftRex::getInstance()->RexSyncService->syncRexListings(100, 0, true);
    return true;
  }

  /**
   * Query the live results of the REX endpoint.
   */
  public function actionLiveResults()
  {
    $response = CraftRex::getInstance()->RexApiService->findAll();
    return $this->asJson($response);
  }

  /**
   * Query all the live results of the REX endpoint.
   */
  public function actionAllLiveResults()
  {
    $response = CraftRex::getInstance()->RexApiService->findAll(100, 0, true);
    return $this->asJson($response);
  }

  /**
   * Query the stored listings.
   */
  public function actionStoredResults()
  {
    $entries = CraftRex::getInstance()->RexListingService->findAll();
    return $this->asJson($entries);
  }
}
