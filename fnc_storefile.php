<?php 
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
