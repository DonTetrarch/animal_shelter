<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в приют "Лапочка"</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="form-container">
        <h2>Вход</h2>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Войти</button>
            </div>
        </form>
        <?php
        session_start();
        include 'db.php'; 

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Валидация данных
            if (empty($email) || empty($password)) {
                echo "<p style='color: red;'>Пожалуйста, заполните все поля</p>";
            } else {
                $sql = "SELECT * FROM users WHERE Email = :email AND Password = :password";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['email' => $email, 'password' => $password]);
                $user = $stmt->fetch();

                if ($user) {
                    // Успешный вход
                    $_SESSION['user_id'] = $user['UserID'];
                    $_SESSION['user'] = $user['FirstName'] . " " . $user['LastName'];
                    $_SESSION['role'] = $user['Role'];

                    header("Location: main.php");
                    exit();
                } else {
                    echo "<p style='color: red;'>Неверный email или пароль</p>";
                }
            }
        }
        ?>
    </div>
    <button type="button" onclick="window.location.href='main.php';" style="margin-left: 380px; width:150px; height:30px;">Вернуться на главную</button>
</body>

</html>