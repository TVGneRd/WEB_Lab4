<?php
include_once "src/connection.php";
include_once "src/cart.php";
include_once "src/auth.php";

$user = getCurrentUser();

if (isset($_POST['submit'])) {
    $mysqli->execute_query("UPDATE `users` SET `name` = ?, `email` = ? WHERE id = ?", [ $_POST['name'], $_POST['email'], $user['id'] ]);
    if ($_POST['password']) {
        $mysqli->execute_query("UPDATE `users` SET `password` = ? WHERE id = ?", [ password_hash($_POST['password'], PASSWORD_DEFAULT), $user['id'] ]);
    }
    $user = getCurrentUser();
}

function getOrderStatusColor($status)
{
    $colors = [ 
        'new'       => 'bg-blue-700',
        'accepted'  => 'bg-yellow-700',
        'rejected'  => 'bg-red-700',
        'delivered' => 'bg-green-700',
        'completed' => 'bg-purple-700',
    ];

    return $colors[ $status ];
}

function getOrderText($status)
{
    $statusTexts = [ 
        'new'       => 'Новый',
        'accepted'  => 'Принят',
        'rejected'  => 'Отклонен',
        'delivered' => 'Доставлен',
        'completed' => 'Завершен',
    ];

    return $statusTexts[ $status ];
}

$orders = $mysqli->execute_query("SELECT orders.*, users.*, orders.id as id, GROUP_CONCAT(products.name, ' (', order_product.amount, 'шт.)') as products FROM orders inner join users on users.id = orders.user_id left join order_product on order_product.order_id = orders.id left join products on products.id = order_product.product_id WHERE user_id = ? GROUP BY orders.id order BY orders.id desc", [ $user['id'] ])->fetch_all(MYSQLI_ASSOC) ?? [];
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
            <h1 class="font-bold text-4xl mb-6 pt-6">Личный кабинет</h1>
            <div class="mt-8 rounded-lg bg-white border border-gray-200 shadow p-6">
                <h2 id="H1" class="font-bold text-xl col-span-12">Контактная информация</h2>

                <form method="post" class="w-full mt-3">

                    <div class="grid grid-cols-12 gap-3">

                        <div class="col-span-4">
                            ФИО:
                            <input name="name" type="text" value="<?= $user['name'] ?>" id="FormView1_lastnameTextBox"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>


                        <div class="col-span-4">
                            Почта:
                            <input name="email" type="email" value="<?= $user['email'] ?>" id="FormView1_emailTextBox"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>

                        <div class="col-span-4">
                            Новый пароль:
                            <input name="password" name="password" type="text" placeholder="*******"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <input type="submit" name="submit"
                            class="block mt-6 text-white px-8 py-3 text-xl rounded-lg bg-green-500" value="Сохранить" />
                    </div>

                </form>

            </div>

            <h1 id="catalogTitle" class="font-bold text-3xl mb-6 pt-6">Заказы</h1>

            <div class="mx-auto container pb-8 mt-6">
                <? foreach ($orders as $order) : ?>
                    <div
                        class="flex justify-between gap-3 mt-8 bg-white border border-gray-200 rounded-lg shadow p-6 items-center">
                        <span class="text-xl flex-shrink-0">
                            <?= $order['id'] ?>
                        </span>
                        <span
                            class="text-xl block px-2 rounded flex-shrink-0 text-white <?= getOrderStatusColor($order['status']) ?>">
                            <?= getOrderText($order['status']) ?>
                        </span>
                        <div class="w-full">
                            <div class="flex justify-between gap-3">
                                <span class="text-xl">
                                    <?= date_create($order['updated_at'])->format('d.m.Y H:i:s') ?>
                                </span>
                            </div>
                            <div>
                                <?= $order['products'] ?>
                            </div>
                        </div>

                        <span class="text-xl flex-shrink-0">Сумма: <b>
                                <?= $order['price'] ?> ₽
                            </b></span>
                    </div>
                <? endforeach; ?>



            </div>
        </div>
    </main>

    <?php include('partials/footer.php') ?>

</body>

</html>