<?php 

// see https://beta-preview.pixelfed.io/resources/accounts#retrieve-account-statuses
$url = 'https://pixelfed.social/api/v1/accounts/'.option('mirthe.pixelfed-import.userid').'/statuses/?';
$url .= '&limit=' . option('mirthe.pixelfed-import.limit');
$url .= '&since_id=' . option('mirthe.pixelfed-import.since_id');

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

print_r($url); exit();

if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
} elseif ($httpCode !== 200) {
    echo "HTTP error code: $httpCode\n";
    echo "Response: $response\n";
} else {
    $data = json_decode($response, true);
    //print_r($data);

    echo "<table border='1' cellpadding='10'>";
    foreach ($data as $post){
        echo "<tr>";
        echo '<td><a href="getone/?id='.$post["id"].'">Download</a></td><td>';
        echo $post["content"];
        echo "</td><tr>";
    }
    echo "</table>";

    exit();
}