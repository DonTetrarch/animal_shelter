<?php
session_start();

if (isset($_POST['delete_pet'])) {
    // Подключение к базе данных
    include 'db.php';

    $pet_id = $_POST['pet_id'];

    // Удаление связанных записей из таблицы adoptapplications
    $stmt = $pdo->prepare("DELETE FROM adoptapplications WHERE PetID = :pet_id");
    $stmt->bindParam(':pet_id', $pet_id);
    $stmt->execute();

    // Удаление питомца из таблицы pets
    $stmt = $pdo->prepare("DELETE FROM pets WHERE PetID = :pet_id");
    $stmt->bindParam(':pet_id', $pet_id);
    $stmt->execute();

    // После удаления питомца и связанных записей, можно перенаправить пользователя на страницу, откуда было выполнено действие
    header('Location: pets_list.php');
    exit();
}
?>