<?php
//XSS対応（ echoする場所で使用！それ以外はNG ）
function h($str){
    return htmlspecialchars($str, ENT_QUOTES);
}

//DB接続関数：db_connect()
function db_connect() {
    try {
        $db_name =  'uniblog_gs_kadai08_db1';            //データベース名
        $db_host =  'localhost';  //DBホスト
        $db_id =    'root';                //アカウント名(登録しているドメイン)
        $db_pw =    '';           //さくらサーバのパスワード
    
        $server_info = 'mysql:dbname='.$db_name.';charset=utf8;host='.$db_host;
        return new PDO($server_info, $db_id, $db_pw);
    } catch (PDOException $e) {
        exit('DB Connection Error:' . $e->getMessage());
    }
}

//SQLエラー関数：sql_error($stmt)
function sql_error($stmt){
    $error = $stmt->errorInfo();
    exit("SQL Error:".$error[2]);
}

//リダイレクト関数: redirect($file_name)
function redirect($file_name){
    header("Location: ".$file_name); // $file_name(後でファイルを指定)にリダイレクト
    exit();
}

//SessionCheck
function sschk(){
    if(!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"]!=session_id()){
        exit("Login Error");
     }else{
        session_regenerate_id(true); //SESSION KEYを入れ替える！
        $_SESSION["chk_ssid"] = session_id();
     }

}





