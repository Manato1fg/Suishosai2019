<?php

$url = "http://suishosai.starfree.jp/stamprally.php";

$data = array(
    'content' => $_POST["text"],
    "accessToken" => $_POST["accessToken"]
);

$content = http_build_query($data);
$options = array('http' => array(
    'method' => 'POST',
    'content' => $content
));
$contents = file_get_contents($url, false, stream_context_create($options));

header("Access-Control-Allow-Origin: *");

echo $contents;