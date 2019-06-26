<?php

/**
 * Basic認証を要求するページの先頭で使う関数
 * 初回時または失敗時にはヘッダを送信してexitする
 *
 * @return string ログインしたユーザ名
 */
function require_basic_auth()
{
    // 事前に生成したユーザごとのパスワードハッシュの配列
    $hashes = [
        'suishosai' => '$2y$10$7rjhY8N8gTbUX8wSzfsOdObbkNVSihkNOWLHEYI69b46hW74BLN5e',
    ];

    if (
        !isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) ||
        !password_verify(
            $_SERVER['PHP_AUTH_PW'],
            isset($hashes[$_SERVER['PHP_AUTH_USER']])
                ? $hashes[$_SERVER['PHP_AUTH_USER']]
                : '$2y$10$abcdefghijklmnopqrstuv' // ユーザ名が存在しないときだけ極端に速くなるのを防ぐ
        )
    ) {
        // 初回時または認証が失敗したとき
        header('WWW-Authenticate: Basic realm="Enter username and password."');
        header('Content-Type: text/plain; charset=utf-8');
        exit('このページを見るにはログインが必要です');
    }

    // 認証が成功したときはユーザ名を返す
    return $_SERVER['PHP_AUTH_USER'];
}

/**
 * htmlspecialcharsのラッパー関数
 *
 * @param string $str
 * @return string
 */
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

class Change{
    //JavaScriptから呼び出して良いクラスか確認のため記述する
    static $JS_ENABLE;  
    public static function JS_change($org, $status, $reason){
        $file = file_get_contents("status.json");
        $obj = json_decode($file);
        $obj[$org] = [
            "status" => $status,
            "reason" => $reason
        ];
        file_put_contents("status.json", json_encode($obj));
    }   
}