<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Page</title>
</head>
<body>
<?php

//データベース呼び出し
$dsn='mysql:dbname=posts;host=localhost;charset=UTF8';
$user='root';
$password='';
$dbh=new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//ID情報に基づいてタイトルと内容を呼び出す
$sql="SELECT title,content FROM posts WHERE ID=:ID";
$stmt=$dbh->prepare($sql);
$stmt->bindParam(":ID",$_POST['id'],PDO::PARAM_INT);
$stmt->execute();
$rec=$stmt->fetch(PDO::FETCH_ASSOC);

$dbh=null;

?>
<h1>
    Edit Todo Page
</h1>
<form method="post" action="model.php">
    <div style="margin: 10px">
        <label for="title">タイトル：</label>
        <input id="title" type="text" name="title" value="<?php print $rec['title']?>">
    </div>
    <div style="margin: 10px">
        <label for="content">内容：</label>
        <textarea id="content" name="contents" rows="8" cols="40"><?php print $rec['content']?></textarea>
    </div>
    <input name="id" type="hidden" value="<?php print $_POST['id'];?>">
    <input name="request" type="hidden" value="edit">
    <input type="submit" name="post" value="編集する">
</form>
<form action="index.php">
    <button type="submit" name="back">戻る</button>
</form>
</body>
</html>