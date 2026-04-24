<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('connect.php');
// session_start(); // Start session to get session variables (card_id)

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['pdfNum']) && isset($_POST['card_id'])) {
        $pdfNum = intval($_POST['pdfNum']); // PDF number to delete
        $card_id = $_POST['card_id']; // The card ID

        // Define valid columns
        $valid_columns = [
            1 => ["d_service_pdf1", "d_service_name1"],
            2 => ["d_service_pdf2", "d_service_name2"],
            3 => ["d_service_pdf3", "d_service_name3"],
            4 => ["d_service_pdf4", "d_service_name4"],
            5 => ["d_service_pdf5", "d_service_name5"]
        ];

        // Validate PDF number
        if (!array_key_exists($pdfNum, $valid_columns)) {
            echo json_encode(["success" => false, "message" => "Invalid PDF number"]);
            exit();
        }

        list($column_name_pdf, $column_name_name) = $valid_columns[$pdfNum];

        // Prepare SQL query to set the PDF fields to NULL
        $stmt = $connect->prepare("UPDATE digi_card4 SET $column_name_pdf = NULL, $column_name_name = NULL WHERE card_id = ?");
        $stmt->bind_param("s", $card_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Error deleting PDF"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Missing parameters"]);
    }
}
?>
