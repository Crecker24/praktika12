<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<?php
session_start(); 
$user = 'root';
$password_db = '';
$db = 'programming product';
$host = '127.0.0.1';
$dsn = "mysql:host=".$host.";dbname=".$db;

try {
    $pdo = new PDO($dsn, $user, $password_db, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных!");
}

if (!isset($_SESSION['user_phone'])) {
    header("Location: authorizathion.php");
    exit;
}

$phone = $_SESSION['user_phone'];

$sql = "SELECT `ФИО`, `Реквизиты_компании`, `Пароль` FROM `Клиент` WHERE `Номер_телефона` = :phone LIMIT 1";
$query = $pdo->prepare($sql);
$query->execute(['phone' => $phone]);
$userData = $query->fetch();

if ($userData) {
    $user_fio = $userData['ФИО'] ?? 'Не указано';
    $user_com = $userData['Реквизиты_компании'] ?? 'Не указано';
    $user_pass_length = strlen($userData['Пароль']);
    $user_pass_masked = str_repeat("*", $user_pass_length); 
} else {
    $user_fio = 'Не указано';
    $user_com = 'Не указано';
    $user_pass_masked = 'Не указано';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="profile-header header">
        <img src="./img/Group 2.svg" alt="" class="profile-top-line top-line">
        <div class="container">
            <p class="logo">CodeNova</p>
            <nav class="header-nav">
                <ul class="nav-list">
                    <li class="nav-list-item"><a href="#" class="nav-link">Главная</a></li>
                    <li class="nav-list-item"><a href="#" class="nav-link">Заявки</a></li>
                </ul>
            </nav>
            <div class="reg-form">
                <img src="./img/Generic avatar.png" alt="avatar">
            </div>
        </div>
    </header>
    <main class="main">
        <section class="profile">
            <div class="container">
                <h4 class="profile-describe">Ваш профиль</h4>
                <div class="content-container">
                    <p class="profile-fio">ФИО: <?= htmlspecialchars($user_fio) ?></p>
                    <p class="profile-company">Реквизиты компании: <?= htmlspecialchars($user_com) ?></p>
                    <p class="profile-phone">Номер телефона: <?= htmlspecialchars($phone) ?></p>
                    <p class="profile-password">Пароль: <?= htmlspecialchars($user_pass_masked) ?></p>
                    <button class="profile-btn">Изменить пароль</button>
                    <div class="logout-container">
                        <a class="profile-logout" href="index.php">Выйти из аккаунта</a>
                    </div>
                </div>
            </div>
            <img class="bottom-line" src="img/Group 1.svg" alt="">
        </section>
    </main>
</body>
</html>