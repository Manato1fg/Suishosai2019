<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

class Contest{

    const USERNAME="suishosai_admin";
    const PASSWORD="SuishosaiSomu73";
    const HOST="mysql1.php.starfree.ne.jp";
    const DB="suishosai_database";

    const PASSWORD2 = "Suishosai_Somu_73";
    const DATA = ["JKL","ABT","CEFGHIJ", "ABCEFGHIJKL", "ABCDEFGHIJKLTU"];

    private static $init_pt = 100.0;
    private static $_init_pt = 200.0;

    private static $names = ["stage", "food", "fun", "poster", "all_contest"];
    private static $_names = ["ステージ発表部門", "調理食販部門", "娯楽・展示部門", "看板部門", "総合部門"];
    private static $counts = [7, 8, 2, 8, 21, 1, 8, 2, 1, 12, 8, 4, 7];
    private static $dict = [
        "A" => 7,
        "B" => 8,
        "C" => 2,
        "E" => 8,
        "F" => 21,
        "G" => 1,
        "H" => 8,
        "I" => 2,
        "J" => 1,
        "K" => 12,
        "L" => 8,
        "T" => 4,
        "U" => 7
    ];
    private static $successMessage = "{name}で、{org}に投票しました。ありがとうございました";


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

    function getPoints($accessToken, $data_index){
        if($this->exist("user", "accessKey", $accessToken)){
            $points_where = "points_" . $data_index;
            $sql = "SELECT $points_where FROM user WHERE accessKey=?";
            $stmt = $this->sqli->prepare($sql);
            $stmt->bind_param("s", $accessToken);
            $stmt->bind_result($res);
            $stmt->execute();
            $stmt->fetch();
            return $res;
        }else{
            return false;
        }
    }

    function updatePoints($accessToken, $data_index, $points_after){
        if($this->exist("user", "accessKey", $accessToken)){
            $points_where = "points_" . $data_index;
            $sql = "UPDATE user SET $points_where=? WHERE accessKey=?";
            $stmt = $this->sqli->prepare($sql);
            $stmt->bind_param("ds", $points_after, $accessToken);
            $stmt->execute();
            $stmt->fetch();
            return true;
        }else{
            return false;
        }
    }

