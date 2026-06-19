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
use headjam\craftrex\events\RexListing\SaveEvent;
use headjam\craftrex\models\RexListingModel;
use headjam\craftrex\records\RexListingRecord;

use Craft;
use craft\base\Component;
use craft\db\Query;

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
 * @since     1.0.2
 */
class RexListingService extends Component
{
  // Private Properties
  // =========================================================================
  const EVENT_AFTER_SAVE = 'afterSave';
  const EVENT_BEFORE_SAVE = 'beforeSave';

  /**
   * @var RexListingModel[]
   */
  private static $listingsById = [];



  // Private Methods
  // =========================================================================
  /**
   * Returns a data-ready record, or null if no record is supplied.
   * @param RexListingRecord $record - The record to generate data for.
   */
  private function getRecordData(?RexListingRecord $record)
  {
    return $record && $record instanceof RexListingRecord ? [
      'listing_id' => $record->getAttribute('listing_id'),
      'listing_status' => $record->getAttribute('listing_status'),
      'listing_type' => $record->getAttribute('listing_type'),
      'listing_details' => $record->getAttribute('listing_details'),
      'publishDate' => $record->getAttribute('publishDate'),
      'soldDate' => $record->getAttribute('soldDate'),
    ] : null;
  }



  // Public Methods
  // =========================================================================
  /**
   * Saves the given model as an ActivRecord to the database.
   * @param RexListingModel $model
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
    $record->setAttribute('listing_type', $model->listing_type);
    $record->setAttribute('publishDate', $model->publishDate);
    $record->setAttribute('soldDate', $model->soldDate);
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

  /**
   * Find the given listing by id.
   * @param int $listingId - The id to query.
   * @param bool $refresh - If true, will query the api regardless of record status.
   * @return RexListingModel|null
   */
  public function findById(int $listingId, ?bool $refresh=false, ?string $type=null)
  {
    $params = ['listing_id' => $listingId];
    if ($type) {
      $params['listing_type'] = $type;
    }
    $record = RexListingRecord::findOne($params);
    if (!$record || $refresh) {
      $apiResult = CraftRex::getInstance()->RexSyncService->syncRexListing($listingId);
      if ($apiResult) {
        $record = RexListingRecord::findOne($params);
      }
    }

    $data = $this->getRecordData($record);
    return ($data && $data['listing_status'] === 'withdrawn') ? null : $data;
  }

  /**
   * Find all property listings.
   * @param string $status - Optional. The status to query listings by.
   * @param bool $refresh - If true, will query the api regardless of record status.
   * @param string $type - Optional. Filter by "Sale" or "Rental".
   * @return RexListingModel[]
   */
  public function findAll(?string $status=null, ?bool $refresh=false, ?string $type=null)
  {
    if ($refresh) {
      CraftRex::getInstance()->RexSyncService->syncRexListings();
    }
    $query = RexListingRecord::find();
    if ($status) {
      $query->andWhere(['listing_status' => $status]);
    }
    if ($status !== 'withdrawn') {
      $query->andWhere(['!=', 'listing_status', 'withdrawn']);
    }
    if ($type) {
      $query->andWhere(['listing_type' => $type]);
    }
    if ($status && $status != 'current') {
      $query->limit(100);
    }
    return array_map(array($this, 'getRecordData'), $query->all());
  }

  /**
   * Find the most recent property listings.
   * @param bool $current=true - Current results if true, sold results if false.
   * @param int $count=4 - The number of recent listings to return.
   * @param string $type - Optional. Filter by "Sale" or "Rental".
   * @return RexListingModel[]
   */
  public function findRecent(bool $current=true, int $count=4, ?string $type=null)
  {
    $status = $current ? 'current' : 'sold';
    $order = $current ? 'publishDate' : 'soldDate';
    $query = RexListingRecord::find()->where(['listing_status' => $status])->orderBy($order . ' desc')->limit($count);
    if ($type) {
      $query->andWhere(['listing_type' => $type]);
    }
    return array_map(array($this, 'getRecordData'), $query->all());
  }
}
