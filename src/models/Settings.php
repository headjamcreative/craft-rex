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

use Craft;
use craft\base\Model;

/**
 * CraftRex Settings Model
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Ben Norman
 * @package   CraftRex
 * @since     1.0.2
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================
    /**
     * The id of the agency performing the REX query.
     *
     * @var string
     */
    public $rexAgencyId = '';

    /**
     * The username to log into REX.
     *
     * @var string
     */
    public $rexUsername = '';

    /**
     * The password to log into REX.
     *
     * @var string
     */
    public $rexPassword = '';

    /**
     * How many seconds to wait between each sync. Recommended minimum of 60.
     *
     * @var int
     */
    public $rexFrequency = 60;



    // Public Methods
    // =========================================================================
    /**
     * @return string the parsed agency id
     */
    public function getRexAgencyId(): string
    {
      return Craft::parseEnv($this->rexAgencyId);
    }

    /**
     * @return string the parsed username
     */
    public function getRexUsername(): string
    {
      return Craft::parseEnv($this->rexUsername);
    }

    /**
     * @return string the parsed password
     */
    public function getRexPassword(): string
    {
      return Craft::parseEnv($this->rexPassword);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['rexAgencyId', 'string'],
            ['rexAgencyId', 'required'],
            ['rexUsername', 'string'],
            ['rexPassword', 'string'],
            ['rexFrequency', 'number'],
            ['rexFrequency', 'required'],
            ['rexFrequency', 'default', 'value' => 60],
        ];
    }
}
