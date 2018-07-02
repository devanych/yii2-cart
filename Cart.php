<?php

namespace devanych\cart;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;

class Cart extends BaseObject
{
    /**
     * @var string $storageClass
     */
    public $storageClass = 'devanych\cart\storage\SessionStorage';

    /**
     * @var string $calculatorClass
     */
    public $calculatorClass = 'devanych\cart\calculators\SimpleCalculator';

    /**
     * @var array $params Custom configuration params
     */
    public $params = [];

    /**
     * @var array $defaultParams
     */
    private $defaultParams = [
        'key' => 'cart',
        'expire' => 604800,
        'productClass' => 'app\model\Product',
        'productFieldId' => 'id',
        'productFieldPrice' => 'price',
    ];

    /**
     * @var CartItem[]
     */
    private $items;

    /**
     * @var \devanych\cart\storage\StorageInterface
     */
    private $storage;

    /**
     * @var \devanych\cart\calculators\CalculatorInterface
     */
    private $calculator;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->params = array_merge($this->defaultParams, $this->params);

        if (!class_exists($this->params['productClass'])) {
            throw new InvalidConfigException('productClass `' . $this->params['productClass'] . '` not found');
        }
        if (!class_exists($this->storageClass)) {
            throw new InvalidConfigException('storageClass `' . $this->storageClass . '` not found');
        }
        if (!class_exists($this->calculatorClass)) {
            throw new InvalidConfigException('calculatorClass `' . $this->calculatorClass . '` not found');
        }

        $this->storage = new $this->storageClass($this->params);
        $this->calculator = new $this->calculatorClass();
    }

    /**
     * Add an item to the cart
     * @param object $product
     * @param integer $quantity
     * @return void
     */
    public function add($product, $quantity)
    {
        $this->loadItems();
        if (isset($this->items[$product->{$this->params['productFieldId']}])) {
            $this->plus($product->{$this->params['productFieldId']}, $quantity);
        } else {
            $this->items[$product->{$this->params['productFieldId']}] = new CartItem($product, $quantity, $this->params);
            ksort($this->items, SORT_NUMERIC);
            $this->saveItems();
        }
    }

    /**
     * Adding item quantity in the cart
     * @param integer $id
     * @param integer $quantity
     * @return void
     */
    public function plus($id, $quantity)
    {
        $this->loadItems();
        if (isset($this->items[$id])) {
            $this->items[$id]->setQuantity($quantity + $this->items[$id]->getQuantity());
        }
        $this->saveItems();
    }

    /**
     * Change item quantity in the cart
     * @param integer $id
     * @param integer $quantity
     * @return void
     */
    public function change($id, $quantity)
    {
        $this->loadItems();
        if (isset($this->items[$id])) {
            $this->items[$id]->setQuantity($quantity);
        }
        $this->saveItems();
    }

    /**
     * Removes an items from the cart
     * @param integer $id
     * @return void
     */
    public function remove($id)
    {
        $this->loadItems();
        if (array_key_exists($id, $this->items)) {
            unset($this->items[$id]);
        }
        $this->saveItems();
    }

    /**
     * Removes all items from the cart
     * @return void
     */
    public function clear()
    {
        $this->items = [];
        $this->saveItems();
    }

    /**
     * Returns all items from the cart
     * @return CartItem[]
     */
    public function getItems()
    {
        $this->loadItems();
        return $this->items;
    }

    /**
     * Returns an item from the cart
     * @param integer $id
     * @return CartItem
     */
    public function getItem($id)
    {
        $this->loadItems();
        return isset($this->items[$id]) ? $this->items[$id] : null;
    }

    /**
     * Returns ids array all items from the cart
     * @return array
     */
    public function getItemIds()
    {
        $this->loadItems();
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->getId();
        }
        return $items;
    }

    /**
     * Returns total cost all items from the cart
     * @return integer
     */
    public function getTotalCost()
    {
        $this->loadItems();
        return $this->calculator->getCost($this->items);
    }

    /**
     * Returns total count all items from the cart
     * @return integer
     */
    public function getTotalCount()
    {
        $this->loadItems();
        return $this->calculator->getCount($this->items);
    }

    /**
     * Load all items from the cart
     * @return void
     */
    private function loadItems()
    {
        if ($this->items === null) {
            $this->items = $this->storage->load();
        }
    }

    /**
     * Save all items to the cart
     * @return void
     */
    private function saveItems()
    {
        $this->storage->save($this->items);
    }
}
