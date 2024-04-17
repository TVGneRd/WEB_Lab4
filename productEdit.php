<?php
include_once "src/connection.php";

$product    = getProduct();
$categories = getCategories();

function getProduct()
{
    global $mysqli;

    if (! isset($_REQUEST['productId'])) return [];

    $query = "SELECT * FROM products where id=? ";
    return $mysqli->execute_query($query, [ $_REQUEST['productId'] ])->fetch_assoc() ?? [];
}

function getCategories()
{
    global $mysqli;

    $query = "SELECT * FROM category";

    return $mysqli->execute_query($query)->fetch_all(MYSQLI_ASSOC) ?? [];
}

function getProductProperty($productId)
{
    global $mysqli;
    return $mysqli->execute_query("SELECT * FROM product_properties join properties on properties.id=property_id where product_id=? ", [ $productId ])->fetch_all(MYSQLI_ASSOC) ?? [];
}

function getProductPropertyValue($productId, $propertyId)
{
    global $mysqli;
    return $mysqli->execute_query("SELECT * FROM product_properties join properties on properties.id=property_id where product_id=? and property_id=?", [ $productId, $propertyId ])->fetch_assoc() ?? [];
}

function getAvailableProperties($product)
{
    global $mysqli;

    if ($product['category_id'] == null) return [];

    return $mysqli->execute_query("SELECT properties.id, properties.name FROM product_properties join properties on properties.id=property_id inner join products on products.id=product_id inner join category on category.id=products.category_id where category_id=? GROUP BY properties.id", [ $product['category_id'] ])->fetch_all(MYSQLI_ASSOC) ?? [];
}

function getAvailablePropertiesValues($propertyId)
{
    global $mysqli;
    return $mysqli->execute_query("SELECT product_properties.product_id, product_properties.value FROM product_properties join properties on properties.id=property_id inner join products on products.id=product_id inner join category on category.id=products.category_id where category_id=? and properties.id=?", [ $_GET['categoryId'], $propertyId ])->fetch_all(MYSQLI_ASSOC) ?? [];
}

if (isset($_POST['mode'])) {
    // Добавить запись, если уже существует обновить
    $result    = $mysqli->execute_query("INSERT INTO products (id, name, price, description, category_id) VALUES(?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name=?, price=?, description=?, category_id=?", [ $_REQUEST['productId'], $_POST['name'], $_POST['price'], $_POST['description'], $_POST['category_id'], $_POST['name'], $_POST['price'], $_POST['description'], $_POST['category_id'] ]);
    $productId = $product['id'] ?? mysqli_insert_id($mysqli);

    foreach ($_POST['property'] as $key => $value) {
        $mysqli->execute_query("INSERT INTO product_properties (product_id, property_id, value) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE value=?", [ $productId, $key, $value, $value ]);
    }

    header('Location: admin.php');
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
        <form method="post" class="mx-auto container pb-8 mt-6">
            <h1 id="catalogTitle" class="font-bold text-4xl mb-6 pt-6">Товар</h1>

            <div class="grid grid-cols-12 gap-3 mt-8 rounded-lg bg-white border border-gray-200 shadow p-6">
                <div class="col-span-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Название</label>
                    <input
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        placeholder="Название товара" name="name" value="<?= $product['name'] ?? '' ?>">
                    </input>
                </div>
                <div class="col-span-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Цена</label>
                    <input type="number"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        placeholder="Цена" name="price" value="<?= $product['price'] ?? '' ?>">
                    </input>
                </div>

                <div class="col-span-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Категория</label>
                    <select name="category_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <?php foreach ($categories as $category) : ?>
                            <option value='<?= $category['id'] ?>' <?= $category['id'] == ($product['category_id'] ?? '') ? "selected" : '' ?>><?= $category['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="col-span-12">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Описание</label>
                    <textarea name="description" rows="10"
                        class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5"><?= $product['description'] ?? '' ?></textarea>
                </div>
            </div>
            <div class="grid grid-cols-12 gap-3 mt-8 rounded-lg bg-white border border-gray-200 shadow p-6">
                <? if (isset($product['id'])) : ?>
                    <?php foreach (getAvailableProperties($product) as $property) : ?>
                        <div class="col-span-6">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Название</label>
                            <span>
                                <?= $property['name'] ?>
                            </span>
                        </div>
                        <div class="col-span-6">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Значение </label>
                            <input
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Значение" name="property[<?= $property['id'] ?>]"
                                value='<?= getProductPropertyValue($product['id'], $property['id'])['value'] ?? "" ?>'>
                            </input>
                        </div>
                    <?php endforeach ?>
                <? else : ?>
                    <span class="col-span-12 text-center">Чтобы изменять характеристики, сначала сохраните товар</span>
                <? endif; ?>
            </div>
            <div class="mt-4 flex justify-end">
                <input type="hidden" name="mode" value="save">
                <? if (isset($product['id'])) : ?>
                    <button class="bg-green-600 text-white px-5 py-2 text-xl rounded-lg">Сохранить</button>
                <? else : ?>
                    <button class="bg-blue-600 text-white px-5 py-2 text-xl rounded-lg">Добавить товар</button>
                <? endif; ?>

            </div>
            </div>

    </main>

    <?php include('partials/footer.php') ?>

</body>

</html>