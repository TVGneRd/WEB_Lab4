<?php
include_once "src/connection.php";

$product = getProducts();

function getProducts()
{
    global $mysqli;

    $query = "SELECT * FROM products";

    return $mysqli->execute_query($query)->fetch_all(MYSQLI_ASSOC) ?? [];
}


function getProductProperty($productId)
{
    global $mysqli;
    return $mysqli->execute_query("SELECT * FROM product_properties join properties on properties.id=property_id where product_id=? ", [ $productId ])->fetch_all(MYSQLI_ASSOC) ?? [];
}

function getAvailableProperties()
{
    global $mysqli;
    return $mysqli->execute_query("SELECT properties.id, properties.name FROM product_properties join properties on properties.id=property_id inner join products on products.id=product_id inner join category on category.id=products.category_id where category_id=? GROUP BY properties.id", [ $_GET['categoryId'] ])->fetch_all(MYSQLI_ASSOC) ?? [];
}

function getAvailablePropertiesValues($propertyId)
{
    global $mysqli;
    return $mysqli->execute_query("SELECT product_properties.product_id, product_properties.value FROM product_properties join properties on properties.id=property_id inner join products on products.id=product_id inner join category on category.id=products.category_id where category_id=? and properties.id=?", [ $_GET['categoryId'], $propertyId ])->fetch_all(MYSQLI_ASSOC) ?? [];
}


switch ($_REQUEST['mode'] ?? null) {
    case 'delete':
        $mysqli->execute_query("DELETE FROM products WHERE id=?", [ $_REQUEST['product_id'] ]);

        break;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('partials/head.php') ?>
</head>

<body>
    <?php include('partials/header.php') ?>

    <main class="mt-[80px] min-h-[calc(100vh_-_160px)]">
        <section class="mx-auto container pb-8 pt-3">

            <div class="flex justify-between items-center">
                <h1 class="font-bold text-4xl mb-6">
                    Администрация каталога
                </h1>
                <a href="productEdit.php" class="bg-blue-500 text-white px-5 py-3 text-lg rounded-lg text-center">+
                    Добавить новый</a>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <?php foreach ($product as $product) : ?>
                    <div class="col-span-1 max-w-sm bg-white border border-gray-200 rounded-lg shadow p-6">
                        <div class="flex flex-col items-center justify-center">
                            <img src='images/<?= $product['image'] ?>' width="300px" class="h-64 object-contain" />

                            <span class="text-xl my-6">
                                <?= $product['name'] ?>
                            </span>
                            <?php foreach (getProductProperty($product['id']) as $property) : ?>

                                <div class="flex justify-between mb-3 w-full">
                                    <span>
                                        <?= $property['name'] ?>:
                                    </span>
                                    <b>
                                        <?= $property['value'] ?>
                                    </b>
                                </div>
                            <?php endforeach; ?>
                            <hr class="border-t border-gray-300 w-full my-4" />
                            <form method="post" class="flex flex-col w-full text-xl gap-4">
                                <span>Цена: <b>
                                        <?= number_format($product['price'], 0, ' ') ?>₽
                                    </b></span>

                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="hidden" name="method" value="cart">

                                <a href="productEdit.php?productId=<?= $product['id'] ?>"
                                    class="bg-blue-500 text-white px-5 py-3 text-lg rounded-lg w-full text-center">Редактировать</a>
                                <input type="submit" name="mode"
                                    class="bg-red-500 text-white px-5 py-3 text-lg rounded-lg w-full" value="Удалить" />
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <?php include('partials/footer.php') ?>

</body>

</html>