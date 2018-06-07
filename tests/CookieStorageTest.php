<?php

namespace devanych\cart\tests;

use devanych\cart\tests\data\DummyProduct;
use devanych\cart\CartItem;
use yii\helpers\Json;
use yii\web\Cookie;

class CookieStorageTest extends TestCase
{
    private $cookie;

    private $params = [
        'key' => 'cartTest',
        'productFieldId' => 'id',
        'productFieldPrice' => 'price',
    ];

    public function load()
    {
        if (!empty($this->cookie)) {
            return array_filter(array_map(function (array $row) {
                if (isset($row['id'], $row['quantity'])) {
                    $product = new DummyProduct();
                    return new CartItem($product, $row['quantity'], $this->params);
                }
                return false;
            }, Json::decode($this->cookie->value)));
        }
        return [];
    }

    public function save(array $items)
    {
        $this->cookie = new Cookie([
            'name' => $this->params['key'],
            'value' => Json::encode(array_map(function (CartItem $item) {
                return [
                    'id' => $item->getId(),
                    'quantity' => $item->getQuantity(),
                ];
            }, $items)),
            'expire' => time() + 3600,
        ]);
    }

    public function testCreate()
    {
        $this->assertEquals([], $this->load());
    }

    public function testStore()
    {
        $product = new DummyProduct();
        $this->save([$product->id => new CartItem($product, 3, $this->params)]);

        /** @var CartItem[] $items */
        $items = $this->load();
        $this->assertEquals(1, count($items));
        $this->assertNotNull($items[5]);
        $this->assertEquals(5, $items[5]->getId());
        $this->assertEquals(3, $items[5]->getQuantity());
        $this->assertEquals(100, $items[5]->getPrice());
    }
}
