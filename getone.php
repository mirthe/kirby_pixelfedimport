<?php $kirby = kirby();
$kirby->impersonate('kirby');

$token = option('mirthe.pixelfed-import.token');
$contentsubfolder = option('mirthe.pixelfed-import.contentsubfolder');
// $userid = option('mirthe.pixelfed-import.userid');
$postid = get('id');

// https://beta-preview.pixelfed.io/resources/statuses#retrieve-status-by-id
$url = 'https://pixelfed.social/api/v1/statuses/' .  $postid;

$ch = curl_init();  
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'User-Agent: MyPixelfedPHPClient/1.0'
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
    include_once('fnc_storefile.php');

    // TODO opruimen, super veel redundantie met getlatest. herzien..

    $naam = substr(md5(rand()), 0, 10);
    $caption = $data["content"];
    $permalink = $data["url"];
    $media = $data["media_attachments"];
    $tags = $data["tags"]; # TODO add tags to seperate field in md file
    $pubdatumtijd = date("Y-m-d H:i", strtotime($data["created_at"]));  # TODO timezone is wrong atm
    $pubdatumjaar = date("Y", strtotime($data["created_at"]));
    $pubdatum = date("Y-m-d", strtotime($data["created_at"]));
    # TODO what happens to the location info? where can I find this with the API?

    // Determine and create folder to store the files
    # TODO replace fotofeed with a config var!
    $exportdir = $kirby->root('content') . '/'.$contentsubfolder.'/' . $pubdatumjaar . '/';
    if (!is_dir($exportdir)) {
        mkdir($exportdir);
    }
    $folder = str_replace('-', '', $pubdatum) .'_' . $naam;
    $subfolder = $exportdir . $folder;
    if (!is_dir($subfolder)) {
        mkdir($subfolder);
    }

    // media parsen..
    $mediauris = "";
    foreach ($media as $slide) {
        $imgfilename = storeFile($slide["preview_url"],$subfolder); // TODO try if "url" isn't the better option
        $mediauris .= "- " . $imgfilename . PHP_EOL;
        // TODO test this with video's and multiple files
    }

    // Compile the content of the file to write
    $strtowrite = "Title: " . $naam
    . PHP_EOL . "----" . PHP_EOL
    . "Intro: " . $caption
    . PHP_EOL . "----" . PHP_EOL
    . "Date: " . $pubdatumtijd
    . PHP_EOL . "----" . PHP_EOL
    . "sourcelink: " . $permalink
    . PHP_EOL . "----" . PHP_EOL
    . "Photo: " .PHP_EOL . $mediauris;

    // Save to file
    file_put_contents($exportdir . $folder. '/photopost.md', $strtowrite);

    print_r($strtowrite);
    echo "<hr>";

}

curl_close($ch);
echo "<p>Done</p>";
exit();