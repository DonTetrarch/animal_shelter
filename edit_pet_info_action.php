<?php
include 'db.php';

if (isset($_POST['edit_pet_info_action'])) {
    $pet_id = $_POST['pet_id'];
    $name = $_POST['name'];
    $species_id_select = $_POST['species_id_select'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $description = $_POST['description'];
    $color = $_POST['color'];
    
    // Проверка существования вида животного
    $sql_check_species = "SELECT COUNT(*) as count FROM species WHERE SpeciesID = :species_id_select";
    $stmt_check_species = $pdo->prepare($sql_check_species);
    $stmt_check_species->execute([':species_id_select' => $species_id_select]);
    $result = $stmt_check_species->fetch();
    
    if ($result['count'] > 0) {
        // Подготовка и выполнение SQL-запроса для обновления питомца
        $sql_update = "UPDATE pets SET Name = :name, SpeciesID = :species_id_select, Breed = :breed, Age = :age, Gender = :gender, Description = :description, Color = :color WHERE PetID = :pet_id";
        $stmt = $pdo->prepare($sql_update);
        
        try {
            $stmt->execute(array(
                ':name' => $name,
                ':species_id_select' => $species_id_select,
                ':breed' => $breed,
                ':age' => $age,
                ':gender' => $gender,
                ':description' => $description,
                ':color' => $color,
                ':pet_id' => $pet_id
            ));

            if ($stmt->rowCount() > 0) {
                header("Location: pets_list.php");
                exit();
            } else {
                header("Location: edit_pet.php?pet_id=" . $pet_id . "&error=" . urlencode("Произошла ошибка при обновлении записи."));
                exit();
            }
        } catch (PDOException $e) {
            // Перенаправление с сообщением об ошибке
            header("Location: edit_pet.php?pet_id=" . $pet_id . "&error=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: edit_pet.php?pet_id=" . $pet_id . "&error=" . urlencode("Неверное значение SpeciesID: " . $species_id_select));
        exit();
    }
}
?>