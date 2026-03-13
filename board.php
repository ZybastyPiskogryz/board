<!DOCTYPE html>
<html>
<head>
<title>board</title>
<meta charset="utf-8" />
<link rel="stylesheet" href="testBoard.css">
</head>
<body>
<?php
// $CLOUDINARY_URL='cloudinary://345119857449517:WirfS6-uPojRVXwJzNc3E9v2SAk@dxertbzwt';

$media = false;
$media_path  = '';
$media_type  = '';
$media_name  = '';
$user_id = '987654';           // ← ВАШЕ значение из Sightengine
$api_secret = 'xyz789abc123';  // ← ВАШЕ значение из Sightengine
$FromGET = false;

$openedThread = $_GET['thread'] ?? '';
if ($openedThread !== '') {
    $thread = $openedThread;
    $FromGET = true;
}
session_start();
 
if (isset($_SESSION["nameSESSION"]) && isset($_SESSION["passSESSION"]))
{
    $nameFromSession = $_SESSION["nameSESSION"];
    $passFromSession = $_SESSION["passSESSION"];
    echo "ИСПРАВИТЬ НА ПРОФИЛЬ  Вы вошли как: $nameFromSession";
}else{
   echo '<div id="no-session">nihyja sessii ne robyat</div>';
    header('Location: register.php');
    exit; 
}

$aut = false;
        try {

            $dbPath = __DIR__ . '/data/publicTread.db'; 
            $messageHistory = new PDO("sqlite:$dbPath");
            $dbUsers = __Dir__ . '/data/users.db'; 
            $sessionAuth = new PDO("sqlite:$dbUsers");
            $users = $sessionAuth->query("SELECT * FROM Users")->fetchAll(PDO::FETCH_ASSOC);
            $messageView = $messageHistory->query("SELECT * FROM publicTread")->fetchAll(PDO::FETCH_ASSOC);
            $threadsRaw = $messageHistory->query("SELECT thread FROM publicTread")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($users as $userFromArray) {
            if ($userFromArray['name'] === $nameFromSession && $userFromArray['pass'] === $passFromSession) {
                $aut = true;
                break;  // нашли → дальше искать не нужно
            }
        }
        if ($aut){
            $message = htmlentities($_POST["text"]);
            if ($FromGET === false) {
            $thread = htmlentities($_POST["thread"]);}
            else{
                if (
                    isset($_FILES['media']) &&
                    $_FILES['media']['error'] === UPLOAD_ERR_OK
                ) {
                    $media = true;
                }
                if ($thread === ""  ) {
                echo '   threads не может быть пустым ОБРАБОТЧИК ЗАПУСКАЕТСЯ ПР ПЕРЕЗАГРУЗКЕ СТРАНИЦЕ А НЕ ПОСЛЕ НАЖАТИЯ КНОПКИ ';
            } else {
                if ($message === '') {
                    echo 'сообщение не может быть пустым';
                } else {
                    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {

                        $uploadDir = __DIR__ . '/uploads/';
                        
                            // создаём уникальное имя
                            $filename = time() . '_' . basename($_FILES['media']['name']);
                            
                            $targetPath = $uploadDir . $filename;

                            if (move_uploaded_file($_FILES['media']['tmp_name'], $targetPath)) {
                                $media = true;
                                
                                
                                
                                // $ch = curl_init('https://api.sightengine.com/1.0/check.json');
                                // curl_setopt($ch, CURLOPT_POST, 1);  // ← ДОБАВЬТЕ ЭТО!
                                // curl_setopt($ch, CURLOPT_POSTFIELDS, [
                                //     'media' => new CURLFile($targetPath),
                                //     'models' => 'nudity',
                                //     'api_user' => $user_id,
                                //     'api_secret' => $api_secret
                                // ]);
                                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                                // $response = curl_exec($ch);
                                // $data = json_decode($response, true);

                                // // 4️⃣ ЕСЛИ ПЛОХО - УДАЛЯЕМ ФАЙЛ
                                // if ($data['nudity']['raw'] > 50) {
                                //     unlink($targetPath);  // ← Удалить файл
                                //     echo "литерали 1984";
                                // }
                                // else{
                                $media_path = '/uploads/' . $filename;
                                $media_type = $_FILES['media']['type'];
                                $media_name = $_FILES['media']['name'];
                                // }

                            } else {
                                echo "Ошибка перемещения файла";
                                exit;
                            }
                    }

                $stmt = $messageHistory->prepare("
                        INSERT INTO publicTread 
                        (sendName, thread, text, media_path, media_type, media_name, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
                ");

                $stmt->execute([
                    $nameFromSession,   // sendName
                    $thread,
                    $message,           // text
                    $media_path,
                    $media_type,
                    $media_name
                ]);

                header('Location: boardview.php');
                exit; 
                    }
                }
        }}


        }catch (\PDOException $e) { // Здесь закрывается try и начинается catch
            echo $e->getMessage(); 
        }


   function boardView($messageView){
    foreach ($messageView as $msg) {
        // echo '<div class="message">';
        // echo '<strong>' . htmlspecialchars($msg['sendName']) . '</strong> ';
        // echo '<small>(' . $msg['created_at'] . ')</small><br>';
        // echo '<b>Тема:</b> ' . htmlspecialchars($msg['thread']) . '<br>';
        // echo '<p>' . nl2br(htmlspecialchars($msg['text'])) . '</p>';
        // echo '<img src="' . htmlspecialchars($msg['media_path']) . '" style="max-width: 400px;">';
        // echo '</div>';
    }
}


if ($FromGET) {

    echo'<form method="POST" class="message-form" enctype="multipart/form-data">';
    echo '<b>Тема:</b><br>';
    echo'<div  Тема: <strong> '.$thread.' </strong></div>';
    echo'<textarea name="text" placeholder="Сообщение"></textarea>';
    echo'<input type="file" name="media" accept="image/*,video/*">';
    echo'<button type="submit">Отправить</button>';
    echo' </form>';

}else{
    echo'<form method="POST" class="message-form" enctype="multipart/form-data">';
    echo'<input type="text" name="thread" placeholder="Тема" required>';
    echo'<textarea name="text" placeholder="Сообщение"></textarea>';
    echo'<input type="file" name="media" accept="image/*,video/*">';
    echo'<button type="submit">Отправить</button>';
    echo' </form>';
}




$threadCounts = array_count_values($threadsRaw);
function threaslist($threadCounts){
    echo '<ul class="thread-list">';

    foreach ($threadCounts as $threadName => $count) {
        echo '<li class="thread-item">';
        echo '<b>Тема:</b> ' . htmlspecialchars($threadName) . '<br>';
        echo '<span class="thread-count">' . $count . ' сообщ.</span>';
        echo '</li>';
    }

    echo '</ul>';
}

?>

<!-- board.php -->

<div class="main-container">

    <div>
        <ul class="Thred-list">
            <?= threaslist($threadCounts); ?>
        </ul>
    </div>

    <div>
        <ul class="Thred-list">
            <?= boardView($messageView); ?>
        </ul>
    </div>

</div>

    </main>

</div>

<!-- Форма -->
    <!-- <form method="POST" class="message-form" enctype="multipart/form-data">
        <input type="text" name="thread" placeholder="Тема" required>
        <textarea name="text" placeholder="Сообщение"></textarea>
        <input type="file" name="media" accept="image/*,video/*">
        <button type="submit">Отправить</button>
    </form> -->
</body>
</html>




