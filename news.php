<?php
include_once "src/connection.php";

$news = $mysqli->execute_query("SELECT * FROM news ORDER BY date")->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? include('partials/head.php') ?>
</head>

<body>
    <? include('partials/header.php') ?>

    <main class="mt-[80px] min-h-[calc(100vh_-_160px)]">

        <section class="mb-32 text-center pt-6">
            <h2 class="mb-12 text-center text-3xl font-bold">Новости</h2>

            <div class="container mx-auto">
                <div class="grid gap-6 lg:grid-cols-3 xl:gap-x-12">
                    <?php foreach ($news as $new) : ?>
                        <div class="mb-6 lg:mb-0">
                            <div class="relative mb-6 overflow-hidden rounded-lg bg-cover bg-no-repeat shadow-lg dark:shadow-black/20"
                                data-te-ripple-init data-te-ripple-color="light">
                                <img src="<?= $new['image'] ?>" class="w-full object-cover h-72" alt="Louvre"
                                    height="300" />
                                <a href="#">
                                    <div
                                        class="absolute top-0 right-0 bottom-0 left-0 h-full w-full overflow-hidden bg-fixed opacity-0 transition duration-300 ease-in-out hover:opacity-100 bg-[hsla(0,0%,98.4%,.15)]">
                                    </div>
                                </a>
                            </div>

                            <h5 class="mb-3 text-lg font-bold">
                                <?= $new['title'] ?>
                            </h5>

                            <p class="mb-6 text-neutral-500">
                                <small>Дата публикации <u>
                                        <?= date_format(date_create($new['date']), 'd.m.Y') ?>
                                    </u>
                            </p>
                            <p class="text-neutral-600 text-md">
                                <?= $new['description'] ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </section>
    </main>

    <?php include('partials/footer.php') ?>

</body>

</html>