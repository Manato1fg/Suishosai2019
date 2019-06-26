<?php

class StampRally{

    const USERNAME="suishosai_admin";
    const PASSWORD="SuishosaiSomu73";
    const HOST="mysql1.php.starfree.ne.jp";
    const DB="suishosai_database";

    private $sqli = null;

    function __construct(){
        $this->sqli = $this->getConnection();
    }

    private function getConnection(){
        $username = self::USERNAME;
        $password = self::PASSWORD;
        $host = self::HOST;
        $db = self::DB;
        $mysqli = new mysqli($host, $username, $password, $db);

        if ($mysqli->connect_error) {
            echo $mysqli->connect_error;
            exit();
        } else {
            $mysqli->set_charset("utf8");
        }

        return $mysqli;
    }

    function get($data){
        $stmt = $this->sqli->prepare("SELECT `id` FROM `stamprally` WHERE `text` = ?");
        $stmt->bind_param("s", $data);
        $stmt->bind_result($id);
        $stmt->execute();
        $stmt->fetch();
        return $id;
    }

    function getUserData($accessToken){
        $stmt = $this->sqli->prepare("SELECT `stampRally` FROM `user` WHERE `accessKey` = ?");
        $stmt->bind_param("s", $accessToken);
        $stmt->bind_result($stamp);
        $stmt->execute();
        $stmt->fetch();
        return $stamp;
    }

    function update($accessToken, $data){
        $stmt = $this->sqli->prepare("UPDATE `user` SET `stampRally` = ? WHERE `accessKey` = ?");
        $stmt->bind_param("is", $data, $accessToken);
        $stmt->execute();
        return;
    }

    function toAry($num){
        $res = [0, 0, 0, 0, 0];
        $i = 0;
        while($num !== 0){
            $remainder = $num % 2;
            $num = ($num - $remainder) / 2;
            $res[$i] = $remainder;
            $i++;
        }
        return $res;
    }

    function toInt($ary){
        $res = 0;
        for($i = 0; $i < count($ary); $i++){
            $res += pow(2, $i) * $ary[$i];
        }
        return $res;
    }

    function append($data, $id){
        $ary = $this->toAry($data);
        $ary[$id] = 1;
        $d = $this->toInt($ary);
        return $d;
    }

    function close(){
        $this->sqli->close();
    }

    function __destruct(){
        $this->close();
    }
}

if(isset($_POST["content"]) && isset($_POST["accessToken"])){
    $content = $_POST["content"];
    $accessToken = $_POST["accessToken"];

    $sr = new StampRally();

    if(($res = $sr->get($content)) !== null){
        if(($stamp = $sr->getUserData($accessToken)) !== null){
            $sr->update($accessToken, $sr->append((int) $stamp, (int) $res));
            if($sr->getUserData($accessToken) === $stamp){
                echo "already got";
            }else{
                echo $res;
            }
        }else{
            echo "No user";
        }
    }
    else{
        echo "No result";
    }
}else if(isset($_POST["order"]) && isset($_POST["accessToken"])){
    if($_POST["order"] !== "get"){
        return;
    }
    $accessToken = $_POST["accessToken"];

    $sr = new StampRally();

    if(($stamp = $sr->getUserData($accessToken)) !== null){
        echo $stamp;
    }else{
        echo "No user";
    }
}/*else{
    $val = $_POST["test"];
    $get = $_POST["get"];
    $sr = new StampRally();
    var_dump($sr->toAry((int)$val));
    var_dump($sr->toInt($sr->toAry((int)$val)));
    var_dump($sr->toAry($sr->append((int)$val, (int)$get)));
}*/