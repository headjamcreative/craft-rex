<?php
/**
 * Craft REX plugin for Craft CMS 3.x
 *
 * A plugin that syncs REX data with Craft.
 *
 * @link      https://www.headjam.com.au
 * @copyright Copyright (c) 2020 Ben Norman
 */

namespace headjam\craftrex\models;

use headjam\craftrex\CraftRex;

use Craft;
use craft\base\Model;

/**
 * RexListingModel Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @property int    $id
 * @property int    $listing_id
 * @property string $listing_details
 * @property string $listing_status
 *
 * @author    Ben Norman
 * @package   CraftRex
 * @since     1.0.2
 */
class RexListingModel extends Model
{
  // Public Properties
  // =========================================================================
  /**
   * The auto-generated database id.
   *
   * @var int
   */
  public $id;

  /**
   * The REX id of the listing.
   *
   * @var int
   */
  public $listing_id;

  /**
   * The JSON-formatted listing details.
   *
   * @var string
   */
  public $listing_details;

  /**
   * The  status of the listing.
   *
   * @var string
   */
  public $listing_status;



  // Public Methods
  // =========================================================================
  /**
   * Factory Method
   *
   * @return RexListingModel
   */
  public static function create($entry): RexListingModel
  {
    $model = new self();
    if ($entry) {
      $model->listing_id = $entry['id'];
      $model->listing_details = $entry;
      $model->listing_status = $entry['system_listing_state'];
    }
    return $model;
  }
}
