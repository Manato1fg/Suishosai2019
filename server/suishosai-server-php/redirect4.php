<?php

header("Access-Control-Allow-Origin: *");

$referer= $_SERVER['HTTP_REFERER'];
$url = parse_url($referer);
$host = $url['host'];
 
if($host !== "suishosai.netlify.com"){
    echo "Invalid Argument";
    return;
}

if(!isset($_POST["userid"])){
    echo "Invalid Argument";
    return;
}


$accessToken = $_POST["userid"];
$salt = "Suishosai_Somu_73";
$order = "register";

$url = "http://suishosai.starfree.jp/register.php";

$data = array(
    "accessToken" => $accessToken,
    "salt" => $salt,
    "order" => $order
);

$content = http_build_query($data);
$options = array('http' => array(
    'method' => 'POST',
    'content' => $content
));
$contents = file_get_contents($url, false, stream_context_create($options));

echo $contents;