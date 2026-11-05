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
    $rexFrequency = CraftRex::getInstance()->getSettings()->rexFrequency;
    $rexLastSync = (int) \Craft::$app->cache->get('craftrex_last_sync') ?: 0;
    $now = time();
    if ($now > ($rexLastSync + $rexFrequency)) {
      CraftRex::getInstance()->RexSyncService->syncRexListings(50);
    }
    return $this->actionStoredResults();
  }

  /**
   * Query the most recently modified live results of the REX endpoint and sync them.
   */
  public function actionSync()
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
   * Optional `limit` param overrides the default cap (100 for non-current statuses); 0 means no limit.
   */
  public function actionStoredResults()
  {
    $type = \Craft::$app->request->getParam('type');
    $status = \Craft::$app->request->getParam('status');
    $service = CraftRex::getInstance()->RexListingService;
    $limit = \Craft::$app->request->getParam('limit');
    $limit = is_numeric($limit) ? max(0, (int) $limit) : null;
    $entries = $service->findAll($status, false, $type, $limit);
    $response = $this->asJson($entries);
    // The body stays a plain array; the uncapped total is exposed via a header.
    $response->headers->set('X-Total-Count', (string) $service->countAll($status, $type));
    $response->headers->set('Access-Control-Expose-Headers', 'X-Total-Count');
    return $response;
  }
}
