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
 * CraftRex Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
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
     * The auth token for any requests.
     */
    public $rexAuthToken = '';

    /**
     * The id of the agency performing the REX query.
     *
     * @var number
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
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['rexAgencyId', 'string'],
            ['rexAgencyId', 'required'],
            ['rexUsername', 'string'],
            ['rexUsername', 'required'],
            ['rexUsername', 'default', 'value' => ''],
            ['rexPassword', 'string'],
            ['rexPassword', 'required'],
            ['rexPassword', 'default', 'value' => ''],
        ];
    }
}
