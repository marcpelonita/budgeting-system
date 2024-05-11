<?php
require 'config.php';
session_start();


$budget_id = isset($_GET['budget_id']) ? $_GET['budget_id'] : null;


$stmt = $db->prepare("UPDATE budgets SET status = 'approve' WHERE budget_id = :budget_id");
$stmt->bindParam(':budget_id', $budget_id, PDO::PARAM_INT);
$response = array();


if ($stmt->execute()) {
    $response['status'] = 'success';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Error: ' . implode(', ', $stmt->errorInfo());
}


header('Content-Type: application/json');
echo json_encode($response);
?>
