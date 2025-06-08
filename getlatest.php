<?php $kirby = kirby();
$kirby->impersonate('kirby');

$token = option('mirthe.pixelfed-import.token');
$userid = option('mirthe.pixelfed-import.userid');
$limit = option('mirthe.pixelfed-import.limit');
$since_id = option('mirthe.pixelfed-import.since_id');

// see https://beta-preview.pixelfed.io/resources/accounts#retrieve-account-statuses
$url = 'https://pixelfed.social/api/v1/accounts/'.$userid.'/statuses/?';
$url .= '&limit=' . $limit;
$url .= '&since_id=' . $since_id;

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

    function storeFile($fullpath, $subfolder) {
        // bestandsnaam eruit halen
        $imgpathmettroep = explode("?", $fullpath);
        $imgpath = $imgpathmettroep[0];
        $imgfilename = basename($imgpath);
        
        // bestand opslaan
        $ch = curl_init($fullpath);
        $savefileloc = $subfolder. '/' . $imgfilename;
        $fp = fopen($savefileloc, 'wb');
        
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        return $imgfilename;
    }

    foreach ($data as $post){
        
        // print_r($post);

        $naam = substr(md5(rand()), 0, 10);
        $caption = $post["content"];
        $permalink = $post["url"];
        $media = $post["media_attachments"];
        $tags = $post["tags"]; # TODO add tags to seperate field in md file
        $pubdatumtijd = date("Y-m-d H:i", strtotime($post["created_at"]));  # TODO timezone is wrong atm
        $pubdatum = date("Y-m-d", strtotime($post["created_at"]));
        
        # TODO what happens to the location info? where can I find this with the API?

        // Determine and create folder to store the files
        $exportdir = __DIR__ . '/temp/';
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

}

curl_close($ch);
echo "<p>Done</p>";
exit();