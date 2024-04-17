<?php

function productInCart($productId)
{
    $products_ids = json_decode($_COOKIE['cart_products'] ?? "{}", true);
    return isset($products_ids[ $productId ]);
}

function cart($product_id, $amount = 1)
{
    $products_ids = json_decode($_COOKIE['cart_products'] ?? "{}", true);
    if ($amount || isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'add')
        $products_ids[ $product_id ] = $amount;
    else
        $products_ids = array_filter($products_ids, fn ($key) => $key != $_REQUEST['product_id'], ARRAY_FILTER_USE_KEY);


    setcookie('cart_products', json_encode($products_ids));
    $_COOKIE['cart_products'] = json_encode($products_ids);
}

function getProductsFromCart()
{
    global $mysqli;

    if (! isset($_COOKIE['cart_products']) || ! $_COOKIE['cart_products']) return [];
    $products_ids = json_decode($_COOKIE['cart_products'] ?? "{}", true);
    $result       = $mysqli->execute_query("SELECT * FROM products where id in(" . str_repeat('?,', count($products_ids) - 1) . '?' . ")", array_keys($products_ids))->fetch_all(MYSQLI_ASSOC) ?? [];

    return array_map(fn ($product) => [ ...$product, 'cart_value' => $products_ids[ $product['id'] ] ?? 0 ], $result);
}