<!DOCTYPE html>
<html>
<head>
<title>board</title>
<meta charset="utf-8" />
</head>
<body>
<?php
$usersArray = json_decode(file_get_contents("data/users.json"), true) ?? [];
$messageHistory = json_decode(file_get_contents("data/threads.json"), true) ?? [];
// $namesList = [];

$message = htmlentities($_POST["message"]);
$thread = htmlentities($_POST["thread"]);

session_start();
 
if (isset($_SESSION["nameSESSION"]) && isset($_SESSION["passSESSION"]))
{
    $nameFromSession = $_SESSION["nameSESSION"];
    $passFromSession = $_SESSION["passSESSION"];
    echo "Вы вошли как: $nameFromSession";
}else{
    echo 'nihyja sessii ne robyat';
}

$aut = false;

foreach ($usersArray as $userFromArray) {
    if ($userFromArray['name'] === $nameFromSession && $userFromArray['pass'] === $passFromSession) {
        $aut = true;
        break;  // нашли → дальше искать не нужно
    }
}

       if ($aut) {
        if ($thread === "") {
            echo 'threads не может быть пустым';
        } else {
            if ($message === '') {
                echo 'сообщение не может быть пустым';
            } else {
                $messageHistory[] = [
                    'thread'  => $thread,
                    'message' => $message,
                    'name' => $nameFromSession,
                    'time'    => date('Y-m-d H:i:s')
                ];
            file_put_contents("data/threads.json", json_encode($messageHistory, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            header('Location: board.php');
            exit; // обязательно после header!}
        }
    }
}

                                                                                                //ДАЛЬШЕ ОТОБРОЖЕНИЕ СООБЩЕНИЙ


echo "<pre>";
echo "<pre>";
foreach ($messageHistory as $msg) {
    echo "тема:      " . ($msg['thread']  ?? 'нет') . "\n";
    echo "ник:       " . ($msg['name']    ?? 'нет') . "\n";
    echo "сообщение: " . ($msg['message'] ?? 'нет') . "\n";
    echo "дата:      " . ($msg['time']    ?? 'нет') . "\n";
    echo "------------------------\n";
    
}
echo "</pre>";
// echo 'тема: ' . $thread . '<br>ник: ' . $name . '<br>сообщение: ' . $message . '<br>дата: ' ;
echo "</pre>";
file_put_contents("data/threads.json", json_encode($messageHistory, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));


?>
<h1>Введите тему и сообщение</h1>
<h3>О чем хотите написать?</h3>
<form method="POST">
    <p>thread: <input type="text" name="thread" /></p>
    <p>message: <input type="test" name="message" /></p>
    <input type="submit" value="Отправить">
</form>

</body>

</html>

