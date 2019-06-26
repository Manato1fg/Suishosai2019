<?php

header("Access-Control-Allow-Origin: *");

if($_SERVER["HTTP_REFERER"] !== "https://suishosai-server-php.herokuapp.com/status.php"){
    echo "㝠ゝ";
    return;
}

if(isset($_POST["org"]) && isset($_POST["status"]) && isset($_POST["reason"])){
    $org = $_POST["org"];
    $status = $_POST["status"];
    $reason = $_POST["reason"];
    date_default_timezone_set('Asia/Tokyo');
    $now = date('Y/m/d H:i:s');
    $file = file_get_contents("status.json");
    $obj = json_decode($file, true);
    if($org === "reset"){
        $obj = [];
    }else{
        $obj[$org] = [];
        $obj[$org] = [
            "status" => $status,
            "reason" => $reason,
            "lastUpdate" => $now
        ];
    }
    file_put_contents("status.json", json_encode($obj));
}else{
    echo "ダメ";
}