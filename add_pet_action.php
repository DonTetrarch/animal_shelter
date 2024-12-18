<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $name = $_POST['name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $description = $_POST['description'];
    $color = $_POST['color'];

    // Проверка, что работник авторизован
    if (!isset($_SESSION['user_id'])) {
        echo 'Ошибка: работник не авторизован.';
        exit();
    }

    // Вывод ID работника для отладки
    echo 'User ID: ' . $_SESSION['user_id'];

    // Обработка загрузки фото
if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
    $photo = $_FILES['photo']['tmp_name'];
    $photo_data = file_get_contents($photo);

    // Подготовка и выполнение SQL-запроса для добавления нового питомца
    $sql = "CALL AddPet(:name, :species, :breed, :age, :gender, :description, :color, :photo, :added_by)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([
            'name' => $name, 
            'species' => $species, 
            'breed' => $breed, 
            'age' => $age, 
            'gender' => $gender, 
            'description' => $description, 
            'color' => $color, 
            'photo' => $photo_data,
            'added_by' => $_SESSION['user_id']
        ]);
        
        // Перенаправление на страницу с животными после успешного добавления
        header("Location: pets_list.php");
        exit();
    } catch (PDOException $e) {
        // Перенаправление на страницу с формой и сообщение об ошибке
        header("Location: add_pet.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}}?>
