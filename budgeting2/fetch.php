<?php
include('config.php');

if (isset($_POST['view'])) {

    // Assuming $db is your PDO database connection
    if ($_POST["view"] != '') {
        // Use prepared statement for better security
        $update_query = "UPDATE notification SET comment_status = 1 WHERE comment_status= 0 AND user_id = user_id";
        $update_statement = $db->prepare($update_query);
        $update_statement->execute();
    }

    $query = "SELECT * FROM notification ORDER BY notification_id DESC LIMIT 5";
    $result = $db->query($query);
    $output = '';

    if ($result->rowCount() > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $output .= '
                <li>
                    <a href="#">
                        <strong>' . $row["comment_subject"] . '</strong><br />
                        <small><em>' . $row["comment_text"] . '</em></small>
                    </a>
                </li>
            ';
        }
    } else {
        $output .= '<li><a href="#" class="text-bold text-italic">No Noti Found</a></li>';
    }

    $status_query = "SELECT * FROM notification WHERE comment_status=0 AND user_id = user_id";
    $result_query = $db->query($status_query);
    $count = $result_query->rowCount();

    $data = array(
        'notification' => $output,
        'unseen_notification' => $count
    );

    echo json_encode($data);
}
?>
