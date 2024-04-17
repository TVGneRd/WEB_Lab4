<?php
include_once "src/connection.php";
include_once "src/cart.php";

$category = $mysqli->execute_query("SELECT * FROM category where id=?", [ $_GET['categoryId'] ])->fetch_array();

$product = getProductWithFilters();

function getProductWithFilters()
{
    global $mysqli;

    $query = "SELECT * FROM products where category_id=? ";
    if (isset($_GET['filter'])) {
        $query .= "AND (";

        foreach ($_GET['filter'] as $key => $value) {
            if (array_key_first($_GET['filter']) != $key) $query .= " AND ";

            $query .= " EXISTS(select * from product_properties where product_properties.product_id=products.id and product_properties.property_id={$key} and product_properties.value IN (" . implode(',', array_map(fn ($v) => "'$v'", $value)) . "))";
        }

        $query .= ")";

    }

    return $mysqli->execute_query($query, [ $_GET['categoryId'] ])->fetch_all(MYSQLI_ASSOC) ?? [];
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


switch ($_REQUEST['method'] ?? null) {
    case 'cart':
        cart($_REQUEST['product_id'], productInCart($_REQUEST['product_id']) ? 0 : 1);
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

            <h1 class="font-bold text-4xl mb-6">
                <?= $category['name'] ?>
            </h1>

            <div class="mb-12">
                <p>
                    <?= $category['description'] ?>
                </p>
            </div>

            <form method="get">
                <?php foreach (getAvailableProperties() as $property) : ?>
                    <div class="mb-6">
                        <div class="flex space-x-2 text-gray-800">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9.5 16C13.0899 16 16 13.0899 16 9.5C16 5.91015 13.0899 3 9.5 3C5.91015 3 3 5.91015 3 9.5C3 13.0899 5.91015 16 9.5 16Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M19 10H12C10.8954 10 10 10.8954 10 12V19C10 20.1046 10.8954 21 12 21H19C20.1046 21 21 20.1046 21 19V12C21 10.8954 20.1046 10 19 10Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <p class="lg:text-2xl text-xl lg:leading-6 leading-5 font-medium ">
                                <?= $property['name'] ?>
                            </p>
                        </div>
                        <div class="md:flex md:space-x-6 mt-4 grid grid-cols-3 gap-y-8 flex-wrap">
                            <?php foreach (getAvailablePropertiesValues($property['id']) as $property_value) : ?>
                                <div class="flex space-x-2 md:justify-center md:items-center items-center justify-start">
                                    <input class="w-4 h-4 mr-2" type="checkbox"
                                        id="property-<?= $property['id'] ?>-<?= $property_value['product_id'] ?>"
                                        name="filter[<?= $property['id'] ?>][]" value="<?= $property_value['value'] ?>"
                                        <?= isset($_GET['filter'][ $property['id'] ]) && in_array($property_value['value'], $_GET['filter'][ $property['id'] ]) ? "checked" : '' ?> />
                                    <div class="inline-block">
                                        <div class="flex space-x-6 justify-center items-center">
                                            <label class="mr-2 text-sm leading-3 font-normal text-gray-600"
                                                for="property-<?= $property['id'] ?>-<?= $property_value['product_id'] ?>"><?= $property_value['value'] ?></label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="flex gap-3">
                    <input type="hidden" name="categoryId" value="<?= $_GET['categoryId'] ?>">
                    <button class="rounded-md py-3 px-6 bg-gray-800 text-white" type="submit">Применить</button>
                    <button class="rounded-md py-3 px-6 bg-gray-600 text-white" type="reset"
                        onclick="this.closest('form').querySelectorAll('input[type=checkbox]').forEach((el) => {el.removeAttribute('checked'); el.checked = false;}) || this.closest('form').submit()">Очистить</button>
                </div>

            </form>

            <h2 class="font-bold text-2xl mb-6 mt-12">
                Товары
            </h2>
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
                            <form method="post" class="flex justify-between w-full text-xl items-center">
                                <span>Цена: <b>
                                        <?= number_format($product['price'], 0, ' ') ?>₽
                                    </b></span>

                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="hidden" name="method" value="cart">
                                <input type="hidden" name="mode"
                                    value="<?= productInCart($product['id']) ? "remove" : "add" ?>">

                                <button
                                    class="bg-blue-500 text-white px-5 py-3 text-lg rounded-lg data-[added=true]:bg-red-500"
                                    data-added='<?= productInCart($product['id']) ? "true" : "false" ?>'>
                                    <?= productInCart($product['id']) ? "Убрать" : "В корзину" ?>
                                </button>
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