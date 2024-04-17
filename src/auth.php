<?php
include_once "src/connection.php";

function getCurrentUser()
{
    global $mysqli;

    $user = $mysqli->execute_query("select * from users where id=?", [ $_COOKIE['userId'] ?? -1 ])->fetch_array(MYSQLI_ASSOC);

    if (! isset($_COOKIE['token']) || sha1($user['id']) != $_COOKIE['token']) {
        return null;
    }

    return $user;
}