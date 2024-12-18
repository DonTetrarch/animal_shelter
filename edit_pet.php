<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Изменение информации о питомце</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="edit_pet.css">
</head>

<body>

<?php
// Подключение к базе данных
include 'db.php';

// Получение ID питомца из POST или GET
$pet_id = isset($_POST['pet_id']) ? $_POST['pet_id'] : (isset($_GET['pet_id']) ? $_GET['pet_id'] : null);

// Инициализация переменных
$name = '';
$species = '';
$breed = '';
$age = '';
$gender = '';
$description = '';
$color = '';

// Извлечение данных о питомце из базы данных
if ($pet_id) {
    $sql = "SELECT * FROM pets WHERE PetID = :pet_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pet_id' => $pet_id]);
    $pet = $stmt->fetch();

    if ($pet) {
        $name = $pet['Name'];
        $species = $pet['SpeciesID'];
        $breed = $pet['Breed'];
        $age = $pet['Age'];
        $gender = $pet['Gender'];
        $description = $pet['Description'];
        $color = $pet['Color'];
    } else {
        echo "<div class='error-message' style='color: red;'>Питомец не найден.</div>";
        exit();
    }
}

// Проверка наличия ошибки в URL
if (isset($_GET['error'])) {
    echo "<div class='error-message' style='color: red;'>" . htmlspecialchars($_GET['error']) . "</div>";
}
?>

<div class="form-container">
    <h2>Изменить информацию о питомце</h2>
    <form action="edit_pet_info_action.php" method="post">
        <div class="form-group">
            <label for="name">Имя:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        <div class="form-group">
            <label for="species_id_select">Вид:</label>
            <select id="species_id_select" name="species_id_select" required>
                <?php
                // Получение названия выбранного вида
                $sql_species = "SELECT NameSpecies FROM species WHERE SpeciesID = :species";
                $stmt_species = $pdo->prepare($sql_species);
                $stmt_species->execute([':species' => $species]);
                $row = $stmt_species->fetch();

                // Добавление выбранного вида
                echo "<option value='" . htmlspecialchars($species) . "'>" . htmlspecialchars($row['NameSpecies']) . "</option>";

                // Получение всех видов, кроме выбранного
                $sql_all_species = "SELECT * FROM species WHERE SpeciesID != :species";
                $stmt_all_species = $pdo->prepare($sql_all_species);
                $stmt_all_species->execute([':species' => $species]);

                while ($row = $stmt_all_species->fetch()) {
                    echo "<option value='" . htmlspecialchars($row['SpeciesID']) . "'>" . htmlspecialchars($row['NameSpecies']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="breed">Порода:</label>
            <input type="text" id="breed" name="breed" value="<?php echo htmlspecialchars($breed); ?>">
        </div>
        <div class="form-group">
            <label for="age">Возраст:</label>
            <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($age); ?>" required min="1" max="9999">
        </div>
        <div class="form-group">
            <label for="gender">Пол:</label>
            <select id="gender" name="gender" required>
                <option value="<?php echo htmlspecialchars($gender); ?>" selected><?php echo htmlspecialchars($gender); ?></option>
                <?php
                    $other_gender = ($gender == 'мальчик') ? 'девочка' : 'мальчик';
                    echo "<option value='" . htmlspecialchars($other_gender) . "'>" . htmlspecialchars($other_gender) . "</option>";
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Описание:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($description); ?></textarea>
        </div>
        <div class="form-group">
            <label for="color">Окрас:</label>
            <input type="text" id="color" name="color" value="<?php echo htmlspecialchars($color); ?>">
        </div>

        <input type="hidden" name="species_id" value="<?php echo htmlspecialchars($species); ?>">
        <input type="hidden" name="pet_id" value="<?php echo htmlspecialchars($pet_id); ?>">
        <div class="form-group">
            <button type="submit" name="edit_pet_info_action">Сохранить</button>
        </div>
    </form>
</div>
<button type="button" onclick="window.location.href='pets_list.php';" style="margin-left: 380px; width:150px; height:30px;">Вернуться назад</button>
</body>

</html>