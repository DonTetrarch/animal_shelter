<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $user_id = $_SESSION['user_id']; // ID текущего пользователя из сессии
    $application_id = $_POST['application_id']; // ID текущей заявки

    // Получение данных о заявке и питомце
    $query = "
        SELECT a.*, p.AddedBy 
        FROM adoptapplications a 
        JOIN pets p ON a.petid = p.PetID 
        WHERE a.applicationid = :application_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);
    $stmt->execute();
    $application = $stmt->fetch();

    if ($application) {
        // Проверка, добавил ли текущий пользователь этого питомца
        if ($application['AddedBy'] == $user_id) {
            $pet_id = $application['PetID'];

            // Обновление статуса текущей заявки на "approved"
            $updateQuery = "UPDATE adoptapplications SET status = 'approved' WHERE applicationid = :application_id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);

            if ($updateStmt->execute()) {
                // Обновление статуса остальных заявок на "archived"
                $rejectQuery = "
                    UPDATE adoptapplications 
                    SET status = 'archived' 
                    WHERE PetID = :pet_id AND applicationid != :application_id";
                $rejectStmt = $pdo->prepare($rejectQuery);
                $rejectStmt->bindParam(':pet_id', $pet_id, PDO::PARAM_INT);
                $rejectStmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);

                if ($rejectStmt->execute()) {
                    header("Location: requests.php");
                    exit();
                } else {
                    // Ошибка обновления остальных заявок
                    echo "Ошибка при обновлении оставшихся заявок на 'archived'.<br>";
                    print_r($rejectStmt->errorInfo());
                }
            } else {
                // Ошибка обновления текущей заявки
                echo "Ошибка при обновлении текущей заявки на 'approved'.<br>";
                print_r($updateStmt->errorInfo());
            }
        } else {
            echo "Вы не можете одобрить заявку на этого питомца, так как он не был добавлен вами.";
        }
    } else {
        echo "Заявка не найдена.";
    }
} else {
    echo "Неверный запрос для одобрения заявки.";
}
?>
