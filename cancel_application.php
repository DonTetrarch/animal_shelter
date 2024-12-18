<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $user_id = $_SESSION['user_id']; // ID текущего пользователя из сессии
    $application_id = $_POST['application_id']; // ID заявки

    // Получение данных о заявке
    $query = "
        SELECT a.*, p.AddedBy 
        FROM adoptapplications a 
        JOIN pets p ON a.PetID = p.PetID 
        WHERE a.ApplicationID = :application_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);
    $stmt->execute();
    $application = $stmt->fetch();

    if ($application) {
        // Проверяем, добавил ли текущий пользователь этого питомца
        if ($application['AddedBy'] == $user_id) {
            try {
                // Устанавливаем статус заявки на "rejected"
                $rejectQuery = "UPDATE adoptapplications SET status = 'rejected' WHERE ApplicationID = :application_id";
                $rejectStmt = $pdo->prepare($rejectQuery);
                $rejectStmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);

                if ($rejectStmt->execute()) {
                    header("Location: requests.php");
                    exit();
                } else {
                    echo "Ошибка при отказе заявки.";
                }
            } catch (Exception $e) {
                echo "Произошла ошибка при обработке отказа: " . $e->getMessage();
            }
        } else {
            echo "Вы не можете отказать в заявке, так как этот питомец не был добавлен вами.";
        }
    } else {
        echo "Заявка не найдена.";
    }
} else {
    echo "Неверный запрос для отказа в заявке.";
}
?>
