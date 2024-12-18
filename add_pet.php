<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление питомца</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="edit_pet.css">
</head>

<body>

    <div class="form-container">
        <h1>Добавить питомца</h1>

        <?php
        // Проверка наличия ошибки в URL
        if (isset($_GET['error'])) {
            echo "<div class='error-message' style='color: red;'>" . htmlspecialchars($_GET['error']) . "</div>";
        }
        ?>

        <form action="add_pet_action.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Имя:</label>
                <input type="text" id="name" name="name" required><br>
            </div>

            <div class="form-group">
                <label for="species">Вид животного:</label>
                <?php
                include 'db.php';
                session_start();
                $speciesStmt = $pdo->query('SELECT * FROM species');
                
                ?>

                <select name="species" id="species" required>
                    <option value="">Выберите вид</option>
                    <?php
                    while ($speciesRow = $speciesStmt->fetch()) {
                        echo "<option value='" . $speciesRow['SpeciesID'] . "'>" . $speciesRow['NameSpecies'] . "</option>";
                    }
                    ?>
                </select><br><br>
            </div>
            
            <div class="form-group">
                <label for="breed">Порода:</label>
                <input type="text" id="breed" name="breed"><br>
            </div>

            <div class="form-group">
                <label for="age">Возраст:</label>
                <input type="number" id="age" name="age" max="30"><br>
            </div>

            <div class="form-group">
                <label for="gender">Пол:</label>
                <select name="gender" id="gender">
                    <option value="мальчик">Мальчик</option>
                    <option value="девочка">Девочка</option>
                </select><br>
            </div>

            <div class="form-group">
                <label for="description">Описание:</label><br>
                <textarea id="description" name="description"></textarea><br>
            </div>

            <div class="form-group">
                <label for="color">Цвет:</label>
                <input type="text" id="color" name="color"><br>
            </div>

            <div class="form-group">
                <label for="photo">Фото:</label>
                <input type="file" id="photo" name="photo"><br>
            </div>

            <div class="form-group">
                <button type="submit" name="edit_pet_info_action">Сохранить</button>
            </div>
        </form>
    </div>

    <button type="button" onclick="window.location.href='pets_list.php';" style="margin-left: 380px; width:150px; height:30px;">Вернуться назад</button>
</body>

</html>