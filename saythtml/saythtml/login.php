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
$error_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $phone =  trim($_POST['phone']);
    $pass = $_POST['password'];
    $checked = isset($_POST['checkbox']);
    
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

    if ($checked){
        if ($userData) {
            $error_message = "Аккаунт на данный номер уже зарегистрирован!";
        } else {
            if (strlen($pass) > 0 && strlen($pass) <= 11) {
                $sql = "INSERT INTO `Клиент`(`Номер_телефона`,`Пароль`) VALUES(:phone,:pass)";
                $query = $pdo->prepare($sql);
                $query->execute(['phone' => $phone, 'pass' => $pass]);
                echo "<script>
                        alert('Вы успешно зарегистрировались! Войдите в аккаунт');
                        window.location.href = 'authorizathion.php';
                      </script>";
                exit; 
            } else {
                $error_message = "Пароль должен состоять из 11 символов!";
            }
        }
    }
    else {
        $error_message = "Для регистрации нужно дать согласие на обработку персональных данных!";
    }
}
?>
<body>
    <main class="main">
        <section class="login">
            <img src="./img/Group 2.svg" alt="" class="top-line">
            <div class="container">
                <div class="login-block">
                    <form action="" class="registration-form" method ="post">
                        <h4 class="registration-title">Регистрация</h4>

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

                        <div class="checkbox-wrapper">
                          <input class="custom-checkbox" type="checkbox" id="agreement" name="checkbox">
                          <label for="agreement">Даю согласие на обработку персональных данных</label>
                        </div>

                        <button type="submit" class="submit-btn">Зарегистрироваться</button>
                    </form>
                    <div class="wrapper">
                        <div></div>
                        <a href="authorizathion.php">Авторизация</a>
                        <div></div>
                    </div>
                </div>
            </div>
            <img class="bottom-line" src="./img/Group 1.svg" alt="">
        </section>
</body>
</html>