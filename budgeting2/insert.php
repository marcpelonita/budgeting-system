<?php
// insert.php

include("config.php");

$subject = "delete";
$comment = "your data is deleted by the admin";

$query = "INSERT INTO notification (comment_subject, comment_text) VALUES (:subject, :comment)";
$stmt = $db->prepare($query);
$stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

try {
    if ($stmt->execute()) {
        echo "Data Inserted Successfully";
    } else {
        echo "Error inserting data";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
