<?php

namespace devanych\cart\tests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->mockApplication();
    }

    protected function tearDown()
    {
        $this->destroyApplication();
        parent::tearDown();
    }

    protected function mockApplication()
    {
       new \yii\console\Application([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => dirname(__DIR__) . '/vendor',
            'components' => [
                'cart' => [
                    'class' => 'devanych\cart\Cart',
                    'storageClass' => 'devanych\cart\tests\data\DummyStorage',
                    'params' => [
                        'productClass' => 'devanych\cart\tests\data\DummyProduct',
                    ],
                ],
            ],
        ]);
    }

    protected function destroyApplication()
    {
        \Yii::$app = null;
    }
}