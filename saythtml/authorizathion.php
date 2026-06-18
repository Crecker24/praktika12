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
$error_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $phone =  trim($_POST['phone']);
    $pass = $_POST['password'];
    
    $user = 'root';
    $password = '';
    $db = 'programming product';
    $host = '127.0.0.1';
    $dsn = "mysql:host=".$host.";dbname=".$db;
    $pdo = new PDO($dsn, $user, $password_db, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

    $sql = 'SELECT `Пароль` FROM `Клиент` WHERE `Номер_телефона` = :phone LIMIT 1';
    $query = $pdo->prepare($sql);
    $query->execute(['phone' => $phone]); 
    $userData = $query->fetch();

    if ($userData) {
        if ($userData['Пароль'] === $pass) {
            $_SESSION['user_phone'] = $phone;
            
            // НАДЕЖНЫЙ РЕДИРЕКТ ЧЕРЕЗ JAVASCRIPT
            echo "<script>
                    alert('Вы успешно вошли!');
                    window.location.href = 'index.php';
                  </script>";
            exit; 
        } else {
            $error_message = "Неверный пароль!";
        }
    } else {
        $error_message = "Аккаунт не найден. Вы ввели: '" . htmlspecialchars($raw_phone) . "' (в поиске использовано: '" . $phone . "'). Проверьте базу данных!";
    }
    }
?>
<body>
    <main class="main">
        <section class="login">
            <img src="./img/Group 2.svg" alt="" class="top-line">
            <div class="container">
                <div class="login-block">
                    <form action="" class="registration-form" method="post">
                        <h4 class="registration-title">Авторизация</h4>

                        <?php if (isset($error_message)): ?>
                            <p style="color: red; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($error_message) ?></p>
                        <?php endif; ?>

                        <div class="registration-form-group">
                            <label for="phone" class="registration-form-label">Номер телефона</label>
                            <input type="tel" id="phone" name="phone" class="registration-form-input" placeholder="Ваш номер телефона" required>
                        </div>

                        <div class="registration-form-group">
                            <label for="password" class="registration-form-label">Пароль</label>
                            <input type="password" id="password" name="password" class="registration-form-input" placeholder="Ваш пароль" required>
                        </div>
                        <button type="submit" class="submit-btn">Войти</button>
                    </form>
                    <div class="wrapper">
                        <div></div>
                        <a href="login.php">Регистрация</a>
                        <div></div>
                    </div>
                </div>
            </div>
            <img class="bottom-line" src="./img/Group 1.svg" alt="">
        </section>
</body>
</html>