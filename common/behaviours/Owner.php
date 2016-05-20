<?php

namespace common\behaviours;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class Owner extends Behavior
{
    public $ownerIdAttribute = 'ownerId';

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'getOwnerId'
        ];
    }

    public function getOwnerId(yii\base\Event $event)
    {
        if (!$this->owner->{$this->ownerIdAttribute}) {
            $this->owner->{$this->ownerIdAttribute} = Yii::$app->user->getId();
        }
    }
}