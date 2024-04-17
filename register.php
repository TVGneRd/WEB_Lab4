<?php
include_once "src/connection.php";

$error = "";

if (isset($_POST['submit'])) {
    if ($_POST['password'] != $_POST['confirmPassword']) {
        $error = "Пароли не совпадают";
    } else if ($mysqli->execute_query("select * from users where email=?", [ $_POST['email'] ])->num_rows) {
        $error = "Почта уже занята";
    } else {
        $insert = $mysqli->execute_query("INSERT INTO users (email, name, birthday, password) VALUES (?, ?, ?, ?)", [ $_POST['email'], $_POST['name'], $_POST['birthday'], password_hash($_POST['password'], PASSWORD_DEFAULT) ]);
        $userId = mysqli_insert_id($mysqli);
        setcookie('userId', $userId);
        setcookie('token', sha1($userId));

        header('Location: index.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? include('partials/head.php') ?>
</head>

<body class="">
    <? include('partials/header.php') ?>

    <main class="mt-[80px] min-h-[calc(100vh_-_160px)]">

        <section class="mx-auto container pb-8 mt-12 flex justify-center">
            <div id="loginModal"
                class="relative p-6 py-8 m-4 w-1/4 w-full max-w-[600px] rounded-lg bg-white font-sans text-base font-light leading-relaxed text-blue-gray-500 antialiased shadow-2xl">
                <div
                    class="flex items-center justify-between p-3 font-sans text-2xl antialiased font-semibold leading-snug shrink-0 text-blue-gray-900">
                    <div>
                        <h5
                            class="block font-sans text-3xl antialiased font-semibold leading-snug tracking-normal text-blue-gray-900">
                            Зарегистрироваться
                        </h5>

                    </div>

                </div>

                <div
                    class="relative px-4 font-sans text-base font-light leading-relaxed text-blue-gray-500 antialiased">
                    <form method="post" class="flex flex-col gap-3">

                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Имя</label>
                            <input name="name" type="text" id="name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Иван" required="required">
                        </div>

                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                            <input name="email" type="email" id="email"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="inanov@mail.ru" required="required">
                        </div>
                        <div>
                            <label for="birthday" class="block mb-2 text-sm font-medium text-gray-900">Дата
                                рождения</label>
                            <input name="birthday" type="date" id="birthday"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required="required">
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Пароль</label>
                            <input name="password" type="password" id="password"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="******" required="required">
                        </div>
                        <div>
                            <label for="confirmPassword" class="block mb-2 text-sm font-medium text-gray-900">Повтор
                                пароля</label>
                            <input name="confirmPassword" type="password" id="confirmPassword"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="******" required="required">
                        </div>
                        <div class="flex flex-col mt-6 gap-3">
                            <input type="submit" name="submit" value="Зарегистрироваться" id="submit"
                                class="block w-full bg-purple-700 text-white rounded-lg py-2 font-bold">
                        </div>

                    </form>

                    <div class="mt-4">
                        <span id="error" class="text-red-800">
                            <?= $error ?>
                        </span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include('partials/footer.php') ?>

</body>

</html>