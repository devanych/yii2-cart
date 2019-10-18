# Расширение корзина для Yii2

Корзина — это обязательный компонент для любого интернет-магазина, но ее функциональность в разных проектах может различаться. На одном сайте корзина работает с сессией, на втором — с кукисами, а на третьем — с базой данных, также не исключено, что со временем хранилище может меняться.

Тоже касается и подсчета стоимости, например: нужно считать цену товара со скидкой в определенный день недели или в какой-то праздник, но это все невозможно предусмотреть при разработке корзины, поэтому должна быть возможность удобной кастомизации в будущем.

Расширение «[devanych/yii2-cart](https://github.com/devanych/yii2-cart)» решает эти проблемы и позволяет очень легко менять хранилища и калькуляторы, дает возможность подключать собственные решения.

Установить расширение через «[Composer](https://getcomposer.org/download/)»:

```
php composer.phar require devanych/yii2-cart "*"
```

или прописать зависимость в разделе `require` в файле `composer.json`:

```
devanych/yii2-cart: "*"
```

и выполнить в терминале `composer update`.

## Конфигурация

В конфигурационном файле Yii2 приложения (`web.php` в «***basic***» и `main.php` в «***advanced***») в секцию `components` помещаем следующий код.

```php
return [
    //...
    'components' => [
        //...
        'cart' => [
            'class' => 'devanych\cart\Cart',
            'storageClass' => 'devanych\cart\storage\SessionStorage',
            'calculatorClass' => 'devanych\cart\calculators\SimpleCalculator',
            'params' => [
                'key' => 'cart',
                'expire' => 604800,
                'productClass' => 'app\model\Product',
                'productFieldId' => 'id',
                'productFieldPrice' => 'price',
            ],
        ],
    ]
    //...
];
```

Свойство `storageClass` отвечает за хранилище, используемое корзиной, по умолчанию это `devanych\cart\storage\SessionStorage`. Сессионное хранилище можно поменять на `devanych\cart\storage\CookieStorage` или на `devanych\cart\storage\DbSessionStorage`.

`DbSessionStorage` использует сессионное хранилище для не авторизованных пользователей и базу данных для авторизованных. Для его использования необходимо применить следующую миграцию.

```
php yii migrate --migrationPath=@vendor/devanych/yii2-cart/migrations
```

Если этих хранилищ окажется недостаточно, то вы можете создать собственное. Для этого необходимо реализовать интерфейс `devanych\cart\storage\StorageInterface` и указать свой созданный класс значением свойства `storageClass`.

Свойству `calculatorClass` присвоено имя класса калькулятора `devanych\cart\calculators\SimpleCalculator`, этот класс подсчитывает общую стоимость и количество всех элементов корзины. Для реализации собственного калькулятора нужно реализовать интерфейс `devanych\cart\calculators\CalculatorInterface`.

Разберем все дополнительные настройки, находящиеся в подмассиве `params`:

* `key` — имя необходимое для сессии и куки (по умолчанию — `cart`);
* `expire` — срок жизни cookie (по умолчанию — `604800`, т.е. неделя);
* `productClass` — класс товара ActiveRecord модели (по умолчанию — `app\model\Product`);
* `productFieldId` — первичный ключ модели товара (по умолчанию — `id`);
* `productFieldId` — свойство (поле в БД) цены модели товара (по умолчанию — `price`).

Вы можете использовать несколько компонентов корзины, например, хранить еще избранные товары:

```php
//...
'cart' => [
    'class' => 'devanych\cart\Cart',
    'storageClass' => 'devanych\cart\storage\SessionStorage',
    'calculatorClass' => 'devanych\cart\calculators\SimpleCalculator',
    'params' => [
        'key' => 'cart',
        'expire' => 604800,
        'productClass' => 'app\model\Product',
        'productFieldId' => 'id',
        'productFieldPrice' => 'price',
    ],
],
'favorite' => [
    'class' => 'devanych\cart\Cart',
    'storageClass' => 'devanych\cart\storage\DbSessionStorage',
    'calculatorClass' => 'devanych\cart\calculators\SimpleCalculator',
    'params' => [
        'key' => 'favorite',
        'expire' => 604800,
        'productClass' => 'app\models\Product',
        'productFieldId' => 'id',
        'productFieldPrice' => 'price',
    ],
],
//...
```

## Использование

Подключение корзины как компонента дает возможность обращения к ней практически из любого места приложения, используя сервис локатор `Yii::$app`.

Использование корзины:

```php
// Товар, объект AR модели 
$product = Product::findOne(1);

// Компонент корзины
$cart = \Yii::$app->cart;

// Создает элемент корзины из переданного товара и его кол-ва
$cart->add($product, $quantity);

// Добавляет кол-во существующего элемента корзины
$cart->plus($product->id, $quantity);

// Изменяет кол-во существующего элемента корзины
$cart->change($product->id, $quantity);

// Удаляет конкретный элемент из корзины, объект `devanych\cart\CartItem`
$cart->remove($product->id);

// Удаляет все элемент из корзины
$cart->clear();

// Получает все элемент из корзины
$cart->getItems();

// Получает конкретный элемент из корзины
$cart->getItem($product->id);

// Получает идентификаторы всех элементов
$cart->getItemIds();

// Получает общую стоимость всех элементов
$cart->getTotalCost();

// Получает общее количество всех элементов
$cart->getTotalCount();
```

Использование элементов корзины:

```php
// Товар, объект AR модели 
$product = Product::findOne(1);

// Компонент корзины
$cart = \Yii::$app->cart;

// Получает конкретный элемент из корзины, объект `devanych\cart\CartItem`
$item = $cart->getItem($product->id);

// Получает идентификатор элемента равному идентификатор товара
$item->getId();

// Получает цену элемента равную цене товара
$item->getPrice();

// Получает товар, объект AR модели
$item->getProduct();

// Получает общую стоимость товара хранящегося в элементе по его количеству
$item->getCost();

// Получает общее кол-во товара хранящегося в элементе корзины 
$item->getQuantity();

// Устанавливает кол-во товара хранящегося в элементе корзины
$item->setQuantity($quantity);
```

Стоит отметить, что метод `getProduct()` элемента корзины (`devanych\cart\CartItem`) возвращает полноценный объект ActiveRecord модели, это очень удобно, если нужно вывести какую-то информацию о хранящемся в корзине товаре.

## Простая реализация контроллера и представления

Данное расширение дает возможность реализации любого представления для корзины. Я специально не стал делать дефолтный контроллер и представление, так как в каждом проекте корзина реализовывается по-разному: где-то просто, где-то без перезагрузки страницы, где-то в модальном окне, и т.д., а это значит, что и код будет отличаться.

В самом примитивном варианте, если «запихать» все в контроллер (хотя так делать не нужно `;-))`, класс контроллера будет выглядеть так.

```php
namespace app\controllers;

use Yii;
use yii\helpers\Html;
use yii\web\Controller;
use app\models\Product;

class CartController extends Controller
{
    /**
     * @var \devanych\cart\Cart $cart
     */
    private $cart;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->cart = Yii::$app->cart;
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'cart' => $this->cart,
        ]);
    }

    public function actionAdd($id, $qty = 1)
    {
        try {
            $product = $this->getProduct($id);
            $quantity = $this->getQuantity($qty, $product->quantity);
            if ($item = $this->cart->getItem($product->id)) {
                $this->cart->plus($item->getId(), $quantity);
            } else {
                $this->cart->add($product, $quantity);
            }
        } catch (\DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    public function actionChange($id, $qty = 1)
    {
        try {
            $product = $this->getProduct($id);
            $quantity = $this->getQuantity($qty, $product->quantity);
            if ($item = $this->cart->getItem($product->id)) {
                $this->cart->change($item->getId(), $quantity);
            }
        } catch (\DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    public function actionRemove($id)
    {
        try {
            $product = $this->getProduct($id);
            $this->cart->remove($product->id);
        } catch (\DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    public function actionClear()
    {
        $this->cart->clear();
        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return Product the loaded model
     * @throws \DomainException if the product cannot be found
     */
    private function getProduct($id)
    {
        if (($product = Product::findOne((int)$id)) !== null) {
            return $product;
        }
        throw new \DomainException('Товар не найден');
    }

    /**
     * @param integer $qty
     * @param integer $maxQty
     * @return integer
     * @throws \DomainException if the product cannot be found
     */
    private function getQuantity($qty, $maxQty)
    {
        $quantity = (int)$qty > 0 ? (int)$qty : 1;
        if ($quantity > $maxQty) {
            throw new \DomainException('Товара в наличии всего ' . Html::encode($maxQty) . ' шт.');
        }
        return $quantity;
    }
}
```

Ну и осталось создать представление `index.php`, в которое передается корзина, пройтись по ней в цикле и вывести информацию о добавленных товарах либо использовать `GridView`.

```html
<?php
/* @var $this yii\web\View */
/* @var $cart \devanych\cart\Cart */
/* @var $item \devanych\cart\CartItem */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php if(!empty($cartItems = $cart->getItems())): ?>
	<div class="table-responsive">
    	<table class="table">
	        <thead>
		        <tr class="active">
		            <th>Фото</th>
		            <th>Наименование</th>
		            <th>Кол-во</th>
		            <th>Цена</th>
		            <th>Сумма</th>
		            <th><i aria-hidden="true">&times;</i></th>
		        </tr>
	        </thead>
	        <tbody>
	        <?php foreach($cartItems as $item): ?>
	            <tr>
	                <td><?=Html::img("@web{$item->getProduct()->photo}", ['alt' => $item->getProduct()->name, 'width' => 50])?></td>
	                <td><a href="<?=Url::to('@web/product/' . $item->getProduct()->alias)?>"><?= $item->getProduct()->name ?></a></td>
	                <td><?=$item->getQuantity()?></td>
	                <td><?=$item->getPrice()?></td>
	                <td><?=$item->getCost()?></td>
	                <td><a href="<?=Url::to(['cart/remove', 'id' => $item->getId()])?>">Удалить</a></td>
	            </tr>
	        <?php endforeach; ?>
		        <tr class="active">
		            <td colspan="4">Общее кол-во:</td>
		            <td colspan="2"><?= $cart->getTotalCount()?></td>
		        </tr>
		        <tr class="active">
		            <td colspan="4">Общая сумма:</td>
		            <td colspan="2"><?=$cart->getTotalCost() ?></td>
		        </tr>
            </tbody>
        </table>
    </div>
<?php else:?>
    <h3>Корзина пуста</h3>
<?php endif;?>
```
