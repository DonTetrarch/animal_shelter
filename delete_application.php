<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $user_id = $_SESSION['user_id']; // ID текущего пользователя
    $application_id = $_POST['application_id']; // ID заявки

    // Проверяем, существует ли заявка и принадлежит ли она текущему пользователю
    $query = "SELECT * FROM adoptapplications WHERE applicationid = :application_id AND userid = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $application = $stmt->fetch();

    if ($application) {
        try {
            // Удаляем заявку из базы данных
            $deleteQuery = "DELETE FROM adoptapplications WHERE applicationid = :application_id";
            $deleteStmt = $pdo->prepare($deleteQuery);
            $deleteStmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);

            if ($deleteStmt->execute()) {
                $_SESSION['success_message'] = 'Заявка успешно удалена.';
                header("Location: requests.php");
                exit();
            } else {
                echo "Ошибка при удалении заявки. Попробуйте снова.";
            }
        } catch (Exception $e) {
            echo "Произошла ошибка: " . $e->getMessage();
        }
    } else {
        echo "Заявка не найдена или вы не можете отменить эту заявку.";
    }
} else {
    echo "Неверный запрос.";
}
?>
