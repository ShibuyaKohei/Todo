<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Index Page</title>
</head>
<body>
<?php

//データの取得
try {
    //データベース起動
    $dsn='mysql:dbname=posts;host=localhost;charset=UTF8';
    $user='root';
    $password='';
    $dbh=new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //データの取得
    $sql='SELECT ID,title,content,created_at,updated_at FROM posts WHERE 1';
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
    $rec=$stmt->fetchAll(PDO::FETCH_ASSOC); //検索条件を満たしたデータは一旦すべて取得する。ページングの際はarray_splice関数を用いて再度データを抽出する。
    $dbh=null;

}catch(Exception $e)
{
    echo 'データベースにてエラーが発生しました。';
    exit();
}


//ページを表示するための準備
define('PAGEMIM',1); //118行目にて使用する定数。ページ番号の最小値を示す。
define('MAX', 5); //１ページ内の最大表示数を定義。
$numberOfData=count($rec); //取得したデータの数を変数に代入する。
$maxPage=ceil($numberOfData/MAX);//トータルページ数を定義する。

if(!isset($_GET['pageId'])) { // $_GET['pageId'] はURLに渡された現在のページ数
    $now = 1; // 設定されてない場合は1ページ目にする
}else{
    $now = $_GET['pageId'];
}

$startNumber=($now-1)*MAX; // 配列の何番目から取得すればよいか
$displayData=array_slice($rec,$startNumber,MAX,true); //表示するデータを格納した配列を作る。

?>


<!--データの表示-->
<h1>
    ToDo List Page
</h1>
<form action="create.html">
    <button type="submit" style="padding: 10px;font-size: 16px;margin-bottom: 10px">New Todo</button>
</form>
<h2>
    タイトル検索
</h2>
<form method= "get" action="search.php">
    <input type="text" name="search" style="width:300px">
    <input type="submit" value="検索">
</form>

<table border="1">
    <colgroup span="4"></colgroup>
    <tr>
        <th>ID</th>
        <th>タイトル</th>
        <th>内容</th>
        <th>作成日時</th>
        <th>更新日時</th>
        <th>編集</th>
        <th>削除</th>
    </tr>
  

    <?php

    foreach ($displayData as $value) {
        ?>
    <tr>
        
        <td><?php echo $value['ID'];?></td>
        <td><?php echo $value['title']?></td>
        <td><?php echo $value['content']?></td>
        <td><?php echo $value['created_at']?></td>
        <td><?php echo $value['updated_at']?></td>
        <td>
            <form method="post" action="edit.php">
                <button type="submit" style="padding: 10px;font-size: 16px;" name="id" value="<?php print $value['ID']?>">編集する</button>
            </form>
        </td>
        <td>
            <form method="post" action="model.php">
                <button type="submit" style="padding: 10px;font-size: 16px;">削除する</button>
                <input name="id" type="hidden" value="<?php print $value['ID'];?>">
                <input name="request" type="hidden" value="delete">
            </form>
        </td>
    </tr>


<?php
}
?>

</table>

<?php
//aタグを用いたページの表示。URLパラメータには現在ページと検索ワードを格納している。

//「前へ」を付けるかを判定する。
if($now>PAGEMIM){
    echo '<a href="index.php?pageId='.($now-1).'">前へ</a>';
} else {
    echo '　　';
}

//ページを表示する。
for ($i=1;$i<=$maxPage;$i++) {
    if($i==$now) {
        echo $now; //現在表示中のページ数の場合は文字列のみを表示。
    }else{
        echo '<a href="index.php?pageId='. $i. '">'. $i. '</a>';
    }
}

//「次へ」を付けるかを判定する。
if($now<$maxPage){
    echo '<a href="index.php?pageId='.($now+1).'">次へ</a>';
} else {
    echo '　　';
}
?>

</body>
</html>