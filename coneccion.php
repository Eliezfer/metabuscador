<?php

function request($url, $body)
{
    $http_header= array(
    'Content-Type' => 'application/x-www-form-urlencoded',
    );
    
    $jsonObj = null;
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => $http_header,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $body 
    ));
    if( !$result = curl_exec($ch) ){
        print($ch);
        die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
    } else{
        $jsonObj = json_decode($result);
    }
    curl_close($ch);
    return $jsonObj;
}