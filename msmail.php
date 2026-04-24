<?php
require_once __DIR__ . '/vendor/autoload.php';

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use GuzzleHttp\Client;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['recipient_email'])) {
    $tenantId = 'a04501c2-9f60-4070-ab65-d2166f277d81';
    $clientId = '76b34383-aad2-47e3-af15-9c5e2f5883b8';
    $clientSecret = 's4w8Q~L4Ag2Zf2TUp2NmGViJIj8MpsnJveYUPbaY';
    $tokenUrl = "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/token";
    $scope = 'https://graph.microsoft.com/.default';
    $senderUser = 'info@technologymindz.com';
    $recipientEmail = trim($_POST['recipient_email']);

    $client = new Client();

    try {
        // Get access token
        $response = $client->post($tokenUrl, [
            'form_params' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => $scope,
                'grant_type' => 'client_credentials',
            ],
            // Optional: bypass SSL issue if needed (not for production)
            // 'verify' => false
        ]);

        $body = json_decode((string) $response->getBody(), true);
        $accessToken = $body['access_token'];

        // Send mail using Graph API
        $graphResponse = $client->post("https://graph.microsoft.com/v1.0/users/$senderUser/sendMail", [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'message' => [
                    'subject' => 'Test Email from Technologymindz.co',
                    'body' => [
                        'contentType' => 'Text',
                        'content' => 'Hello! This is a test email sent using Microsoft Graph API and PHP.',
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $recipientEmail,
                            ]
                        ]
                    ],
                ],
                'saveToSentItems' => true,
            ]
        ]);

        $status = "✅ Email sent to $recipientEmail!";
    } catch (Exception $e) {
        $status = "❌ Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Mail via Microsoft Graph</title>
</head>
<body>
    <h2>Send Email</h2>
    <form method="POST">
        <label for="recipient_email">Recipient Email:</label><br>
        <input type="email" name="recipient_email" required><br><br>
        <button type="submit">Send Email</button>
    </form>

    <?php if (!empty($status)): ?>
        <p><strong><?= htmlspecialchars($status) ?></strong></p>
    <?php endif; ?>
</body>
</html>
