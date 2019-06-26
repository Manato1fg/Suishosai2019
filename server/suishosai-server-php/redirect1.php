<?php

$url = "http://suishosai.starfree.jp/contest.php";

$data = array(
    'data_org' => $_POST["data_org"],
    'data_index' => $_POST["data_index"],
    "order" => "vote",
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