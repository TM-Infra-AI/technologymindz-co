<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Increase limits for large ZIPs
ini_set('max_execution_time', 600); // 10 minutes
ini_set('max_input_time', 600);
ini_set('memory_limit', '512M');

$zipFile = 'deploy.zip';
$extractTo = __DIR__;

header('Content-Type: application/json');

function logStatus($message) {
    file_put_contents(__DIR__ . '/deploy_log.txt', date('Y-m-d H:i:s') . " - $message\n", FILE_APPEND);
}

logStatus("Starting deployment");

if (!file_exists($zipFile)) {
    http_response_code(404);
    logStatus("deploy.zip not found");
    echo json_encode(['status' => 'error', 'message' => 'deploy.zip not found']);
    exit;
}

$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo($extractTo);
    $zip->close();

    // Optionally delete the ZIP after extraction
    if (unlink($zipFile)) {
        logStatus("Extraction complete. ZIP deleted.");
        echo json_encode(['status' => 'success', 'message' => 'Deployed and zip removed']);
    } else {
        logStatus("Extraction complete. Failed to delete ZIP.");
        echo json_encode(['status' => 'success', 'message' => 'Deployed, but failed to delete deploy.zip']);
    }
} else {
    http_response_code(500);
    logStatus("Failed to extract ZIP");
    echo json_encode(['status' => 'error', 'message' => 'Failed to extract deploy.zip']);
}

exit;
