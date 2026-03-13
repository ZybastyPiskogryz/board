<!DOCTYPE html>
<html>
<head>
    <title>METANIT.COM</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<?php
$remove = false;
$afterregistration = false;
$file = '/data/messages.json';
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

            $stmt = $db->prepare("SELECT * FROM Users WHERE name = ?"); //последнее изменение
            $stmt->execute([$name]);                                    //безопасность+
            $usersSQlite = $stmt->fetchAll();

            $found = false;
                    $_SESSION["nameSESSION"] = $name;
                    $_SESSION["passSESSION"] = $pass;

            foreach ($usersSQlite as $user) {
                if ($user['name'] === $name && $user['pass'] === $pass) {
                    $found = true;


                    $afterregistration = true;

                    break;
                }
            }

            if ($found) {
                ?>
                <script>
                    if (confirm('Такой пользователь уже существует. Войти?')) {
                        window.location.href = 'boardview.php';
                    } else {
                        window.location.href = 'register.php';
                    }
                </script>
                <?php
                $_SESSION['remove'] = true;

                exit; 
            } else {
                $stmt = $db->prepare("
                    INSERT INTO Users (name, pass, created_at)
                    VALUES (?, ?, CURRENT_TIMESTAMP)
                ");
                $stmt->execute([$name, $pass]);
                $afterregistration = true;

                header('Location: boardview.php');
                exit;
            } 
        } catch (\PDOException $e) { // Здесь закрывается try и начинается catch
            echo $e->getMessage(); 
        } // Здесь закрывается catch
    } // Здесь закрывается else
} // Здесь закрывается isset



if ($_SESSION['remove']){
    if (isset($_SESSION["nameSESSION"]) && isset($_SESSION["passSESSION"]))
    {
        header('Location: boardview.php');
            exit;
    }
}
    // if($_POST['d'])
    //     {
    //     if (file_exists($file)) {
    //         readfile($file);}
    //             exit;
    //         } else {
    //             echo "Файл не найден.";
    //         }
    


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

<!-- <h3>Форма ввода данных (sqlite)</h3> -->
<div class="registr">
    <form method="POST" action="">
        <h1>Добро пожаловать</h1>
        <h4>Пожалуйста, зарегистрируйтесь или войдите</h4>

        <div class="input-form">
            <input type="text" name="name" placeholder="Логин" required>
        </div>

        <div class="input-form">
            <input type="password" name="pass" placeholder="Пароль" required>
        </div>

        <button type="submit" name="press" class="btn" value="зарегистрироваться">
            Зарегистрироваться
        </button>
        <!-- <button type="submit" name="d" class="btn" value="валя нажми сюда">
            валя нажми сюда
        </button> -->
    </form>
</div>
</body>
</html>

