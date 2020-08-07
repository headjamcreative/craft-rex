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

use Craft;
use craft\base\Component;
use headjam\craftrex\events\RexListing\SaveEvent;
use headjam\craftrex\models\RexListingModel;
use headjam\craftrex\records\RexListingRecord;

/**
 * REX Listing Service
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
class RexListingService extends Component
{
  // Private Properties
  // =========================================================================
  const EVENT_AFTER_SAVE = 'afterSave';
  const EVENT_BEFORE_SAVE = 'beforeSave';



  // Public Methods
  // =========================================================================
  /** 
   * @var RexListingModel[]
   */
  private static $listingsById = [];

  

  // Public Methods
  // =========================================================================
  /**
   * @param RexListingModel $model
   *
   * @return bool
   * @throws \Exception
   */
  public function save(RexListingModel $model): bool
  {
    $isNew = !$model->id;
    if (!$isNew) {
      $record = RexListingRecord::findOne(['id' => $model->id]);
    } else {
      $record = RexListingRecord::findOne(['listing_id' => $model->listing_id]);
    }

    if (!$record) {
      $record = RexListingRecord::create();
    }

    $record->setAttribute('listing_id', $model->listing_id);
    $record->setAttribute('listing_details', $model->listing_details);
    $record->setAttribute('listing_status', $model->listing_status);
    $record->validate();
    $model->addErrors($record->getErrors());

    $beforeSaveEvent = new SaveEvent($model, $isNew);
    $this->trigger(self::EVENT_BEFORE_SAVE, $beforeSaveEvent);

    if ($beforeSaveEvent->isValid && !$model->hasErrors()) {
      $transaction = null;
      if (!\Craft::$app->getDb()->getTransaction()) {
        $transaction = \Craft::$app->getDb()->getTransaction() ?? \Craft::$app->getDb()->beginTransaction();
      }
      try {
        $record->save(false);
        if ($isNew) {
          $model->id = $record->id;
        }
        self::$listingsById[$model->id] = $model;
        if ($transaction !== null) {
          $transaction->commit();
        }
        $this->trigger(self::EVENT_AFTER_SAVE, new SaveEvent($model, $isNew));
        return true;
      } catch (\Exception $e) {
        if ($transaction !== null) {
          $transaction->rollBack();
        }
        throw $e;
      }
    }
    return false;
  }
}
