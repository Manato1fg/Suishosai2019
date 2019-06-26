<?php

class Register{

    const USERNAME="suishosai_admin";
    const PASSWORD="SuishosaiSomu73";
    const HOST="mysql1.php.starfree.ne.jp";
    const DB="suishosai_database";

    const PASSWORD2 = "Suishosai_Somu_73";

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

    function password($password){
        return $password === self::PASSWORD2;
    }

    function register($accessToken){
        if(!$this->exist("user", "accessKey", $accessToken)){
            $sql = "INSERT INTO user (accessKey) VALUE(?)";
            $stmt = $this->sqli->prepare($sql);
            $default = 0;
            $stmt->bind_param("s", $accessToken);
            $stmt->execute();
            return true;
        }else{
            return false;
        }
    }

    function exist($table, $name, $data){
        $stmt = $this->sqli->prepare("SELECT EXISTS (SELECT * FROM $table WHERE $name=?);");
        $stmt->bind_param("s", $data);
        $stmt->bind_result($exists);
        $stmt->execute();
        $stmt->fetch();
        return $exists;
    }

    function close(){
        $this->sqli->close();
    }

    function __destruct(){
        $this->close();
    }
}

$r = new Register();

if(isset($_POST["salt"]) && isset($_POST["order"]) && isset($_POST["accessToken"])){
    if(!$r->password($_POST["salt"])){
        echo "Invalid argument";
        exit();
    }
}else{
    echo "Invalid argument";
    exit();
}

$order = $_POST["order"];
$accessToken = $_POST["accessToken"];

if($order === "register"){
    if($r->register($accessToken)){
        echo "Successfully Registered";
    }else{
        echo "You have already logged in";
    }
    exit();
}