<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Наши животные - Приют "Лапочка"</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="pets_list.css">
    <link rel="stylesheet" href="requests.css">
</head>

<body>
    <?php
    session_start();
    include 'db.php';

    $user_id = $_SESSION['user_id'];
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
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
            <li><a href="main.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'main.php') ? 'active' : ''; ?>">Главная</a></li>
            <li><a href="pets_list.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'pets_list.php') ? 'active' : ''; ?>">Наши животные</a></li>
            <li><a href="requests.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'requests.php') ? 'active' : ''; ?>">Заявки на приют</a></li>
            <?php
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
            <h1>Ваши заявки на приют</h1>

            <div class="search">
                <form method="get">
                    <label for="search">Поиск:</label>
                    <input type="text" name="search" id="search"
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                        placeholder="Имя, порода, описание...">
                    <button type="submit">Поиск</button>
                </form>
            </div>

            <div class="container">
                <?php
                if ($_SESSION['role'] == 'user') {
                    $query = "SELECT a.applicationid, p.petid, p.name, p.age, p.gender, p.description, p.color, p.photo, s.namespecies, p.breed, a.status
                              FROM adoptapplications a
                              JOIN pets p ON p.petid = a.petid
                              JOIN species s ON s.speciesid = p.speciesid  
                              WHERE a.userid = :user_id";

                    if (!empty($searchTerm)) {
                        $query .= " AND (p.name LIKE :searchTerm
                                         OR p.age LIKE :searchTerm
                                         OR p.gender LIKE :searchTerm
                                         OR p.description LIKE :searchTerm
                                         OR p.color LIKE :searchTerm
                                         OR s.namespecies LIKE :searchTerm
                                         OR p.breed LIKE :searchTerm
                                         OR a.status LIKE :searchTerm)";
                    }

                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':user_id', $user_id);

                    if (!empty($searchTerm)) {
                        $searchTerm = '%' . $searchTerm . '%';
                        $stmt->bindParam(':searchTerm', $searchTerm);
                    }

                    $stmt->execute();
                    $applications = $stmt->fetchAll();

                    if ($applications) {
                        foreach ($applications as $application) {
                            echo '<div class="card">';
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($application['photo']) . '" alt="Pet Image">';
                            echo '<div class="card-info">';
                            echo '<p>Имя питомца: ' . htmlspecialchars($application['name']) . '</p>';
                            echo '<p>Вид: ' . htmlspecialchars($application['namespecies']) . '</p>';
                            echo '<p>Возраст: ' . htmlspecialchars($application['age']) . '</p>';
                            echo '<p>Порода: ' . htmlspecialchars($application['breed']) . '</p>';
                            echo '<p>Пол: ' . htmlspecialchars($application['gender']) . '</p>';
                            echo '<p>Описание: ' . htmlspecialchars($application['description']) . '</p>';
                            echo '<p>Цвет: ' . htmlspecialchars($application['color']) . '</p>';
                            $statusMessage = '';
                            
$statusMessage = '';
$statusClass = ''; 

switch ($application['status']) {
    case 'pending':
        $statusMessage = 'Заявка на рассмотрении';
        $statusClass = 'status-pending';
        break;
    case 'approved':
        $statusMessage = 'Теперь это ваш питомец!';
        $statusClass = 'status-approved';
        break;
    case 'archived':
        $statusMessage = 'Заявка занесена в архив';
        $statusClass = 'status-archived';
        break;
    case 'rejected':
        $statusMessage = 'К сожалению, отказ';
        $statusClass = 'status-rejected';
        break;
    default:
        $statusMessage = 'Неизвестный статус';
        $statusClass = 'status-unknown';
        break;
}
echo '<p class="' . htmlspecialchars($statusClass) . '">' . htmlspecialchars($statusMessage) . '</p>';

                            echo '</div>';

                            if ($application['status'] === 'pending') {
                                echo "<form action='delete_application.php' method='post'>";
                                echo "<input type='hidden' name='application_id' value='" . $application['applicationid'] . "'>";
                                echo "<button type='submit'>Отменить заявку</button>";
                                echo "</form>";
                            }
                            

                            echo '</div>';
                        }
                    } else {
                        echo '<p>У вас нет активных заявок на приют</p>';
                    }
                } elseif ($_SESSION['role'] == 'worker') {
                    // Код для работников
                    $query = "SELECT a.applicationid, p.petid, p.name AS pet_name, p.age, p.gender, p.description, p.color, p.photo, s.namespecies, p.breed, a.status, u.FirstName, u.LastName
                              FROM adoptapplications a
                              JOIN pets p ON p.petid = a.petid
                              JOIN species s ON s.speciesid = p.speciesid
                              JOIN users u ON u.UserID = a.userid
                              WHERE p.addedBy = :user_id";

                    if (!empty($searchTerm)) {
                        $query .= " AND (p.name LIKE :searchTerm
                                         OR p.age LIKE :searchTerm
                                         OR p.gender LIKE :searchTerm
                                         OR p.description LIKE :searchTerm
                                         OR p.color LIKE :searchTerm
                                         OR s.namespecies LIKE :searchTerm
                                         OR p.breed LIKE :searchTerm
                                         OR a.status LIKE :searchTerm
                                         OR CONCAT(u.FirstName, ' ', u.LastName) LIKE :searchTerm)"; // Поиск по имени пользователя
                    }

                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':user_id', $user_id);

                    if (!empty($searchTerm)) {
                        $searchTerm = '%' . $searchTerm . '%';
                        $stmt->bindParam(':searchTerm', $searchTerm);
                    }

                    $stmt->execute();
                    $applications = $stmt->fetchAll();

                    if ($applications) {
                        foreach ($applications as $application) {
                            echo '<div class="card">';
                            echo '<img src="data:image/jpeg;base64,' . base64_encode($application['photo']) . '" alt="Pet Image">';
                            echo '<div class="card-info">';
                            echo '<p>Имя пользователя: ' . htmlspecialchars($application['FirstName'] . ' ' . $application['LastName']) . '</p>'; // Вывод имени пользователя
                            echo '<p>Имя питомца: ' . htmlspecialchars($application['pet_name']) . '</p>';
                            echo '<p>Вид: ' . htmlspecialchars($application['namespecies']) . '</p>';
                            echo '<p>Возраст: ' . htmlspecialchars($application['age']) . '</p>';
                            echo '<p>Порода: ' . htmlspecialchars($application['breed']) . '</p>';
                            echo '<p>Пол: ' . htmlspecialchars($application['gender']) . '</p>';
                            echo '<p>Описание: ' . htmlspecialchars($application['description']) . '</p>';
                            echo '<p>Цвет: ' . htmlspecialchars($application['color']) . '</p>';
                            echo '<p>Статус заявки: ' . htmlspecialchars($application['status']) . '</p>';
                            echo '</div>';

                            if ($application['status'] === 'pending') {
                                echo "<div style='display: flex; gap: 10px;'>"; // Flexbox для расположения кнопок рядом
                                echo "<form action='cancel_application.php' method='post'>";
                                echo "<input type='hidden' name='application_id' value='" . $application['applicationid'] . "'>";
                                echo "<button type='submit'>Отменить заявку</button>";
                                echo "</form>";
                                echo "<form action='approve_application.php' method='post'>";
                                echo "<input type='hidden' name='application_id' value='" . $application['applicationid'] . "'>";
                                echo "<button type='submit'>Одобрить заявку</button>";
                                echo "</form>";
                                echo "</div>";
                            }
                            
                            
                            echo '</div>';
                        }
                    } else {
                        echo '<p>У вас нет заявок на питомцев, которые были добавлены вами.</p>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>