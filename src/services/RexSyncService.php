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
 * @since     1.0.2
 */
class RexSyncService extends Component
{
  // Public Methods
  // =========================================================================
  /**
   * Retreieve the REST property listings.
   * @param int $limit - The limit to use in the query. Defaults to 100.
   * @param int $offset - The offset to use in the query. Defaults to 0.
   * @param bool $all - If true, will attempt to query all listings. Defaults to false.
   */
  public function syncRexListings(?int $limit=100, ?int $offset=0, ?bool $all=false)
  {
    $entries = CraftRex::getInstance()->RexApiService->findAll($limit, $offset, $all);
    foreach ($entries as $entry) {
      if ($entry && $entry instanceof RexListingModel) {
        CraftRex::getInstance()->RexListingService->save($entry);
      }
    }
    if (!$all) {
      CraftRex::getInstance()->rexLastSync = time();
    }
    return $entries;
  }

  /**
   * Sync a single REX listing by listing id.
   * @param int $listingId - The ID to query REX by.
   */
  public function syncRexListing(int $listingId)
  {
    $entry = CraftRex::getInstance()->RexApiService->findById($listingId);
    if ($entry && $entry instanceof RexListingModel) {
      CraftRex::getInstance()->RexListingService->save($entry);
    }
    return $entry;
  }
}
