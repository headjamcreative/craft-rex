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
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.craftRex }}).
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
   * @param string $status - Optional. The status to query listings by.
   * @param bool $refresh - Optional. If true, will query fresh results from the api.
   * @return RexListingModel[]
   */
  public function listings(?string $status=null, ?bool $refresh=false)
  {
    return CraftRex::getInstance()->RexListingService->findAll($status, $refresh);
  }

  /**
   * Return the most recent four published listings.
   * @param int $count=4 - The number of recent listings to return.
   * @return RexListingModel[]
   */
  public function recentPublishedListings(int $count=4)
  {
    return CraftRex::getInstance()->RexListingService->findRecent(true, $count);
  }

  /**
   * Return the most recent four sold listings.
   * @param int $count=4 - The number of recent listings to return.
   * @return RexListingModel[]
   */
  public function recentSoldListings(int $count=4)
  {
    return CraftRex::getInstance()->RexListingService->findRecent(false, $count);
  }
}
