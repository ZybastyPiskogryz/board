<!DOCTYPE html>
<html>
<head>
    <title>METANIT.COM</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php

$afterregistration = false;
// Получаем данные из формы (правильные имена)
$name = trim(htmlentities($_POST["name"] ?? ''));
$pass = trim(htmlentities($_POST["pass"] ?? ''));
session_start();
if (isset($_POST['press'])){
    if ($name === '' || $pass === '') {
        echo 'Заполни все поля!';
    } else {
        try {
            $dbPath = __DIR__ . '/data/users.db'; 
            $db = new PDO("sqlite:$dbPath");

            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $db->query("SELECT * FROM Users");
            $usersSQlite = $stmt->fetchAll();

            $found = false;

            foreach ($usersSQlite as $user) {
                if ($user['name'] === $name && $user['pass'] === $pass) {
                    $found = true;
                    break;
                }
            }

            if ($found) {
                ?>
                <script>
                    if (confirm('Такой пользователь уже существует. Войти?')) {
                        window.location.href = 'board.php';
                    } else {
                        window.location.href = 'register.php';
                    }
                </script>
                <?php
                exit; 
            } else {
                $stmt = $db->prepare("
                    INSERT INTO Users (name, pass, created_at)
                    VALUES (?, ?, CURRENT_TIMESTAMP)
                ");
                $stmt->execute([$name, $pass]);
                $afterregistration = true;
                header('Location: board.php');
                exit;
            } 
        } catch (\PDOException $e) { // Здесь закрывается try и начинается catch
            echo $e->getMessage(); 
        } // Здесь закрывается catch
    } // Здесь закрывается else
} // Здесь закрывается isset

function fromArray()
{
    // foreach ($usersArray as $tosqlite){

    // }
};

if (isset($_SESSION["nameSESSION"]) && isset($_SESSION["passSESSION"]))
{
    header('Location: board.php');
        exit;
}



//  удалить потом


 
// Для отладки — показываем массив


    // for ($i=0 ; $i<count($usersArray) ; $i++) {
    //     if ($usersArray[$i]['name'] === $name && $usersArray[$i]['pass'] === $pass) {
    //         $found = true;
    //         break;
    //     }
    // }

    // $i=0;
    // while ($i<count($usersArray)) {
    //     if ($usersArray[$i]['name'] === $name && $usersArray[$i]['pass'] === $pass) {
    //         $found = true;
    //         break;
    //     }
    //     $i++;
    // }
?>

<h3>Форма ввода данных (sqlite)</h3>
<form method="POST" >
    <p>Имя: <input type="text" name="name" /></p>
    <p>Пароль: <input type="password" name="pass" /></p>
    <input type="submit" name = 'press' value="зарегестрироваться">
</form>

</body>
</html>

