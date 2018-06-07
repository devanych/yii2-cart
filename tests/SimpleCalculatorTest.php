<?php

namespace devanych\cart\tests;

use devanych\cart\calculators\SimpleCalculator;
use devanych\cart\tests\data\DummyProduct;
use devanych\cart\CartItem;

class SimpleCalculatorTest extends TestCase
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
