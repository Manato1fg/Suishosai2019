<?php

$url = "http://suishosai.starfree.jp/concierge.php";

$data = array(
    'query' => $_POST["query"],
);

$content = http_build_query($data);
$options = array('http' => array(
    'method' => 'POST',
    'content' => $content
));
$contents = file_get_contents($url, false, stream_context_create($options));

header("Access-Control-Allow-Origin: *");

echo $contents;