<?php

namespace devanych\cart\tests;

use devanych\cart\tests\data\DummyProduct;
use devanych\cart\CartItem;

class SessionStorageTest extends TestCase
{
    private $params = [
        'key' => 'cartTest',
        'productFieldId' => 'id',
        'productFieldPrice' => 'price',
    ];

    public function load()
    {
        return isset($_SESSION[$this->params['key']]) ? unserialize($_SESSION[$this->params['key']]) : [];
    }

    public function save(array $items)
    {
        $_SESSION[$this->params['key']] = serialize($items);
    }

    public function testCreate()
    {
        $this->assertEquals([], $this->load());
    }

    public function testStore()
    {
        $product = new DummyProduct();
        $this->save([5 => new CartItem($product, 3, $this->params)]);

        /** @var CartItem[] $items */
        $items = $this->load();
        $this->assertEquals(1, count($items));
        $this->assertNotNull($items[5]);
        $this->assertEquals(5, $items[5]->getId());
        $this->assertEquals(3, $items[5]->getQuantity());
        $this->assertEquals(100, $items[5]->getPrice());
    }
}
