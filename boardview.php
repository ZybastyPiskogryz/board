<!DOCTYPE html>
<html>
<head>
<title>boardView</title>
<meta charset="utf-8" />
<link rel="stylesheet" href="testBoard.css">
</head>
<body>
<?php
// $CLOUDINARY_URL='cloudinary://345119857449517:WirfS6-uPojRVXwJzNc3E9v2SAk@dxertbzwt';
$comment = htmlentities($_POST["comment"]);
$allBoard = false;
$media = false;
$answer =false;
$media_path  = '';
$media_type  = '';
$media_name  = '';
$user_id = '987654';           // ← ВАШЕ значение из Sightengine
$api_secret = 'xyz789abc123';  // ← ВАШЕ значение из Sightengine
$selectedThread = $_GET['thread'] ?? null;


// $dbPath = __DIR__ . '/data/users.db';
// $db = new PDO("sqlite:$dbPath");

// $db->exec("ALTER TABLE users ADD COLUMN profile_media INTEGER");

// echo "Колонка добавлена";


session_start();
 


$aut = false;
        try {
            

            $dbPath = __DIR__ . '/data/publicTread.db'; 
            $messageHistory = new PDO("sqlite:$dbPath");
            $dbUsers = __DIR__ . '/data/users.db'; 
            $sessionAuth = new PDO("sqlite:$dbUsers");
            $users = $sessionAuth->query("SELECT * FROM Users")->fetchAll(PDO::FETCH_ASSOC);
            $icon = $sessionAuth->query("SELECT profile_media FROM Users")->fetchAll(PDO::FETCH_ASSOC);
            $messageView = $messageHistory->query("SELECT * FROM publicTread")->fetchAll(PDO::FETCH_ASSOC);
            $threadsRaw = $messageHistory->query("SELECT thread FROM publicTread")->fetchAll(PDO::FETCH_COLUMN);
            // $sendName = $messageHistory->query("SELECT sendName FROM publicTread")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($users as $userFromArray) {
            if ($userFromArray['name'] === $nameFromSession && $userFromArray['pass'] === $passFromSession) {
                $aut = true;
                break;  // нашли → дальше искать не нужно
            }
        }
        $messageView = $messageHistory->query("
            SELECT * FROM publicTread 
            ORDER BY created_at DESC   -- ← DESC = новые сверху
        ")->fetchAll(PDO::FETCH_ASSOC);

       
// if (isset($_POST['like'])) {
//     $active_comment_id = $_POST['message_id'] ?? null;  // ← Запоминаем ID
// }


    if (isset($_POST['plus'])) {
        $message_id = $_POST['messageid'] ?? null;

        
        if ($message_id) {
            // UPDATE таблицу, добавить лайк
            $stmt = $messageHistory->prepare("
                UPDATE publicTread 
                SET likes = likes + 1 
                WHERE messageID = ?
            ");
            $stmt->execute([$message_id]);
            
            header('Location: board.php');
            exit;
        }
    }
// if(isset($_POST['submitComment'])){

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// exit;

// }
if (
        isset($_POST['submitComment']) &&
        !empty($_POST['comment']) &&
        !empty($_POST['thread'])
        ) {

        $comment = htmlentities($_POST["comment"] ?? '');
        $thread = htmlentities($_POST["thread"] ?? '');
        $answerTo = $_POST["answer_to"] ?? '';
        $sendName = $_POST["sendName"] ?? '';

    if ($comment != '' && $thread != '') {

        // загрузка файла
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {

            $uploadDir = __DIR__ . '/uploads/';
            $filename = time() . '_' . basename($_FILES['media']['name']);
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['media']['tmp_name'], $targetPath)) {
                $media_path = '/uploads/' . $filename;
                $media_type = $_FILES['media']['type'];
                $media_name = $_FILES['media']['name'];
            }
        }

        $stmt = $messageHistory->prepare("
        INSERT INTO publicTread
        (sendName, thread, text, media_path, media_type, media_name, created_at, answer_to)
        VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?)
        ");

        $stmt->execute([
            $sendName,
            $thread,
            $comment,
            $media_path,
            $media_type,
            $media_name,
            $answerTo
        ]);

        header('Location: boardview.php?thread=' . urlencode($thread));
        exit;
    }
        }
    if (isset($_SESSION["nameSESSION"]) && isset($_SESSION["passSESSION"]))
    {
        $nameFromSession = $_SESSION["nameSESSION"];
        $passFromSession = $_SESSION["passSESSION"];
    echo '<div class="profile">';
        echo '<form method="POST" enctype="multipart/form-data">';
            // echo ' <div class="board-controls">';
            // echo ' <a href="boardview.php" class="btn-all">Все обсуждения</a>';
            // echo '<input type="submit" name="New_Board" value="Создать обсуждение" class="btn-create">';

            // Ник слева
            echo '<div class="name">' . htmlspecialchars($nameFromSession) . '</div>';

            // Кнопка с иконкой справа
            echo '<button type="submit" class="image-button" name="action" value="upload">';
            if (!empty($user['profile_media'])){
                $iconPath = $user['profile_media'];
                echo '<img src="' . htmlspecialchars($iconPath) . '" alt="Иконка" width="36" height="36">';
            }else{
               echo '<img src="data/icon.png" alt="Иконка" width="36" height="36">';
        }
            echo '</button>';

        echo '</form>';
    echo '</div>';
    }else{
    echo '<div id="no-session">nihyja sessii ne robyat</div>';
        header('Location: register.php');
        exit; 
    }
}


 catch (\PDOException $e) { // Здесь закрывается try и начинается catch
            echo $e->getMessage(); 
        }

if (isset($_GET['thread-submit'])) {
     $selectedThread = $_GET['thread-submit'];  // это и есть название треда

    // редирект с правильным urlencode
    header('Location: board.php?thread=' . urlencode($selectedThread));
    exit; 
}

