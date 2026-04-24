<?php
// proxy.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ch = curl_init('https://func-business-card-ocr-dev.azurewebsites.net/api/business_card_parser');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    // Set headers and body from uploaded file
    $file = $_FILES['image'] ?? null;
    if ($file && $file['tmp_name']) {
        $cfile = new CURLFile($file['tmp_name'], $file['type'], $file['name']);
        $data = ['image' => $cfile];

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        header("Content-Type: application/json", true, $status);
        echo $response;
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'No image uploaded.']);
    }

    curl_close($ch);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
