<?php
require('connect.php'); // Include your database connection file

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['videoNum']) && isset($_POST['card_id'])) {
        $videoNum = intval($_POST['videoNum']);
        $card_id = $_POST['card_id'];

        if ($videoNum < 1 || $videoNum > 5) {
            echo json_encode(["success" => false, "message" => "Invalid video number."]);
            exit;
        }

        // Define column names dynamically
        $videoColumn = "d_service_video" . $videoNum;
        $nameColumn = "d_service_name" . $videoNum;

        // Get the current video file path from the database
        $stmt = $connect->prepare("SELECT $videoColumn FROM uploaded_video WHERE card_id = ?");
        $stmt->bind_param("s", $card_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $videoData = $result->fetch_assoc();
        $stmt->close();

        if (!$videoData || empty($videoData[$videoColumn])) {
            echo json_encode(["success" => false, "message" => "Video not found in the database."]);
            exit;
        }

        $videoPath = $videoData[$videoColumn];

        // Perform dummy delete (mark both video and name as NULL)
        $stmt = $connect->prepare("UPDATE uploaded_video SET $videoColumn = NULL, $nameColumn = NULL WHERE card_id = ?");
        $stmt->bind_param("s", $card_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Video and name marked as deleted."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update database."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request parameters."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
