<?php
include_once "src/connection.php";

$categories = $mysqli->execute_query("SELECT * FROM category")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? include('partials/head.php') ?>
</head>

<body>
    <? include('partials/header.php') ?>

    <main class="mt-[80px] min-h-[calc(100vh_-_160px)]">
        <section class="relative h-[calc(100vh_-_80px)]">
            <img class="brightness-50 object-cover h-full w-full" src="images/main.jpg">

            <div class="absolute left-0 bottom-24 right-0">
                <div class="container mx-auto flex flex-col">
                    <span class="text-white text-6xl font-bold">GeekMac</span>
                    <span class="text-4xl text-gray-100 ">
                        Интернет магазин электронной техники
                    </span>
                </div>
            </div>
        </section>

        <section class="mx-auto container pb-8 mt-12">

            <h1 class="font-bold text-4xl mb-6">Каталог</h1>

            <div class="grid grid-cols-3 gap-3">
                <? foreach ($categories as $product) : ?>
                    <a href="category.php?categoryId=<?= $product['id'] ?>"
                        class="col-span-1 max-w-sm bg-white border border-gray-200 rounded-lg shadow p-6">
                        <div class="flex flex-col items-center justify-center">
                            <img src='images/<?= $product['image'] ?>' width="300px" class="h-64 object-contain" />
                            <span class="text-xl mt-6">
                                <?= $product['name'] ?>
                            </span>
                        </div>
                    </a>
                <? endforeach; ?>
            </div>
        </section>
    </main>

    <?php include('partials/footer.php') ?>

</body>

</html>