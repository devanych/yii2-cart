<?php

namespace devanych\cart\tests;

use devanych\cart\tests\data\DummyProduct;
use devanych\cart\tests\data\DummyStorage;
use devanych\cart\Cart;

class CartTest extends TestCase
{
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var DummyProduct
     */
    private $product;

    public function setUp()
    {
        parent::setUp();
        $this->cart = \Yii::$app->cart;
        $this->product = new DummyProduct();
    }

    public function testCreate()
    {
        $this->assertEquals([], \Yii::$app->cart->getItems());
    }

    public function testAdd()
    {
        $this->cart->add($this->product, 3);
        $this->assertEquals(1, count($items = $this->cart->getItems()));
        $this->assertEquals(5, $items[5]->getId());
        $this->assertEquals(3, $items[5]->getQuantity());
        $this->assertEquals(100, $items[5]->getPrice());
    }

    public function testAddExist()
    {
        $this->cart->add($this->product, 3);
        $this->cart->add($this->product, 4);
        $this->assertEquals(1, count($items = $this->cart->getItems()));
        $this->assertEquals(7, $items[5]->getQuantity());
    }

    public function testRemove()
    {
        $this->cart->add($this->product, 3);
        $this->cart->remove(5);
        $this->assertEquals([], $this->cart->getItems());
    }

    public function testClear()
    {
        $this->cart->add($this->product, 3);
        $this->cart->clear();
        $this->assertEquals([], $this->cart->getItems());
    }

    public function testTotalCost()
    {
        $this->cart->add($this->product, 3);
        $this->cart->add($this->product, 7);
        $this->assertEquals(1000, $this->cart->getTotalCost());
    }

    public function testTotalCount()
    {
        $this->cart->add($this->product, 3);
        $this->cart->add($this->product, 7);
        $this->assertEquals(10, $this->cart->getTotalCount());
    }
}
