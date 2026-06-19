<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <title>CodeNova</title>
    <script src="script.js"></script>
</head>

<?php
session_start(); 
$error_message = "";
$user = 'root';
$password_db = '';
$db = 'programming product';
$host = '127.0.0.1';
$dsn = "mysql:host=".$host.";dbname=".$db.";charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $password_db, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_consultation'])) {
    $phone    = $_SESSION['user_phone'] ?? '';
    $fio      = trim($_POST['fio'] ?? '');
    $datatime = $_POST['datatime'] ?? '';

    if (empty($phone)) {
        $error_message = "Вы не авторизованы!";
    } elseif (empty($fio)) {
        $error_message = "Введите ФИО!";
    } elseif (empty($datatime)) {
        $error_message = "Выберите дату!";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $datatime)) {
        $error_message = "Неверный формат даты! Используйте ГГГГ-ММ-ДД";
    } else {
        try {
            $pdo->beginTransaction();

            $sql = "SELECT `ID_Клиента` FROM `Клиент` 
                    WHERE `Номер_телефона` = :phone LIMIT 1";
            $query = $pdo->prepare($sql);
            $query->execute(['phone' => $phone]);
            $client = $query->fetch();

            if (!$client) {
                $pdo->rollBack();
                $error_message = "Пользователь с таким номером телефона не найден!";
            } else {
                $clientId = $client['ID_Клиента'];

                $sql = "UPDATE `Клиент` SET `ФИО` = :fio 
                        WHERE `ID_Клиента` = :id";
                $query = $pdo->prepare($sql);
                $query->execute(['fio' => $fio, 'id' => $clientId]);

                $sql = "INSERT INTO `Заявка` (`Консультация`) 
                        VALUES (:datatime)";
                $query = $pdo->prepare($sql);
                $query->execute(['datatime' => $datatime]);
                $requestId = $pdo->lastInsertId();

                $sql = "UPDATE `Клиент` 
                        SET `ID_Заявки` = :request_id 
                        WHERE `ID_Клиента` = :client_id";
                $query = $pdo->prepare($sql);
                $query->execute([
                    'request_id' => $requestId,
                    'client_id'  => $clientId
                ]);

                $pdo->commit();

                echo "<script>
                    alert('Заявка успешно создана!');
                    if (window.history.replaceState) {
                        window.history.replaceState({}, document.title, window.location.href);
                    }
                </script>";
            }

        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Ошибка базы данных: " . $e->getMessage();
        }
    }
}
?>
<body>
    <header class="header">
        <img src="./img/Group 7.svg" alt="" class="top-line">
        <div class="container">
            <p class="logo">CodeNova</p>
            <nav class="header-nav">
                <ul class="nav-list">
                    <li class="nav-list-item"><a href="#" class="nav-link">Сервис</a></li>
                    <li class="nav-list-item"><a href="#" class="nav-link">Портфолио</a></li>
                    <li class="nav-list-item"><a href="#" class="nav-link">О нас</a></li>
                </ul>
            </nav>
            <div class="reg-form">
                <?php if (isset($_SESSION['user_phone']) && !empty($_SESSION['user_phone'])): ?>
                    <a class="header-img" href="profile.php">
                        <img src="./img/Generic avatar.png" alt="avatar">
                    </a>
                <?php else: ?>
                    <img src="./img/Generic avatar.png" alt="avatar">
                    <a class="reg-text" href="login.php">Зарегистрироваться</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="main">
        <section class="welcome">
            <div class="container">
                <h1 class="welcome-title">CodeNova - Трансформируй идеи в новые возможности</h1>
                <h3 class="welcome-description">Полный цикл программной разработки: с концепции до релиза</h3>
                <div class="button-container">
                    <a href="#consultation" class="button-container-item welcome-consultation">Консультация</a>
                    <a href="#portfolio" class="button-container-item welcome-portfolio">Портфолио</a>
                </div>
                <div class="welcome-info">
                    <div class="info-block">
                        <img class="info-img" src="img/Group 4.svg" alt="code">
                        <h4 class="info-description">Разработка на заказ</h4>
                    </div>
                    <div class="info-block">
                        <img class="info-img" src="img/Group 3.svg" alt="managment">
                        <h4 class="info-description">Управление проектом</h4>
                    </div>
                    <div class="info-block">
                        <img class="info-img" src="img/Group 5.svg" alt="support">
                        <h4 class="info-description">Поддержка 24/7</h4>
                    </div>
                </div>
            </div>
            <img class="bottom-line" src="./img/Group 6.svg" alt="">
        </section>

        <section id="consultation" class="consultation">
            <div class="container">
                <div class="consultation-left-side">
                    <img src="./img/консультация.jpg" alt="">
                </div>
                <div class="consultation-right-side">
                    <?php if (!empty($error_message)): ?>
                        <p style="color:red; margin-bottom: 15px;"><?= htmlspecialchars($error_message) ?></p>
                    <?php endif; ?>
                    <form action="" class="consultation-form" method="post">
                        <h4 class="consultation-title">Консультация</h4>
                        <input type="hidden" name="phone" value="<?= htmlspecialchars($_SESSION['user_phone'] ?? '') ?>">
                        <div class="consultation-form-group">
                            <label for="name" class="consultation-form-label">ФИО</label>
                            <input type="text" id="name" name="fio" class="consultation-form-input" 
                                   placeholder="Ваше полное имя" 
                                   value="<?= htmlspecialchars($_SESSION['user_fio'] ?? '') ?>"
                                   required>
                        </div>
                        <div class="consultation-form-group">
                            <label for="datatime" class="consultation-form-label">Дата консультации</label>
                            <input type="date" id="datatime" name="datatime" class="consultation-form-input" min="<?= date('Y-m-d') ?>"required>
                        </div>
                        <button type="submit" name="submit_consultation" class="submit-btn">Отправить заявку</button>
                    </form>
                </div>
            </div>
        </section>
        <section class="portfolio" id="portfolio">
            <div class="container">
                <h2 class="portfolio-title">Наше портфолио</h2>
                <ul class="tabs">
                    <li><button class="tab-btn active" data-filter="all">Все проекты</button></li>
                    <li><button class="tab-btn" data-filter="mobile">Мобильные</button></li>
                    <li><button class="tab-btn" data-filter="web">Веб-разработка</button></li>
                </ul>
                <div class="slider-container">

                <button class="arrow-btn prev-btn">&#10094;</button>
                <div class="slider-track">
                    <div class="slide active" data-category="web">
                        <div class="slide-content">
                            <img src="img/portfolio1.jpg" alt="">
                            <div class="slide-text">
                                <p>Коммерческий проект</p>
                                <p>React, Node.js, Python</p>
                                <p>Веб-разработка · 3 месяца</p>
                            </div>
                        </div>
                    </div>
                    <div class="slide" data-category="mobile">
                        <div class="slide-content">
                            <img src="img/portfolio2.png" alt="">
                            <div class="slide-text">
                                <p>FitTrack</p>
                                <p>Flutter, Firebase, Kotlin</p>
                                <p>Мобильная разработка · 4 месяца</p>
                            </div>
                        </div>
                    </div>
                    <div class="slide" data-category="web">
                        <div class="slide-content">
                            <img src="img/portfolio3.png" alt="">
                            <div class="slide-text">
                                <p>ShopFlow</p>
                                <p>Vue.js, Django, PostgreSQL</p>
                                <p>Веб-разработка · 2 месяца</p>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="arrow-btn next-btn">&#10095;</button>
            </div>
            <div class="dots-container"></div>
            </div>
        </section>
    </main>
</body>
</html>