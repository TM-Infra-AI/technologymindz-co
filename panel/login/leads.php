<?php
ob_start();  // Ensure no output before header()

require('connect.php');
require('header.php');

// Fetch data based on card_id
$id = $_SESSION['card_id_inprocess'];

$sql_card = "SELECT card_id FROM digi_card WHERE id = ?";
$stmt_card = $connect->prepare($sql_card);
$stmt_card->bind_param("s", $id);
$stmt_card->execute();
$result_card = $stmt_card->get_result();

if ($result_card->num_rows > 0) {
    $row_card = $result_card->fetch_assoc();
    $card_id = $row_card['card_id'];
    // var_dump($card_id);exit;
} else {
    echo "<p class='text-center text-danger mt-3'>No records found for this card ID.</p>";
    exit;
}
$stmt_card->close();

$sql = "SELECT name, phone, company, email FROM contact_details WHERE card_id = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("s", $card_id);
$stmt->execute();
$result = $stmt->get_result();

$stmt->close();
$connect->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Information</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        body {
            font-family: "Poppins", sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .table-container {
            overflow-x: auto;
            max-height: 500px;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
        }

        thead {
            background: #015fb1;
            color: white;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        thead th {
            font-size: 16px;
            font-weight: bold;
            padding: 12px;
            text-align: center;
        }

        tbody tr {
            transition: all 0.3s ease-in-out;
            border-bottom: 1px solid #ddd;
        }

        tbody tr:hover {
            background: #e9f5ff;
            transform: scale(1.01);
        }

        tbody td {
            padding: 12px;
            text-align: center;
            font-size: 14px;
            color: #555;
        }

        .text-truncate {
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .text-center {
            text-align: center;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-muted {
            color: #6c757d;
        }

        @media (max-width: 768px) {
            thead th, tbody td {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Visitor Information</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $serial = 1;
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$serial}</td>
                                <td class='text-truncate'>{$row['name']}</td>
                                <td class='text-truncate'>{$row['phone']}</td>
                                <td class='text-truncate'>{$row['company']}</td>
                                <td class='text-truncate'>{$row['email']}</td>
                              </tr>";
                            $serial++;
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center text-muted'>No visitors found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>
