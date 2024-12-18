<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Наши животные - Приют "Лапочка"</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="pets_list.css">
</head>

<body>
    <?php
    session_start();
    include 'db.php';

    $user_id = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['adopt_pet']) && $_SESSION['role'] == 'user') { 
            if (!$user_id) {
                echo "Войдите или зарегистрируйтесь для того чтобы оставить заявку на питомца";
            } else {
                $pet_id = $_POST['pet_id'];

                $sqlCheck = "SELECT * FROM adoptapplications WHERE PetID = :pet_id AND UserID = :user_id";
                $stmtCheck = $pdo->prepare($sqlCheck);
                $stmtCheck->bindParam(':pet_id', $pet_id);
                $stmtCheck->bindParam(':user_id', $user_id);
                $stmtCheck->execute();

                if ($stmtCheck->rowCount() > 0) {
                    echo '<div style="background-color: #f8d7da; font-size: 20px; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin: 10px 0; text-align: center;">';
                    echo 'Вы уже оставили заявку на приют для этого питомца';
                    echo '</div>';
                } else {
                    try {
                        $sql = "INSERT INTO adoptapplications (PetId, UserID, Status) VALUES (:pet_id, :user_id, 'pending')";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':pet_id', $pet_id);
                        $stmt->bindParam(':user_id', $user_id);
                        $stmt->execute();
                        echo '<div style="background-color: #d4edda; font-size: 20px; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin: 10px 0; text-align: center;">';
                        echo 'Заявка на усыновление питомца отправлена!';
                        echo '</div>';
                    } catch (Exception $e) {
                        echo "Произошла ошибка при обработке запроса: " . $e->getMessage();
                    }
                }
            }
        }
    }
    ?>
    <div class="window">
        <div class="title-bar">
            <div class="title">Приют "Лапочка"
                <?php
                if (isset($_SESSION['user'])) {
                    echo "<p>" . $_SESSION['user'] . "</p>";
                    echo "<form action='logout.php' method='post'><button type='submit'>Выход</button></form>";
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
                    <li><a href="main.php"
                            class="<?php echo (basename($_SERVER['PHP_SELF']) == 'main.php') ? 'active' : ''; ?>">Главная</a>
                    </li>
                    <li><a href="pets_list.php"
                            class="<?php echo (basename($_SERVER['PHP_SELF']) == 'pets_list.php') ? 'active' : ''; ?>">Наши
                            животные</a></li>
                    <li><a href="requests.php"
                            class="<?php echo (basename($_SERVER['PHP_SELF']) == 'requests.php') ? 'active' : ''; ?>">Заявки
                            на приют</a></li>
                    <?php
                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'worker') {
                        ?>
                        <li><a href="weights_info.php"
                                class="<?php echo (basename($_SERVER['PHP_SELF']) == 'weights.php') ? 'active' : ''; ?>">Веса
                                критериев</a></li>
                        <?php
                    }
                } else {
                    ?>
                    <li><a href="main.php"
                            class="<?php echo (basename($_SERVER['PHP_SELF']) == 'main.php') ? 'active' : ''; ?>">Главная</a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>

        <div class="content">
            <h1>Наши животные</h1>

            <div class="search">
                <form method="get">
                    <label for="search">Поиск:</label>
                    <input type="text" name="search" id="search"
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                        placeholder="Имя, порода, описание...">
                    <button type="submit">Поиск</button>
                    <?php
                    if ($_SESSION['role'] == 'worker') {
                        echo '<button type="button" onclick="location.href=\'add_pet.php\';">Добавить питомца</button>';
                    }
                    ?>
                </form>
            </div>

            <div class="container">
                <?php
                try {
                    // Получение весов из базы данных
                    $sqlWeights = "SELECT Criterion, Weight FROM user_weights";
                    $stmtWeights = $pdo->prepare($sqlWeights);
                    $stmtWeights->execute();
                    $weights = $stmtWeights->fetchAll(PDO::FETCH_KEY_PAIR);

                    // Получение предпочтений по видам
                    $preferredSpecies = [];
                    if ($_SESSION['role'] == 'user') {
                        $sqlPreferences = "SELECT species.NameSpecies, COUNT(adoptapplications.PetID) AS AppCount 
                                       FROM species 
                                       LEFT JOIN Pets ON species.SpeciesID = Pets.SpeciesID 
                                       LEFT JOIN adoptapplications ON adoptapplications.PetID = Pets.PetID AND adoptapplications.UserID = :user_id 
                                       GROUP BY species.NameSpecies 
                                       ORDER BY AppCount DESC";

                        $stmtPreferences = $pdo->prepare($sqlPreferences);
                        $stmtPreferences->bindParam(':user_id', $user_id);
                        $stmtPreferences->execute();
                        $preferredSpecies = $stmtPreferences->fetchAll(PDO::FETCH_KEY_PAIR);
                    }

                    // Получение предпочтений по цветам
                    $preferredColors = [];
                    if ($_SESSION['role'] == 'user') {
                        $sqlColorPreferences = "SELECT pets.Color, COUNT(adoptapplications.PetID) AS AppCount 
                                            FROM Pets 
                                            LEFT JOIN adoptapplications ON adoptapplications.PetID = Pets.PetID 
                                            WHERE adoptapplications.UserID = :user_id 
                                            GROUP BY pets.Color 
                                            ORDER BY AppCount";

                        $stmtColorPreferences = $pdo->prepare($sqlColorPreferences);
                        $stmtColorPreferences->bindParam(':user_id', $user_id);
                        $stmtColorPreferences->execute();
                        $preferredColors = $stmtColorPreferences->fetchAll(PDO::FETCH_KEY_PAIR);
                    }

                    $searchFilter = isset($_GET['search']) ? $_GET['search'] : '';

                    $sql = 'SELECT DISTINCT pets.PetID, pets.Name, pets.SpeciesID, pets.Breed, pets.Age, pets.Gender, pets.Description, pets.Color, pets.Photo, species.NameSpecies 
                        FROM Pets 
                        LEFT JOIN adoptapplications ON adoptapplications.PetID = Pets.PetID 
                        LEFT JOIN species ON Pets.SpeciesID = species.SpeciesID 
                        WHERE (adoptapplications.status IS NULL OR adoptapplications.status != "approved")';

                    if ($_SESSION['role'] == 'worker') {
                        $sql .= ' AND pets.addedBy = :user_id';
                    }

                    if ($searchFilter) {
                        $sql .= ' AND (pets.Name LIKE :search
                                    OR pets.Breed LIKE :search 
                                    OR pets.Age LIKE :search 
                                    OR pets.Gender LIKE :search 
                                    OR pets.Description LIKE :search 
                                    OR pets.Color LIKE :search
                                    OR species.NameSpecies LIKE :search)';
                    }

                    // Добавляем сортировку по весам
                    $orderConditions = [];

                    if ($_SESSION['role'] == 'user') {
                        if (!empty($preferredSpecies)) {
                            $orderConditions[] = 'FIELD(species.NameSpecies, ' . implode(',', array_map(function ($species) {
                                return "'" . $species . "'";
                            }, array_keys($preferredSpecies))) . ') * ' . ($weights['species'] ?? 0);
                        }

                        if (!empty($preferredColors)) {
                            $orderConditions[] = 'FIELD(pets.Color, ' . implode(',', array_map(function ($color) {
                                return "'" . $color . "'";
                            }, array_keys($preferredColors))) . ') * ' . -($weights['color'] ?? 0);
                        }

                        // Получение минимального и максимального возраста питомцев
                        $sqlAgeRange = "SELECT MIN(Age) AS MinAge, MAX(Age) AS MaxAge FROM Pets";
                        $stmtAgeRange = $pdo->prepare($sqlAgeRange);
                        $stmtAgeRange->execute();
                        $ageRange = $stmtAgeRange->fetch(PDO::FETCH_ASSOC);

                        // Определение порогов на основе минимального и максимального возраста
                        $minAge = $ageRange['MinAge'];
                        $maxAge = $ageRange['MaxAge'];

                        // Установите пороги в 1/3 и 2/3 от максимального возраста
                        $threshold1 = $minAge + ($maxAge - $minAge) / 3; // 1/3 от максимального возраста
                        $threshold2 = $minAge + 2 * ($maxAge - $minAge) / 3; // 2/3 от максимального возраста
                
                        // Использование порогов в условии сортировки
                        if (isset($weights['age'])) {
                            $ageCondition = "CASE 
                                            WHEN pets.Age < $threshold1 THEN -{$weights['age']} 
                                            WHEN pets.Age >= $threshold1 AND pets.Age < $threshold2 THEN 0 
                                            ELSE {$weights['age']} 
                                         END";
                            $orderConditions[] = $ageCondition;
                        }
                    }

                    if (!empty($orderConditions)) {
                        $sql .= ' ORDER BY ' . implode(' + ', $orderConditions) . ', Pets.PetID ASC';
                    } else {
                        $sql .= ' ORDER BY Pets.PetID ASC';
                    }

                    $stmt = $pdo->prepare($sql);
                    if ($searchFilter) {
                        $searchParam = '%' . $searchFilter . '%';
                        $stmt->bindParam(':search', $searchParam);
                    }
                    if ($_SESSION['role'] == 'worker') {
                        $stmt->bindParam(':user_id', $user_id);
                    }
                    $stmt->execute();

                    if ($stmt->rowCount() == 0) {
                        echo 'Кажется, все животные обрели дом! :D';
                    } else {
                        while ($row = $stmt->fetch()) {
                            $petid = $row['PetID'];
                            ?>
                            <div class="card">
                                <div>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['Photo']); ?>" alt="Pet Image">
                                    <div class="card-info">
                                        <p>Имя: <?php echo htmlspecialchars($row['Name']); ?></p>
                                        <p>Вид: <?php echo htmlspecialchars($row['NameSpecies']); ?></p>
                                        <p>Возраст: <?php echo htmlspecialchars($row['Age']); ?></p>
                                        <p>Порода: <?php echo htmlspecialchars($row['Breed']); ?></p>
                                        <p>Пол: <?php echo htmlspecialchars($row['Gender']); ?></p>
                                        <p>Описание: <?php echo htmlspecialchars($row['Description']); ?></p>
                                        <p>Цвет: <?php echo htmlspecialchars($row['Color']); ?></p>
                                    </div>
                                </div>
                                <div>
                                    <?php
                                    if ($_SESSION['role'] == 'worker') {
                                        // Проверяем статус заявок на текущего питомца
                                        $checkStatusQuery = "SELECT status FROM adoptapplications WHERE PetID = :pet_id AND status = 'approved'";
                                        $statusStmt = $pdo->prepare($checkStatusQuery);
                                        $statusStmt->bindParam(':pet_id', $petid, PDO::PARAM_INT);
                                        $statusStmt->execute();
                                        $isAdopted = $statusStmt->fetch();

                                        if ($isAdopted) {
                                            // Если есть заявка со статусом "approved"
                                            echo '<p style="color: green; font-weight: bold;">Питомец нашел дом!</p>';
                                        } else {
                                            // Для работников оставляем логику управления питомцами
                                            echo '<form method="post" action="delete_pet.php" style="display: inline;">';
                                            echo '<input type="hidden" name="pet_id" value="' . $row['PetID'] . '">';
                                            echo '<button type="submit" name="delete_pet">УДАЛИТЬ</button>';
                                            echo '</form>';

                                            echo '<form method="post" action="edit_pet.php" style="display: inline; margin-left: 10px;">';
                                            echo '<input type="hidden" name="pet_id" value="' . $row['PetID'] . '">';
                                            echo '<button type="submit" name="edit_pet">ИЗМЕНИТЬ</button>';
                                            echo '</form>';
                                        }
                                    } else {
                                        // Проверяем статус заявок на текущего питомца
                                        $checkStatusQuery = "SELECT status FROM adoptapplications WHERE PetID = :pet_id AND status = 'approved'";
                                        $statusStmt = $pdo->prepare($checkStatusQuery);
                                        $statusStmt->bindParam(':pet_id', $petid, PDO::PARAM_INT);
                                        $statusStmt->execute();
                                        $isAdopted = $statusStmt->fetch();

                                        if ($isAdopted) {
                                            // Если есть заявка со статусом "approved"
                                            echo '<p style="color: green; font-weight: bold;">Питомец нашел дом!</p>';
                                        } else {
                                            // Если питомец еще не приютен
                                            echo '<form method="post">';
                                            echo '<input type="hidden" name="pet_id" value="' . $petid . '">';
                                            echo '<button type="submit" name="adopt_pet">ПРИЮТИТЬ</button>';
                                            echo '</form>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                } catch (PDOException $e) {
                    die('Ошибка: ' . $e->getMessage());
                }
                ?>
            </div>
        </div>
    </div>
</body>