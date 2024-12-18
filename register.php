<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="register.css">
</head>

<body>
<button type="button" onclick="window.location.href='main.php';" style="margin-left:10px;margin-top: 50px; width:150px; height:30px;">Вернуться на главную</button>

    <div class="form-container">
        <h2>Регистрация</h2>
        
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="first-name">Имя:</label>
                <input type="text" id="first-name" name="first-name" required>
            </div>

            <div class="form-group">
                <label for="last-name">Фамилия:</label>
                <input type="text" id="last-name" name="last-name" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="phone-number">Телефон:</label>
                <input type="text" id="phone-number" name="phone-number">
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <button type="submit">Зарегистрироваться</button>
            </div>
            
        </form>
        
    </div>
        
    <?php
    include 'db.php';
    session_start();    

    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $firstName = $_POST['first-name'];
            $lastName = $_POST['last-name'];
            $email = $_POST['email'];
            $phoneNumber = $_POST['phone-number'];
            $password = $_POST['password'];
            $role = 'user';

            if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
                echo "<p style='color: red;'>Пожалуйста, заполните все обязательные поля</p>";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "<p style='color: red;'>Неверный формат Email</p>";
            } else {
                $sql = "INSERT INTO users (Email, Password, FirstName, LastName, PhoneNumber, Role)
                VALUES (:email, :password, :firstName, :lastName, :phoneNumber, :role)";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'email' => $email,
                    'password' => $password,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'phoneNumber' => $phoneNumber,
                    'role' => $role
                ]);

                $newUserId = $pdo->lastInsertId();
                echo "Пользователь успешно зарегистрирован";
                $_SESSION['user_id'] = $newUserId;
                $_SESSION['role'] = $role;
                $_SESSION['user'] = $firstName  . " " . $lastName ;

                header("Location: main.php");
                exit;
            }
        }
    } catch (PDOException $e) {
        die('Ошибка при регистрации пользователя: ' . $e->getMessage());
    }
?>
</body>

</html>