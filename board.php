<!DOCTYPE html>
<html>
<head>
<title>board</title>
<meta charset="utf-8" />
</head>
<body>
<?php
$users = json_decode(file_get_contents("data/users.json"), true) ?? [];
// $message = htmlentities($_POST["message"]);
// $usersID = $users ["name"] ;
$namesList = [];
$name = htmlentities( $_POST["name"]);
$message = htmlentities($_POST["message"]);
foreach ($users as $user) {
    $namesList[] = ['name' => $user['name']];
}


if(isset($_POST["message"])){
    $messageHistory[] = [
        'name'    => $name,
        'message' => $message,
        'time'    => date('Y-m-d H:i:s')   // полезно иметь время
    ];
}


// if(isset($_POST["name"])){
  
//     $name = htmlentities( $_POST["name"]);
// }
// if(isset($_POST["message"])){
  
//     $message = htmlentities($_POST["message"]);

// }


echo "<pre>";
print_r($users);           // ← должен показать нормальный массив
echo "</pre>";
// print_r($users); гпт сказал не надо
// $users = json_encode(file_get_contents("data/users.json"));
// file_put_contents($users, $name, $pass)
?>
<h3>Форма ввода данных(0,3)</h3>
<form method="POST">
    <p>Имя: <input type="text" name="name" /></p>
    <p>message: <input type="test" name="message" /></p>
    <input type="submit" value="Отправить">
</form>
</body>
</html>