if(isset($_POST['New_Board'])){
   header('Location: board.php');
    exit; 
}

$threadCounts = array_count_values($threadsRaw);
$threadCounts = array_count_values($threadsRaw);

function threaslist($threadCounts){

    echo '<ul class="thread-list">';

    foreach ($threadCounts as $threadName => $count) {

        echo '<li class="thread-item">';

        // Кликабельный блок темы
        echo '<a href="?thread='.urlencode($threadName).'" 
        style="text-decoration:none; color:inherit; display:block;">';

        echo '<b>Тема:</b> ' . htmlspecialchars($threadName) . '<br>';
        echo '<span class="thread-count">' . $count . ' сообщ.</span>';

        echo '</a>';

        // Кнопка "+"
        echo '<form method="GET">';

        echo '<input type="hidden" name="thread" value="'.htmlspecialchars($threadName).'">';

        echo '<button type="submit" name="thread-submit">+</button>';

        echo '</form>';

        echo '</li>';
    }

    echo '</ul>';
}
if(isset($_POST["all_Board"])){
    $allBoard =true;
}
if ($selectedThread !== null ) {

foreach ($messageView as $msg) {

    if ($msg['thread'] !== $selectedThread) {
        continue;
    }

    echo '<div class="message">';
    echo '<form method="POST" enctype="multipart/form-data">';
    
    // Автор
    echo '<strong>' . htmlspecialchars($msg['sendName']) . '</strong> ';
    echo '<small>(' . $msg['created_at'] . ')</small><br>';

    // Тема
    echo '<b>Тема:</b> ' . htmlspecialchars($msg['thread']) . '<br>';

    if (!empty($msg['answer_to'])) {
        echo '<p><b>Ответ для:</b> '. htmlspecialchars($msg['answer_to']) . '</p>';
    }

    // Текст
    echo '<p>' . nl2br(htmlspecialchars($msg['text'])) . '</p>';

    // Медиа
    if (!empty($msg['media_path'])) {

        if (strpos($msg['media_type'], 'image') !== false) {
            echo '<img src="'.$msg['media_path'].'" width="200">';
        }

        elseif (strpos($msg['media_type'], 'video') !== false) {
            echo '<video src="'.$msg['media_path'].'" controls width="200"></video>';
        }
    }

    // Скрытые поля
    echo '<input type="hidden" name="answer_to" value="' . $msg['sendName'] . '">';
    echo '<input type="hidden" name="thread" value="' . $msg['thread'] . '">';
    echo '<input type="hidden" name="sendName" value="'.$nameFromSession.'">';

    // Ответ
    echo '<input type="text" name="comment" placeholder="Ответить">';

    echo '<input type="hidden" name="thread" value="'.$msg['thread'].'">';
    echo '<input type="hidden" name="answer_to" value="'.$msg['sendName'].'">';
    echo '<input type="hidden" name="sendName" value="'.$nameFromSession.'">';

    echo '<input type="submit" name="submitComment" value="Ответить">';

    echo '</form>';
    echo '</div>';
}

}
else{
    foreach ($messageView as $msg) {

        echo '<div class="message">';
        echo '<form method="POST" enctype="multipart/form-data">';

        // Автор
        echo '<div class="msg-header">';
        echo '<strong>' . htmlspecialchars($msg['sendName']) . '</strong> ';
        echo '<small>(' . $msg['created_at'] . ')</small><br>';
        echo '</div>';

        // Тема
        echo '<div class="msg-thread">';
        echo '<b>Тема:</b> ' . htmlspecialchars($msg['thread']) . '<br>';
        echo '</div>';
    

        if (!empty($msg['answer_to'])) {
            echo '<p><b>Ответ для:</b> '. htmlspecialchars($msg['answer_to']) . '</p>';
        }


        // Текст
        echo '<div class="msg-text">';
        echo '<p>' . nl2br(htmlspecialchars( $msg['text'])) . '</p>';
        echo '</div>';
        // Медиа
        if (!empty($msg['media_path'])) {

            if (strpos($msg['media_type'], 'image') !== false) {
                echo '<img src="'.$msg['media_path'].'" class="msg-media">';
            }

            elseif (strpos($msg['media_type'], 'video') !== false) {
                echo '<video src="'.$msg['media_path'].'" class="msg-media"></video>';
            }

        }

        // Скрытые поля
        // echo '<input type="hidden" name="answer_to" value="' . $msg['sendName'] . '">';
        // echo '<input type="hidden" name="thread" value="' . $msg['thread'] . '">';
        // echo '<input type="hidden" name="sendName" value="'.$nameFromSession.'">';

        // Ответ
        echo '<input type="text" name="comment" class="message-form " placeholder="Ответить">';

        echo '<input type="submit" name="submitComment" class="message-btn" '.$nameFromSession.'">';

        echo '</form>';
        echo '</div>';
    }
}



?>

<!-- board.php -->

<div class="main-container">

    <!-- Левая колонка: сначала кнопки, потом список тредов -->
    <aside class="threads-sidebar">

        <!-- Блок кнопок — над списком -->
        <div class="board-controls">
            <a href="boardview.php" class="btn-all">Все обсуждения</a>
            <a href="kal.php" class="btn-all">Звонки</a>

            <form method="POST" class="create-form">
                <input type="submit" name="New_Board" value="Создать обсуждение" class="btn-create">
            </form>
        </div>

        <!-- Список тредов идёт после кнопок -->
        <ul class="Thred-list">
            <?= threaslist($threadCounts); ?>
        </ul>

    </aside>

    <!-- Правая часть — сообщения -->
    <main class="messages-content">
        <?= boardView($messageView); ?>
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




