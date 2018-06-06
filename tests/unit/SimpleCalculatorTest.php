<?php

namespace devanych\cart\tests\unit;

use devanych\cart\calculators\SimpleCalculator;
use devanych\cart\tests\dummy\DummyProduct;
use devanych\cart\CartItem;
use Codeception\Test\Unit;

class SimpleCalculatorTest extends Unit
{
    private $params = [
        'productFieldId' => 'id',
        'productFieldPrice' => 'price',
    ];

    public function testCostCalculate()
    {
        $calculator = new SimpleCalculator();
        $product = new DummyProduct();
        $this->assertEquals(300, $calculator->getCost([
            $product->id => new CartItem($product, 3, $this->params),
        ]));
    }

    public function testCountCalculate()
    {
        $calculator = new SimpleCalculator();
        $product = new DummyProduct();
        $this->assertEquals(3, $calculator->getCount([
            $product->id => new CartItem($product, 3, $this->params),
        ]));
    }
}
