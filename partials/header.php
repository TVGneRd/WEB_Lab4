<?php
include_once "src/connection.php";
include_once "src/cart.php";
include_once "src/auth.php";

$cart_products = getProductsFromCart();
$user          = getCurrentUser();

?>

<header class="bg-white fixed top-0 left-0 right-0 z-10 shadow-md shadow-b ">
    <nav class="mx-auto flex container items-center justify-between py-6" aria-label="Global">
        <div class="flex lg:flex-1 gap-6 items-center">
            <a href="index.php" class="-m-1.5 p-1.5 flex gap-3 items-center">
                <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&amp;shade=600"
                    alt="">
                <span class="text-md text-gray-900 font-bold">GeekMac</span>
            </a>
            <a class="text-sm cursor-pointer font-semibold leading-6 text-gray-900" href="contacts.php">Контакты</a>
            <a class="text-sm cursor-pointer font-semibold leading-6 text-gray-900" href="news.php">Новости</a>

            <? if ($user) : ?>
                <a class="text-sm cursor-pointer font-semibold leading-6 text-gray-900" href="cabinet.php">Кабинет</a>
            <? endif; ?>

        </div>

        <div class="flex flex-1 justify-end gap-3">
            <a class="mr-4 pb-4 border-b-2 border-gray-300" href="cart.php">
                Корзина
                <?= "(" . count($cart_products) . ")" ?>
            </a>
            <div id="AuthLinks" class="flex gap-3">
                <? if (! $user) : ?>
                    <a class="text-sm cursor-pointer font-semibold leading-6 text-gray-900" href="login.php">Войти</a>
                    |
                    <a class="text-sm cursor-pointer font-semibold leading-6 text-gray-900"
                        href="register.php">Зарегистрироваться</a>
                <? else : ?>

                    <span class="text-sm cursor-pointer font-semibold leading-6 text-gray-900"
                        href="register.php">Здравствуйте, <?= $user['name'] ?>!</span>
                <? endif; ?>

            </div>

        </div>
    </nav>

</header>