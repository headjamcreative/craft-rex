<?php
/**
 * Craft REX plugin for Craft CMS 3.x
 *
 * A plugin that syncs REX data with Craft.
 *
 * @link      https://www.headjam.com.au
 * @copyright Copyright (c) 2020 Ben Norman
 */

namespace headjam\craftrex\services;

use headjam\craftrex\CraftRex;
use headjam\craftrex\models\RexListingModel;

use Craft;
use craft\base\Component;

/**
 * REX Sync Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Ben Norman
 * @package   CraftRex
 * @since     1.0.0
 */
class RexSyncService extends Component
{
  // Public Methods
  // =========================================================================
  /**
   * Retreieve the REST property listings.
   * @param int $limit - The limit to use in the query. Defaults to 100.
   * @param int $offset - The offset to use in the query. Defaults to 0.
   */
  public function syncRexListings(int $limit=100, int $offset=0)
  {
    $result = CraftRex::getInstance()->RexApiService->rexAuthenticatedRequest('POST', 'listings/search', [
      'limit' => $limit,
      'offset' =>  $offset,
      'criteria' => [
        [
          'name' => 'system_publication_status',
          'type' => '=',
          'value' => 'published'
        ]
      ]
    ]);
    if ($result['success'] && $result['data']['result']) {
      foreach ($result['data']['result']['rows'] as $entry) {
        $model = RexListingModel::create();
        $model->listing_id = $entry['id'];
        $model->listing_details = \GuzzleHttp\json_encode($entry);
        $model->listing_status = $entry['system_listing_state'];
        CraftRex::getInstance()->RexListingService->save($model);
      }
      if ($limit + $offset < $result['data']['result']['total']) {
        return $this->syncRexListings($limit, $limit + $offset);
      }
      return true;
    }
    return false;
  }

  /** 
   * Sync a single REX listing by listing id.
   * @param int $listingId - The ID to query REX by.
   */
  public function syncRexListing(int $listingIdd)
  {
    $result = CraftRex::getInstance()->RexApiService->rexAuthenticatedRequest('POST', 'listings/read', [ 'id' => $listingIdd ]);
    if ($result['success'] && $result['data']['result']) {
      $entry = $result['data']['result'];
      $model = RexListingModel::create();
      $model->listing_id = $entry['id'];
      $model->listing_details = \GuzzleHttp\json_encode($entry);
      $model->listing_status = $entry['system_listing_state'];
      CraftRex::getInstance()->RexListingService->save($model);
      return true;
    }
    return false;
  }
}
