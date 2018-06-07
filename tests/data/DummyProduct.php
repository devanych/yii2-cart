<?php

namespace devanych\cart\tests\data;
/**
 * Class Product
 *
 * @property int $id
 * @property string $price
 */
class DummyProduct extends \yii\db\ActiveRecord
{
    public $id = 5;
    public $price = 100;
}
