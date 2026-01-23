<!DOCTYPE html>
<html>
<head>
    <title>METANIT.COM (спиздил и что?)</title>
    <meta charset="utf-8" />
</head>
<body>

<?php

$usersArray = json_decode(file_get_contents("data/users.json"), true) ?? [];
$afterregistration = false;
// Получаем данные из формы (правильные имена)
$name = trim(htmlentities($_POST["name"] ?? ''));
$pass = trim(htmlentities($_POST["pass"] ?? ''));
session_start();

if (isset($_SESSION["nameSESSION"]) && isset($_SESSION["passSESSION"]))
{
    header('Location: board.php');
        exit;
}
if ($name === '' || $pass === '') {
    echo 'Заполни все поля!';
} else {
    // 2. Ищем совпадение
    $found = false;

    foreach ($usersArray as $user) {
        if ($user['name'] === $name && $user['pass'] === $pass) {
            $found = true;
            break;
        }
    }

    if ($found) {
        // Пользователь существует → показываем JS-confirm
        ?>
        <script>
            if (confirm('Такой пользователь уже существует. Войти?')) {
                // Да → переходим на board.php
                window.location.href = 'board.php';
            } else {
                // Нет → остаёмся на register.php
                window.location.href = 'register.php';
            }
        </script>
        <?php
        exit;  // останавливаем выполнение
    } else {
        // Пользователя нет → добавляем нового
        $usersArray[] = [
            'name' => $name,
            'pass' => $pass
        ];
        
        $_SESSION["nameSESSION"] = $name;
        $_SESSION["passSESSION"] = $pass;
        $afterregistration = true;

        // Сохраняем
        file_put_contents("data/users.json", json_encode($usersArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Переходим на board.php
        header('Location: board.php');
        exit;
    } 
}

// Для отладки — показываем массив
// echo "<pre>";
// print_r($usersArray);
// echo "</pre>";
?>

<h3>Форма ввода данных (1ю0 гпт)</h3>
<form method="POST" >
    <p>Имя: <input type="text" name="name" /></p>
    <p>Пароль: <input type="password" name="pass" /></p>
    <input type="submit" value="зарегестрироваться">
</form>

</body>
</html>