<?php

//サニタイズ
foreach ($_POST as $key=>$value) {
    $sanitizedPost[$key]=htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

//フラグの取得
$request=$sanitizedPost['request'];

//データベース呼び出し
$dsn='mysql:dbname=posts;host=localhost;charset=UTF8';
$user='root';
$password='';
$dbh=new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//新規作成の処理
if ($request==='create') {
    //字数のバリデーション
    if (mb_strlen($sanitizedPost['title'])>50 or $sanitizedPost['title']==='' or $sanitizedPost['contents']==='') {
        $dbh=null;
        header('Location:ng.html');
        exit();
    } else {
        //タイトルと内容を新規作成
        $sql="INSERT INTO posts VALUES (DEFAULT, :title, :content, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $stmt=$dbh->prepare($sql);
        $stmt->bindParam(":title", $sanitizedPost['title'], PDO::PARAM_STR);
        $stmt->bindParam(":content", $sanitizedPost['contents'], PDO::PARAM_STR);
        $stmt->execute();
        $dbh=null;

        //index.phpに戻る
        header('Location:index.php');
        exit();
    }
}

//編集の処理
if ($request==='edit') {
    //字数のバリデーション
    if (mb_strlen($sanitizedPost['title'])>50 or $sanitizedPost['title']==='' or $sanitizedPost['contents']==='') {
        $dbh=null;
        header('Location:ng.html');
        exit();
    } else {
        //ID情報に基づいてタイトルと内容を更新
        $sql="UPDATE posts SET title=:title, content=:content, updated_at=CURRENT_TIMESTAMP WHERE ID=:ID";
        $stmt=$dbh->prepare($sql);
        $stmt->bindParam(":ID", $sanitizedPost['id'], PDO::PARAM_INT);
        $stmt->bindParam(":title", $sanitizedPost['title'], PDO::PARAM_STR);
        $stmt->bindParam(":content", $sanitizedPost['contents'], PDO::PARAM_STR);
        $stmt->execute();
        $dbh=null;

        //index.phpに戻る
        header('Location:index.php');
        exit();
    }
}

//削除の処理
if ($request==='delete') {
    //ID情報に基づいてタイトルと内容を削除
    $sql="DELETE FROM posts WHERE ID=:ID";
    $stmt=$dbh->prepare($sql);
    $stmt->bindParam(":ID", $sanitizedPost['id'], PDO::PARAM_INT);
    $stmt->execute();
    $dbh=null;

    //index.phpに戻る
    header('Location:index.php');
    exit();
}
