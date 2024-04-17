<?php
include_once "src/connection.php";
include_once "src/cart.php";
include_once "src/auth.php";

$user = getCurrentUser();

if (isset($_POST['remove'])) {
    cart($_POST['productId'], 0);
}
$products   = getProductsFromCart();
$totalPrice = array_reduce($products, fn ($prev, $product) => $prev + $product['price'], 0);

if (isset($_POST['submitOrder'])) {
    $mysqli->execute_query("INSERT INTO `orders`( `price`, `delivery_type`, `delivery_address`, `user_id`) VALUES (?, ?, ?, ?)", [ $totalPrice, $_POST['DeliveryType'], $_POST['DeliveryAddress'], $user['id'] ]);
    $order_id = mysqli_insert_id($mysqli);

    foreach ($products as $product) {
        $mysqli->execute_query("INSERT INTO `order_product`(`order_id`, `product_id`, `price`, `amount`) VALUES (?, ?, ?, ?)", [ $order_id, $product['id'], $product['price'], 1 ]);
    }

    setcookie('cart_products', "", -1);

    header('Location: cabinet.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? include('partials/head.php') ?>
</head>

<body>
    <? include('partials/header.php') ?>

    <main class="mt-[80px] min-h-[calc(100vh_-_160px)]">
        <div class="mx-auto container pb-8 mt-6">
            <h1 id="catalogTitle" class="font-bold text-4xl mb-6 pt-6">Корзина</h1>

            <div class="">
                <?php foreach ($products as $product) : ?>
                    <form method="post"
                        class="grid grid-cols-12 gap-3 mt-8 bg-white border border-gray-200 rounded-lg shadow p-6 items-center">

                        <img id="CartGridView_Image1_0" class="h-24 object-contain col-span-2"
                            src="images/<?= $product['image'] ?>" style="width:300px;">
                        <span class="col-span-3 text-xl">
                            <?= $product['name'] ?>
                        </span>

                        <span class="col-span-2 col-start-9 text-xl">Цена: <b>
                                <?= $product['price'] ?>₽
                            </b></span>

                        <input type="hidden" name="productId" value="<?= $product['id'] ?>">
                        <input type="hidden" name="amount" value="<?= $product['cart_value'] ?>">

                        <input type="submit" name="remove" value="Убрать"
                            class="bg-red-500 col-span-2 text-white px-8 py-3 h-fit text-xl rounded-lg">

                    </form>
                <?php endforeach; ?>

            </div>

            <div class="flex justify-end text-3xl mt-6">
                Итог: <b id="totalPrice" class="font-bold ml-3">
                    <?= $totalPrice ?> ₽
                </b>
            </div>
            <form method="POST">
                <div class="mt-8 bg-white border border-gray-200 rounded-lg shadow p-6 items-center">

                    <h2 class="text-2xl mb-6">Детали заказа</h2>

                    <div class="grid grid-cols-12 gap-3">
                        <div class="col-span-6">
                            <label for="DeliveryType" class="block mb-2 text-sm font-medium text-gray-900">Тип
                                доставки</label>
                            <select name="DeliveryType" id="DeliveryType"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option selected="selected" value="0">Доставка</option>
                                <option value="1">Самовывоз</option>
                            </select>
                        </div>
                        <div id="DeliveryPickUpBlock" class="col-span-6" hidden>
                            <label for="DeliveryPickUp" class="block mb-2 text-sm font-medium text-gray-900">Пункт
                                самовяза</label>

                            <div>
                                г. Санкт-Петербург ул. Зачетная д. 5
                            </div>

                        </div>

                        <div id="DeliveryAddressBlock" class="col-span-12">
                            <label for="DeliveryAddress"
                                class="block mb-2 text-sm font-medium text-gray-900">Адрес</label>
                            <textarea name="DeliveryAddress" rows="3" cols="20" id="DeliveryAddress"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="г. Мосвка ул. Пушкина д. 42 кв. 15"></textarea>
                        </div>

                    </div>
                </div>

                <div class="flex justify-end text-3xl mt-6">
                    <input type="submit" name="submitOrder" value="Оформить заказ" id="submitOrder"
                        class="bg-blue-500 col-span-2 text-white px-8 py-3 h-fit text-xl rounded-lg data-[added=true]:bg-red-500">
                </div>
            </form>
        </div>
    </main>

    <?php include('partials/footer.php') ?>

    <script>
        document.getElementById('DeliveryType').addEventListener('change', (e) => {
            document.getElementById('DeliveryAddressBlock').hidden = Boolean(Number(e.target.value));
            document.getElementById('DeliveryPickUpBlock').hidden = !Boolean(Number(e.target.value));
        });
    </script>
</body>

</html>