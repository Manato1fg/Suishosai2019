<?php

header("Access-Control-Allow-Origin: *");

const FILE_NAME = "./output.csv";

if ( ! isset( $_POST["query"] ) ) {
    echo "Invalid Argument";
    return;
}

$data = [];

$query = (string) urldecode($_POST["query"]);
$query = str_replace("\n", "", $query);

$query = str_replace("@q ", "@q", $query);
$query = str_replace("@q", "", $query);
$query = str_replace(",", "[COMMA]", $query);

$keywords = explode(" ", $query);

$result_array = [];
$cache = [];

$file_contents = file_get_contents(FILE_NAME);
$cache = explode("\n", $file_contents);
$cache2 = [];

foreach($cache as $row){
    list($o_id, $o_name, $o_description, $o_place, $o_keywords, $o_url) = explode(",", $row);
    $cache2[$o_id] = [];
    $cache2[$o_id] = ["name" => $o_name, "description" => $o_description, "url" => $o_url, "keywords" => $o_keywords];
    $result_array[$o_id] = 0;
}

foreach($keywords as $keyword){
    if($keyword === "") continue;
    foreach($cache2 as $_o_id_ => $o_data){
        $result_array[$_o_id_] += strpos($o_data["keywords"], $keyword) !== FALSE ? 1 : 0;
    }
}

arsort($result_array);

$i = 0;
$data[0] = "";
foreach($result_array as $key => $value){
    if($value === 0) break;
    $data[$i] = $key;
    $i++;
}
echo json_encode($data, JSON_UNESCAPED_UNICODE);
return;