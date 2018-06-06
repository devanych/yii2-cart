<?php

namespace devanych\cart\storage;

interface StorageInterface
{
    /**
     * @param array $params (configuration params)
     */
    public function __construct(array $params);
    /**
     * @return \devanych\cart\models\CartItem[]
     */
    public function load();
    /**
     * @param \devanych\cart\models\CartItem[] $items
     */
    public function save(array $items);
}
