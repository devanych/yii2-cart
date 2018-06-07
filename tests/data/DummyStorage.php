<?php

namespace devanych\cart\tests\data;

use devanych\cart\storage\StorageInterface;

class DummyStorage implements StorageInterface
{
    private $items = [];
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function load()
    {
        return $this->items;
    }

    public function save(array $items)
    {
        $this->items = $items;
    }
}
