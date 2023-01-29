<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Page</title>
</head>
<body>

<?php
//検索ワードを踏まえたデータの取得

//サニタイズして検索した文字列を配列化
$sanitizedGetSearch=htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8');
$search=explode(" ", mb_convert_kana($sanitizedGetSearch, 's'));

foreach($search as $value){
    //配列$searchから、SQL文のLIKE句の部分を作りつつexecuteの引数すなわちワイルドカードを埋める部分を作成する。
    $searchCondition[]="(title LIKE ?)";
    $values[]='%'.addcslashes($value, '%_\\').'%';
}

try {
    //データベース起動
    $dsn='mysql:dbname=posts;host=localhost;charset=UTF8';
    $user='root';
    $password='';
    $dbh=new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //配列$searchConditionをimplode関数で文字列へと一本化し、プリペアードステートメントで実行し、データを取得。
    $searchCondition=implode('AND', $searchCondition);
    $sql="SELECT * FROM posts WHERE $searchCondition";
    $stmt=$dbh->prepare($sql);
    $stmt->execute($values);
    $rec=$stmt->fetchAll(PDO::FETCH_ASSOC);//検索条件を満たしたデータは一旦すべて取得する。ページングの際はarray_splice関数を用いて再度データを抽出する。
    $dbh=null;

}catch(Exception $e)
{
    echo 'データベースにてエラーが発生しました。';
    exit();
}


//ページを表示するための準備
define('PAGEMIM',1); //113行目にて使用する定数。ページ番号の最小値を示す。
define('MAX',5); //１ページ内の最大表示数を定義。
$numberOfData=count($rec); //取得したデータの数を変数に代入する。
$maxPage=ceil($numberOfData/MAX);//トータルページ数を定義する。

if (!isset($_GET['pageId'])) { // $_GET['pageId'] はURLに渡された現在のページ数
    $now=1; // 設定されてない場合は1ページ目にする
} else {
    $now=$_GET['pageId'];
}

$startNumber=($now-1)*MAX; // 配列の何番目から取得すればよいか
$displayData=array_slice($rec, $startNumber, MAX, true); //表示するデータを格納した配列を作る。
?>

<!--データの表示-->
<h2>
    検索結果
</h2>
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

    foreach($displayData as $value){
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
    echo '<a href="search.php?pageId='.($now-1).'&search='.$_GET['search'].'">前へ</a>';
} else {
    echo '　　';
}

//ページを表示する。
for ($i=1;$i<=$maxPage;$i++) { 
    if($i==$now) {
        echo $now; //現在表示中のページ数の場合は文字列のみを表示。
    }else{
        echo '<a href="search.php?pageId='. $i. '&search='.$_GET['search'].'">'. $i. '</a>';
    }
}

//「次へ」を付けるかを判定する。
if($now<$maxPage){
    echo '<a href="search.php?pageId='.($now+1).'&search='.$_GET['search'].'">次へ</a>';
} else {
    echo '　　';
}


//何もヒットしなかった場合にメッセージを表示する。
if(empty($displayData)===true)
{
    print '<br/>';
    print '検索条件と十分に一致する結果が見つかりませんでした。';
}
?>

</br>
<a href="index.php">戻る</a>

</body>
</html>