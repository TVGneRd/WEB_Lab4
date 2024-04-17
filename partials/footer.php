<?php
include_once "src/connection.php";
include_once "src/cart.php";
include_once "src/auth.php";

$cart_products = getProductsFromCart();
$user          = getCurrentUser();

?>

<footer class="bg-white shadow-[0_35px_60px_5px_rgba(0,0,0,0.3)]">
    <div class="container mx-auto py-5">
        <div class="flex justify-between">
            <div class="text-lg">
                © GeekMac
                <?= date('Y') ?> год
            </div>

            <div class="text-xl">
                Разработчик: <a href="https://github.com/TVGneRd" class="text-purple-700 underline">Дима Селедкин</a>
            </div>
        </div>
    </div>
</footer>