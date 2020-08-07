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
 * the {{ craft }} global variable (e.g. {{ craft.craftrex }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Ben Norman
 * @package   CraftRex
 * @since     1.0.2
 */
class RexListingVariable
{
  // Public Methods
  // =========================================================================
  /** 
   * Get a specific property listing by ID.
   * @param int $id - The listing ID to query search for.
   * @return RexListingModel
   */
  public function listing(int $id)
  {
    return CraftRex::getInstance()->RexListingService->findById($id);
  }

  /** 
   * Get all property listings.
   * @param string $status - Optional. The status to query listings by.
   * @return RexListingModel[]
   */
  public function listings(?string $status = null)
  {
    return CraftRex::getInstance()->RexListingService->findAll($status);
  }
} 