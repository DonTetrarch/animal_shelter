<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Приют "Лапочка"</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="main.css">
</head>

<body>
    <div class="window">
        <div class="title-bar">

            <div class="title">Приют "Лапочка"
                <?php
                session_start();

                if (isset($_SESSION['user'])) {
                    echo "<p>" . $_SESSION['user'] . "</p>";
                    echo "<form action='logout.php' method='post'>
                    <button type='submit'>Выход</button>
                  </form>";

                } else {
                    echo "<p>Вход не выполнен</p>";
                }

                ?>
            </div>

        </div>
        <div class="menu">
    <ul>
        <?php
        if (isset($_SESSION['user'])) {
            ?>
            <li><a href="main.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'main.php') ? 'active' : ''; ?>">Главная</a></li>
            <li><a href="pets_list.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'pets_list.php') ? 'active' : ''; ?>">Наши животные</a></li>
            <li><a href="requests.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'requests.php') ? 'active' : ''; ?>">Заявки на приют</a></li>
            <?php
            // Показываем "Веса критериев" только для работников
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'worker') {
                ?>
                <li><a href="weights_info.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'weights.php') ? 'active' : ''; ?>">Веса критериев</a></li>
                <?php
            }
        } else {
            ?>
            <li><a href="main.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'main.php') ? 'active' : ''; ?>">Главная</a></li>
            <?php
        }
        ?>
    </ul>
</div>


        <div class="content">
            <h1>
                <span class="caption">Добро пожаловать в приют "Лапочка"!
                    <p>Приют "Лапочка" - это место, где каждое животное находит любовь, заботу и дом. Мы стремимся
                        помочь каждому питомцу найти свою семью и навсегда остаться в ней. Присоединяйтесь к нам и
                        помогите тем, кто нуждается в вашей поддержке!</p>
                    <div class="buttons">
                        <?php
                        if (!isset($_SESSION['user'])) {
                            echo '<button onclick="location.href=\'login.php\'">Войти</button>';
                            echo '<button onclick="location.href=\'register.php\'">Зарегистрироваться</button>';
                        }
                        ?>
                    </div>
                    
                </span>

                <img src="pets_photo/cat_dog.jpg" alt="Добро пожаловать">
            </h1>
        </div>
    </div>
</body>

</html>