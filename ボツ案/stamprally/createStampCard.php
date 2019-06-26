<?php

class CSC{

    const BASE_IMAGE_URL = "http://suishosai.starfree.jp/img/unnmLJ3sGkOtYNIyvXH4Rgzq7MG459rd.png";
    const ID_1_IMAGE_URL = "http://suishosai.starfree.jp/img/b27CkBK0oKBi6kbNRLo8fNiU35E6TfdJ.png";
    const ID_2_IMAGE_URL = "http://suishosai.starfree.jp/img/5pScqhiZNsFu0BHBpREh377ok4TPtJlA.png";
    const ID_3_IMAGE_URL = "http://suishosai.starfree.jp/img/A6wE7yW8wSOEF62VxfWEggt8qXQqJOLD.png";
    const ID_4_IMAGE_URL = "http://suishosai.starfree.jp/img/CiCC2N0n7ThdWRtUbwflGC1qQYL2sGwx.png";
    const ID_5_IMAGE_URL = "http://suishosai.starfree.jp/img/VlKDBdbrKtSHwsg3TMRnShS6yDSAgeA9.png";
    const IMAGE_URLS = [
        self::ID_1_IMAGE_URL,
        self::ID_2_IMAGE_URL,
        self::ID_3_IMAGE_URL,
        self::ID_4_IMAGE_URL,
        self::ID_5_IMAGE_URL
    ];
    const BASE_IMAGE_MARGIN_TOP = 136;
    const SOURCE_IMAGE_SIZE = 144;
    const SPACE_SIZE = 10;

    function toAry($num){
        $res = [0,0,0,0,0];
        $i=0;
        while($num !== 0){
            $remainder = $num % 2;
            $num = ($num - $remainder) / 2;
            $res[$i] = $remainder;
            $i++;
        }
        return $res;
    }

    function createStampCard($stamp){
        $ary = $this->toAry($stamp);
        $img = imagecreatefrompng(self::BASE_IMAGE_URL);
        for($i = 0; $i < count($ary); $i++){
            if($ary[$i] === 1){
                $src = imagecreatefrompng(self::IMAGE_URLS[$i]);
                $w = self::SPACE_SIZE + self::SPACE_SIZE + (self::SPACE_SIZE + imagesx($src)) * $i;
                imagecopy($img, $src, $w, self::BASE_IMAGE_MARGIN_TOP, 0, 0, imagesx($src), imagesy($src));
            }
        }

        return $img;
    }
}

if(isset($_GET["accessToken"])){

    $url = "http://suishosai.starfree.jp/stamprally.php";

    $data = array(
        "accessToken" => $_GET["accessToken"],
        "order" => "get"
    );

    $content = http_build_query($data);
    $options = array('http' => array(
        'method' => 'POST',
        'content' => $content
    ));
    $contents = file_get_contents($url, false, stream_context_create($options));
    if($contents === "No user"){
        echo "No user";
        return;
    }

    $csc = new CSC();
    $stamp = (int) $contents;
    $img = $csc->createStampCard($stamp);
    header("Access-Control-Allow-Origin: *");
    header('Content-Type: image/png');
    imagepng($img);
    
}