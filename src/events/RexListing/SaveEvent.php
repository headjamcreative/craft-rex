<?php

namespace headjam\craftrex\events\RexListing;

use craft\events\CancelableEvent;
use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use headjam\craftrex\models\RexListingModel;

class SaveEvent extends CancelableEvent implements Arrayable
{
  use ArrayableTrait;

  /** 
   * @var RexListingModel 
   */
  private $model;

  /** 
   * @var bool 
   */
  private $new;

  /**
   * @param RexListingModel $model
   * @param bool $new
   */
  public function __construct(RexListingModel $model, bool $new)
  {
    $this->model = $model;
    $this->new   = $new;
    parent::__construct();
  }

  /**
   * @return RexListingModel
   */
  public function getModel(): RexListingModel
  {
    return $this->model;
  }

  /**
   * @return bool
   */
  public function isNew(): bool
  {
    return $this->new;
  }
}
