<?php
include_once "src/connection.php";

$error = "";

if (isset($_POST['submit'])) {

    $result = $mysqli->execute_query("select * from users where email=?", [ $_POST['email'] ])->fetch_array();

    if (password_verify($_POST['password'], $result['password'])) {
        setcookie('userId', $result['id']);
        setcookie('token', sha1($result['id']));

        header('Location: index.php');
    } else {
        $error = "Пароли не совпадают";
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? include('partials/head.php') ?>
</head>

<body>
    <? include('partials/header.php') ?>

    <main class="mt-[80px] min-h-[calc(100vh_-_160px)]">

        <div class="flex justify-center">
            <div id="loginModal"
                class="relative p-6 py-8 m-4 w-1/4 w-full max-w-[600px] rounded-lg bg-white font-sans text-base font-light leading-relaxed text-blue-gray-500 antialiased shadow-2xl">
                <div
                    class="flex items-center justify-between p-3 font-sans text-2xl antialiased font-semibold leading-snug shrink-0 text-blue-gray-900">
                    <div>
                        <h5
                            class="block font-sans text-3xl antialiased font-semibold leading-snug tracking-normal text-blue-gray-900">
                            Войти
                        </h5>

                    </div>

                </div>

                <div
                    class="relative px-4 font-sans text-base font-light leading-relaxed text-blue-gray-500 antialiased">
                    <form method="post" class="flex flex-col gap-3">
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                            <input name="email" type="email" id="email"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="inanov@mail.ru" required="required">
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Пароль</label>
                            <input name="password" type="password" id="password"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="******" required="required">
                        </div>

                        <div class="flex flex-col mt-6 gap-3">
                            <input type="submit" name="submit" value="Войти"
                                class="block w-full bg-blue-700 text-white rounded-lg py-2 font-bold">

                            <a href="register.php"
                                class="block text-center w-full bg-pink-700 text-white rounded-lg py-2 font-bold">Зарегистрироваться</a>
                        </div>

                    </form>

                    <div class="mt-4">
                        <span id="error" class="text-red-800"></span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include('partials/footer.php') ?>

</body>

</html>