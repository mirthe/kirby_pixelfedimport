<?php 
$url = 'https://pixelfed.social/api/v1/accounts/verify_credentials';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, kirby()->site()->title());
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . option('mirthe.pixelfed-import.token'),
    'Accept: application/json',
    'User-Agent: ' . kirby()->site()->title()
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
} elseif ($httpCode !== 200) {
    echo "HTTP error code: $httpCode\n";
    echo "Response: $response\n";
} else {
    $data = json_decode($response, true);
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

curl_close($ch);
exit();