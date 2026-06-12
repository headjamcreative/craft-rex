<?php
/**
 * Craft REX plugin for Craft CMS 3.x
 *
 * A plugin that syncs REX data with Craft.
 *
 * @link      https://www.headjam.com.au
 * @copyright Copyright (c) 2020 Ben Norman
 */

namespace headjam\craftrex\variables;

use headjam\craftrex\CraftRex;
use headjam\craftrex\models\RexListingModel;

use Craft;

/**
 * Craft REX Variable
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Ben Norman
 * @package   CraftRex
 * @since     1.1.0
 */
class CraftRexVariable
{
  // Public Methods
  // =========================================================================
  /**
   * Get a specific property listing by ID.
   * @param int $id - The listing ID to query search for.
   * @param bool $refresh - Optional. If true, will query fresh results from the api.
   * @return RexListingModel
   */
  public function listing(int $id, ?bool $refresh=false)
  {
    return CraftRex::getInstance()->RexListingService->findById($id, $refresh);
  }

  /**
   * Get all property listings.
   * @param string $status - Optional. Filter by listing status.
   * @param bool $refresh - Optional. If true, will query fresh results from the api.
   * @param string $type - Optional. Filter by "Sale" or "Rental".
   * @return RexListingModel[]
   */
  public function listings(?string $status=null, ?bool $refresh=false, ?string $type=null)
  {
    return CraftRex::getInstance()->RexListingService->findAll($status, $refresh, $type);
  }

  /**
   * Return the most recent published listings.
   * @param int $count - The number of recent listings to return.
   * @param string $type - Optional. Filter by "Sale" or "Rental".
   * @return RexListingModel[]
   */
  public function recentPublishedListings(int $count=4, ?string $type=null)
  {
    return CraftRex::getInstance()->RexListingService->findRecent(true, $count, $type);
  }

  /**
   * Return the most recent sold listings.
   * @param int $count - The number of recent listings to return.
   * @param string $type - Optional. Filter by "Sale" or "Rental".
   * @return RexListingModel[]
   */
  public function recentSoldListings(int $count=4, ?string $type=null)
  {
    return CraftRex::getInstance()->RexListingService->findRecent(false, $count, $type);
  }
}
