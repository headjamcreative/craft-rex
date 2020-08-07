<?php
/**
 * Craft REX plugin for Craft CMS 3.x
 *
 * A plugin that syncs REX data with Craft.
 *
 * @link      https://www.headjam.com.au
 * @copyright Copyright (c) 2020 Ben Norman
 */

namespace headjam\craftrex\records;

use headjam\craftrex\CraftRex;

use Craft;
use craft\db\ActiveRecord;

/**
 * RexListingRecord Record
 *
 * ActiveRecord is the base class for classes representing relational data in terms of objects.
 *
 * Active Record implements the [Active Record design pattern](http://en.wikipedia.org/wiki/Active_record).
 * The premise behind Active Record is that an individual [[ActiveRecord]] object is associated with a specific
 * row in a database table. The object's attributes are mapped to the columns of the corresponding table.
 * Referencing an Active Record attribute is equivalent to accessing the corresponding table column for that record.
 *
 * http://www.yiiframework.com/doc-2.0/guide-db-active-record.html
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
class RexListingRecord extends ActiveRecord
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
   * The status of the listing.
   *
   * @var string
   */
  public $listing_status;

  // Public Static Methods
  // =========================================================================
  /**
   * Declares the name of the database table associated with this AR class.
   * By default this method returns the class name as the table name by calling [[Inflector::camel2id()]]
   * with prefix [[Connection::tablePrefix]]. For example if [[Connection::tablePrefix]] is `tbl_`,
   * `Customer` becomes `tbl_customer`, and `OrderItem` becomes `tbl_order_item`. You may override this method
   * if the table is not named after this convention.
   *
   * By convention, tables created by plugins should be prefixed with the plugin
   * name and an underscore.
   *
   * @return string the table name
   */
  public static function tableName()
  {
    return '{{%craftrex_rexlistingrecord}}';
  }

  // Public Methods
  // =========================================================================
  /**
   * Factory Method
   *
   * @return RexListingRecord
   */
  public static function create(): RexListingRecord
  {
    return new self();
  }
}
