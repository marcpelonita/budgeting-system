<?php
require 'config.php';

// Start the session
session_start();

if (isset($_POST['submit_search'])) {
    $expense_category = $_POST['expense_category'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Check if a category is selected
    if (!empty($expense_category)) {
        try {
            // Construct the SQL query to fetch transactions for the selected category
            $sql = "SELECT expenses_id, expense_category, amount FROM expenses WHERE expense_category = :expense_category AND user_id = :user_id";
            
            // Prepare the SQL statement
            $stmt = $db->prepare($sql);

            // Bind the parameters
            $stmt->bindParam(':expense_category', $expense_category, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            // Execute the statement
            $stmt->execute();

            // Fetch the results
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Display or process the results as needed
            foreach ($results as $result) {
                echo "Expenses ID: " . $result['expenses_id'] . "<br>";
                echo "Category: " . $result['expense_category'] . "<br>";
                echo "Amount: $" . $result['amount'] . "<br>";
                echo "-----------------------<br>";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        // Handle the case when no category is selected
        echo "Please select a category.";
    }
}
?>
