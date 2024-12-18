<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Приют "Лапочка"</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="weights_info.css">
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
                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'worker') {
                        ?>
                        <li><a href="weights_info.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'weights_info.php') ? 'active' : ''; ?>">Веса критериев</a></li>
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

    <?php
    include 'db.php';

    $errorMessage = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $weights = $_POST['weights'];
        $valid = true;

        foreach ($weights as $criterion => $weight) {
            // Проверка, что вес находится в пределах от -1 до 1
            if ($weight < -1 || $weight > 1) {
                $valid = false;
                $errorMessage = "Весовые коэффициенты должны быть в пределах от -1 до 1.";
                break;
            }
        }

        if ($valid) {
            try {
                foreach ($weights as $criterion => $weight) {
                    $sql = "INSERT INTO user_weights (Criterion, Weight) 
                            VALUES (:criterion, :weight)
                            ON DUPLICATE KEY UPDATE Weight = :weight";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':criterion', $criterion);
                    $stmt->bindParam(':weight', $weight);
                    $stmt->execute();
                }
                echo "<p style='color: green;'>Весовые коэффициенты успешно сохранены!</p>";
            } catch (PDOException $e) {
                echo "<p style='color: red;'>Ошибка при сохранении: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color: red;'>$errorMessage</p>";
        }
    }

    $defaultWeights = [
        'age' => 1,
        'species' => 1,
        'color' => 1,
    ];

    try {
        $sql = "SELECT Criterion, Weight FROM user_weights";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $weightsFromDB = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $currentWeights = array_merge($defaultWeights, $weightsFromDB);
    } catch (PDOException $e) {
        $currentWeights = $defaultWeights;
        echo "<p style='color: red;'>Ошибка при загрузке весов: " . $e->getMessage() . "</p>";
    }
    ?>

    <div class="container">
        <h1>Настройка весов критериев</h1>
        <form method="POST">
            <div>
                <label for="age_weight">Возраст:</label>
                <input type="number" id="age_weight" name="weights[age]" value="<?php echo htmlspecialchars($currentWeights['age']); ?>" min="-1" max="1" step="0.01">
            </div>
            <div>
                <label for="species_weight">Популярность вида питомца:</label>
                <input type="number" id="species_weight" name="weights[species]" value="<?php echo htmlspecialchars($currentWeights['species']); ?>" min="-1" max="1" step="0.01">
            </div>
            <div>
                <label for="color_weight">Цвет:</label>
                <input type="number" id="color_weight" name="weights[color]" value="<?php echo htmlspecialchars($currentWeights['color']); ?>" min="-1" max="1" step="0.01">
            </div>
            <button type="submit">Сохранить</button>
        </form>
    </div>

</body>
</html>