<?php

require_once __DIR__ . '/function.php';
$username = require_basic_auth();

header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<head>
    <title>ステータス</title>
	<!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <div class="container">
            <div class="form-group">
                <label for="exampleInputEmail1">団体コード</label>
                <select class="custom-select" id="orgs">
                    <option value="none" selected>団体コードを選択して下さい</option>
                </select>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">色</label>
                <select class="custom-select" id="status">
                    <option value="on" selected>緑(運営中など)</option>
                    <option value="off" selected>赤(売り切れ間近や売り切れなど)</option>

                </select>
            </div>
            <div class="form-group">
                <label for="reason">文章(横に表示されます)</label>
                <input type="text" class="form-control" id="reason" placeholder="理由" >
            </div>
            <button class="btn btn-primary" onclick="submit()">送信</button>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script>
        var dict = {
            "A" : 7,
            "B" : 8,
            "C" : 2,
            "E" : 8,
            "F" : 21,
            "G" : 1,
            "H" : 8,
            "I" : 2
        }

        var pair = {
            "A-1": "鶏林 ～マジチキオオバヤシ～",
            "A-2": "しげみうどん",
            "A-3": "やきそばうゑた屋",
            "A-4": "シャカぐちポテト",
            "A-5": "ステーキ丼だけ～",
            "A-6": "いっちゃん総本店",
            "A-7": "格子点の個数を求めなさい",
            "B-1": "SUIRAN BUCKS",
            "B-2": "おじゃわ teaCHA",
            "B-3": "CATS",
            "B-4": "ブルゥ　スィール",
            "B-5": "Me too eat CHURROS",
            "B-6": "ポップコーンモンスター",
            "B-7": "茶道部",
            "B-8": "翠嵐万十博物館",
            "C-1": "Art club",
            "C-2": "【新コーナー】 シメキリリッリ",
            "E-1": "CANDY  A☆GO☆RO！",
            "E-3": "吉吉神社",
            "E-4": "ゴキげんよう",
            "E-5": "菜摘王の称号を目指せ！",
            "E-6": "隠密",
            "E-7": "令和でパイ投げつれーわ",
            "E-8": "神社　オブ　テラー",
            "F-1": "VSi嵐",
            "F-2": "ハウルのめっちゃ動く的",
            "F-3": "投げ処　TAKASHI",
            "F-4": "文芸部が部誌を無料で配布するよ",
            "F-5": "シンジて撃て！",
            "F-6": "補講なんてほっとこう",
            "F-7": "IMPOSSIBLE",
            "F-8": "招待試合",
            "F-9": "なんで、私が クイ研に!?",
            "F-10": "パシられ隊",
            "F-11": "棋道部",
            "F-12": "ストラックアウト",
            "F-13": "招待試合",
            "F-14": "翠嵐かるた会",
            "F-15": "MOGURAS, INC.",
            "F-16": "ミリオンGo！",
            "F-17": "迷探偵コナソ",
            "F-18": "GAME CENTER OPEN",
            "F-19": "鉄道研究同好会",
            "F-20": "Pirates ・ of ・ けいちゃん",
            "F-21": "翠和会",
            "G-1": "CATASＴROPHE -呪われた鉱山-",
            "H-1": "Change the World～科学部～",
            "H-2": "トラッシュバスターず",
            "H-3": "書道部",
            "H-4": "国際交流委員会",
            "H-5": "数学研究部",
            "H-6": "IT研究部",
            "H-7": "保健委員会",
            "H-8": "写真部",
            "I-1": "放送委員会映像班",
            "I-2": "ふしぎな短編演劇",
            "J-1": "ポピュラーソング部",
            "K-1": "Clear Cider",
            "K-2": "箪笥",
            "K-3": "スマルト",
            "K-4": "さけいくら",
            "K-5": "Vegetable's",
            "K-6": "圧倒的庶民派",
            "K-7": "MINT BLUE",
            "K-8": "裏Ms.翠嵐",
            "K-9": "すぱげてぃ",
            "K-10": "オパビニアの子孫",
            "K-11": "Ms.&Mr. 翠嵐",
            "K-12": "プリティダンスバトル",
            "L-2": "サッカー部",
            "L-3": "朝鮮中高級学校",
            "L-4": "YSDC ~嵐踊者~",
            "L-5": "スイラン！ブラバン！ビックバン！！",
            "L-6": "Suiran String Orchestra 2019",
            "L-7": "フーチコイワカ",
            "L-8": "天使のいる音楽会",
            "T-1": "やきそば",
            "T-2": "鳥市民",
            "T-3": "フランクフルト",
            "T-4": "たこせん",
            "T-5": "定時制多文化共生委員会",
            "U-1": "定時制演劇部『ユダ』",
            "U-2": "定時制保健委員会",
            "U-3": "定時制イラスト・デザイン部",
            "U-4": "写真展",
            "U-5": "Spiral road",
            "U-6": "定時制合唱部青空コンサート",
            "O-1": "案内所"
        }

        function on(){
            var orgs = document.getElementById("orgs");
            Object.keys(dict).forEach(x => {
                for(var j = 1; j <= dict[x]; j++){
                    let op = document.createElement("option");
                    op.value = x + "-" + j;
                    op.text = x + "-" + j + ": " + pair[x + "-" + j];
                    orgs.appendChild(op);
                }
            });
            let op = document.createElement("option");
            op.value = "reset";
            op.text = "reset";
            orgs.appendChild(op);
        }

        on();

        function submit(){
            var org = document.getElementById("orgs").value;
            var _status = document.getElementById("status").value;
            var reason = document.getElementById("reason").value;

            if(org === "none"){
                alert("ダメです");
                return;
            }

            var data = createRequest("org", org) + "&" + createRequest("status", _status) + "&" + createRequest("reason", reason);

            postData("https://suishosai-server-php.herokuapp.com/updateStatus.php", data, function(e){
                var status = e.target.status;
                var readyState = e.target.readyState;
                var response = e.target.responseText;
                if (status === 200 && readyState === 4) {
                    alert("おっけーでした");
                    location.href = "./status.php";
                }       
            })
        }

        function createRequest(name, value) {
            return name + "=" + value;
        }

        function postData(url, data, callback) {

            var xhr = new XMLHttpRequest();

            xhr.open('POST', url);
            xhr.setRequestHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');

            xhr.onreadystatechange = callback;

            xhr.send(data);
        }
    </script>
</body>