    function updateUserVoted($accessToken, $data_index, $data_org){
        if($this->exist("user", "accessKey", $accessToken)){
            if(strpos($this->getUserVoted($accessToken, $data_index), $data_org) === false){
                $votes_where = "voted_" . $data_index;
                $sql = "UPDATE user SET $votes_where=CONCAT($votes_where,?) WHERE accessKey=?";
                $stmt = $this->sqli->prepare($sql);
                $data_org1 = $data_org.",";
                $stmt->bind_param("ss", $data_org1, $accessToken);
                $stmt->execute();
                $stmt->fetch();
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function getUserVoted($accessToken, $data_index){
        if($this->exist("user", "accessKey", $accessToken)){
            $votes_where = "voted_" . $data_index;
            $sql = "SELECT $votes_where FROM user WHERE accessKey=?";
            $stmt = $this->sqli->prepare($sql);
            $stmt->bind_param("s", $accessToken);
            $stmt->execute();
            $stmt->bind_result($result);
            $stmt->fetch();
            return $result;
        }else{
            return false;
        }
    }

    /**
     * @param
     * $data_index(int): 0~4
     * $data_org(string): ex) A-1
     */
    function vote($accessToken, $data_index, $data_org){
        if($data_index < 0 || $data_index > 4){
            echo "不正なデータです";
            exit();
        }
        $data_org = str_replace("\"", "", $data_org);
        $data_org = str_replace("-", "_", $data_org);
        
        if(strpos(self::DATA[$data_index], substr($data_org, 0, 1)) !== false && strpos($data_org, "_") !== false){

            if(
                (int) explode("_", $data_org)[1] < 1 ||
                (int) explode("_", $data_org)[1] > self::$dict[substr($data_org, 0, 1)] 
            ){
                echo "不正なデータです";
                exit();
            }

            $points_prev = $this->getPoints($accessToken, $data_index);
            $points_after = 0;
            if($points_prev === self::$_init_pt){
                $points_prev = 0;
                $points_after = self::$init_pt;
            }else{
                $points_after = $this->roundToAny(self::$init_pt * $points_prev / (self::$init_pt + $points_prev), 0.5);
            }

            $points = $points_after - $points_prev;
            $data = self::DATA[$data_index];
            if(!$this->updateUserVoted($accessToken, $data_index, $data_org)){
                echo str_replace("{name}",self::$_names[$data_index],"すでに{name}でこの団体に投票しています。");
                exit();
            }
            $orgs = explode("," , $this->getUserVoted($accessToken, $data_index));
            $d = self::$names[$data_index];
            for($i = 0; $i < count($orgs); $i++){
                if($orgs[$i] === "") continue;
                $sql = "UPDATE $d SET `points` = `points` + ? WHERE org=?";
                $k = $points;
                if($orgs[$i] === $data_org){
                    $k = $points_after;
                }
                $sql = "UPDATE $d SET `points` = `points` + ? WHERE org=?";

                $stmt = $this->sqli->prepare($sql);
                $stmt->bind_param("ds", $k, $orgs[$i]);
                $stmt->execute();
            }

            $this->updatePoints($accessToken, $data_index, $points_after);
            $data_org = str_replace("_", "-", $data_org);
            echo str_replace("{org}", $data_org, str_replace("{name}", self::$_names[$data_index], self::$successMessage));
            return true;
        }else{
            return false;
        }
    }

    function init_table(){
        $counts = [7, 8, 2, 8, 21, 1, 8, 2, 1, 12, 8, 4, 7];
        $str = "ABCEFGHIJKLTU";
        for($i = 0; $i < count(self::$names); $i++){
            $name = self::$names[$i];
            $data = self::DATA[$i];
            for($j = 0; $j < strlen($str); $j++){
                if(strpos($data, $str[$j]) !== false){
                    for($k = 1; $k <= $counts[$j]; $k++){
                        $org = $str[$j] . "_" . $k;
                        $points = 0;
                        $sql = "UPDATE $name SET points=? WHERE org=?";
                        $stmt = $this->sqli->prepare($sql);
                        $stmt->bind_param("ds", $points, $org);
                        $stmt->execute();
                    }
                }
            }
        }
    }

    function getResult(){
        $results = [];
        for($i = 0; $i < count(self::$names); $i++){
            $name = self::$names[$i];
            $sql = "SELECT * FROM $name ORDER BY points DESC LIMIT 5";
            $results[$name] = [];
            if($result = mysqli_query($this->sqli, $sql)){
                $k = 0;
                while ($myrow = $result->fetch_array(MYSQLI_ASSOC)){
                    $results[$name][$k] = [];
                    $results[$name][$k]["org"] = $myrow["org"];
                    $results[$name][$k]["points"]    = $myrow["points"];
                    $k++;
                }
            }
        }

        echo json_encode($results);
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

    function roundToAny($x, $k){
        return round($x / $k) * $k;
    }
}

$contest = new Contest();

if(!isset($_POST["order"])){
    echo "Invalid argument";
    exit();
}

$order = $_POST["order"];

/*if($order === "init"){
    $contest->init_table();
    echo "init succeed";
    exit();
}*/

if($order === "result"){
    $contest->getResult();
    exit();
}

if(!isset($_POST["accessToken"])){
    echo "Invalid argument";
    exit();
}

date_default_timezone_set('Asia/Tokyo');

$today = date("Y-m-d H:i:s");
$target_day = "2019-06-22 09:30:00";
$target_day2 = "2019-06-23 15:30:00";
if(strtotime($today) <= strtotime($target_day)){
    echo "6月22日9:30までは投票できません。";
    exit();
}else if(strtotime($today) >= strtotime($target_day2)){
    echo "翠翔祭グランプリはもう終了しました。";
    exit();
}

$accessToken = $_POST["accessToken"];

if($order === "vote"){
    if($contest->exist("user", "accessKey", $accessToken)){
        if(isset($_POST["data_index"]) && isset($_POST["data_org"])){
            if($contest->vote($accessToken, $_POST["data_index"], $_POST["data_org"])){
                //echo "Successfully Voted";
            }else{
                echo "不正なデータです";
            }
            exit();
        }else{
            echo "Invalid argument";
            exit();
        }
    }else{
        echo "Invalid";
        exit();
    }
}