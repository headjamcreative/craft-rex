<?php
/**
 * Craft REX plugin for Craft CMS 3.x
 *
 * A plugin that syncs REX data with Craft.
 *
 * @link      https://www.headjam.com.au
 * @copyright Copyright (c) 2020 Ben Norman
 */

namespace headjam\craftrex\migrations;

use Craft;
use craft\db\Migration;

/**
 * Adds listing_type column to craftrex_rexlistingrecord to store the
 * listing_sale_or_rental value ("Sale" or "Rental") from the REX API.
 */
class m260612_145820_add_listing_type extends Migration
{
    public function safeUp()
    {
        if (!$this->db->columnExists('{{%craftrex_rexlistingrecord}}', 'listing_type')) {
            $this->addColumn(
                '{{%craftrex_rexlistingrecord}}',
                'listing_type',
                $this->string(50)->null()->after('listing_status')
            );
        }
        return true;
    }

    public function safeDown()
    {
        if ($this->db->columnExists('{{%craftrex_rexlistingrecord}}', 'listing_type')) {
            $this->dropColumn('{{%craftrex_rexlistingrecord}}', 'listing_type');
        }
        return true;
    }
}
