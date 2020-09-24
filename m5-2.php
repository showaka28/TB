<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>keijiban</title>
   
</head>
<body>
    
<?php

    $comment = $_POST["comment"] ;
    $name = $_POST["name"];
    $date = date("Y/m/d H:i:s");
        
        
    $pass=$_POST["pass"];
    $edipass=$_POST["edipass"];
    $delpass=$_POST["delpass"];
    
    $e=true;
    
    //データベース名：***
    //ユーザ名：***
    //パスワード：***
    
    //DB接続設定
    $dsn='mysql:dbname=***;host=localhost';
    $user='***';
    $password='***';
    $pdo=new PDO($dsn,$user,$password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //echoらへんのやつ
    function forecho(){
        //入力したデータレコードを取り出し、表示
        
        global $pdo;//function外から関数を引用
        
        $sql = "SELECT * FROM tbtest111";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row["id"].",";
            echo $row["name"].",";
            echo $row["comment"].",";
            echo $row["date"]."<br>";
            
            echo "<hr>";
        }
    }
    //新規投稿用
    //()の中の関数に後からデータが引き渡される
    function newrecord($newname, $newcomment, $newpass){
        
        global $pdo;
        
        $sql=$pdo->prepare("INSERT INTO tbtest111(name,comment,date,pass)VALUES(:name,:comment,:date,:pass)");
        $sql->bindParam(":name",$name,PDO::PARAM_STR);
        $sql->bindParam(":comment",$comment,PDO::PARAM_STR);
        $sql->bindParam(":date",$date,PDO::PARAM_STR);
        $sql->bindParam(":pass",$pass,PDO::PARAM_STR);
        $name=$newname;//好きな名前
        $comment=$newcomment;//好きなコメ
        $date= date("Y/m/d H:i:s");
        $pass=$newpass;//好きなパスワード
        $sql->execute();    
    }
    
    //編集用
    function editrecord($num, $rename, $newcomment, $newpass){
        
        global $pdo;
        
        $id=$num;//変更したい投稿番号
        $name=$rename;//変更後の名前
        $comment=$newcomment;//変更後のコメント
        $date= date("Y/m/d H:i:s");
        $pass=$newpass;
        
        $sql="UPDATE tbtest111 SET name=:name,comment=:comment, date=:date, pass=:pass WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":comment", $comment, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":date", $date, PDO::PARAM_STR);
        $stmt->bindParam(":pass", $pass, PDO::PARAM_STR);
        $stmt->execute();
    }
    
    //削除用
    function deleterecord($num){
        
        global $pdo;
        
        $id = $_POST["delete"];
        $sql = 'delete from tbtest111 where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
    }
    
    //データベースサーバにテーブルを作成。毎回やる必要ないかも
    //テーブル名tbtest111
    $sql="CREATE TABLE IF NOT EXISTS tbtest111"
    ."("
    ."id INT AUTO_INCREMENT PRIMARY KEY,"//自動で登録されるナンバリング
    ."name char(32),"//名前を入れる。半角英数で32文字
    ."comment TEXT,"//コメント。文字列や長めの文も入る
    ."pass char(32),"//パスワードを入れる。半角英数で32文字
    ."date DATETIME"
    .");";
    $stmt=$pdo->query($sql);
    
    
    //編集選択機能 
    if($_POST["edit1"]){
        if(!empty($_POST["edipass"])){//パスワードがはいってたら
            
            //データ持ってくる
            $sql = "SELECT * FROM tbtest111";
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            
            foreach ($results as $result){
                if($_POST["edipass"]===$result["pass"]){//パスワードが一致していたら
                    
                    if($_POST["edit"]===$result["id"]){//編集したい番号と、DB内の各行のうちIDが一致したら
                        $editnumber=$result["id"];
                        $editname=$result["name"];
                        $editcomment=$result["comment"];
                        $edipass=$result["pass"];
                    }
                }
            }
        }
        if($e){
            forecho();
        }
    }
 
    if($_POST["submit1"]){
        //データレコードの挿入（データの登録）＝新規投稿？
        if(empty($_POST["edited"])
             &&!empty($_POST["pass"])
             &&!empty($_POST["comment"])&&!empty($_POST["name"])){
            
                newrecord($_POST["name"], $_POST["comment"], $_POST["pass"]);    
	    
    	    if($e){
                forecho();
            }
        }
    	
    //入力されているデータレコードの編集
        
        //投稿番号がある時編集機能
        elseif(!empty($_POST["edited"])&&!empty($_POST["pass"])){
             //データ持ってくる
            $sql = "SELECT * FROM tbtest111";
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            
            foreach ($results as $result){
                if($_POST["edited"]=== $result["id"]){
                    editrecord($_POST["edited"], $_POST["name"], $_POST["comment"], $_POST["pass"]);
              
                }
            }
            if($e){
                forecho();
            }
        }
	}
    
    //データレコードの削除
    if($_POST["delete1"]){
        $delete=$_POST["delete"];
            
        //データ持ってくる
        $sql = "SELECT * FROM tbtest111";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        
        if(!empty($_POST["delpass"])){
            foreach ($results as $result){
                if($delpass===$result["pass"]){//パスワードが一致したら
                    
                    foreach ($results as $result){
                        if($delete === $result["id"]){
                            deleterecord($num);
                        }
                    }
                }
            }
        }
        if($e){
            forecho();
        }
    }
	

?>

<form action="" method="post">
        <input type="text" name="name" placeholder="名前"
            value="<?php echo$editname;?>">
         
     <br>   
        <input type="text" name="comment" placeholder="コメント"
            value="<?php echo$editcomment;?>">
        <input type="hidden" name="edited" value="<?php echo $editnumber;?>">
    <br>
        <input type="text" name="pass" placeholder="パスワード">
   
        <input type="submit" name="submit1">
    </form>
    <br>
    <br>    
    <form action="" method="post">
        <input type="num"  name="delete"  placeholder="削除番号指定用フォーム">
        <input type="text" name="delpass" placeholder="パスワード">
        <input type="submit" name="delete1" value="削除">
    <br>
        <input type="num" name="edit" placeholder="編集したい番号">
        <input type="text" name="edipass" placeholder="パスワード">
        <input type="submit" name="edit1" value="編集">
    
    </form>
    
<?php
    //パスワードなしでボタンが押されたら
    if(empty($_POST["pass"])&&$_POST["submit1"]
     ||empty($_POST["delpass"])&&$_POST["delete1"]
     ||empty($_POST["edipass"])&&$_POST["edit1"]){
        
        echo "パスワードを入力してください。<br>";
        
        if($e){
            forecho();
        }
    }
    elseif($_POST["submit1"]&&empty($_POST["comment"])
     ||$_POST["submit1"]&&empty($_POST["name"])
     ||$_POST["submit1"]&&empty($_POST["comment"])&&empty($_POST["name"])){
        echo "名前、コメントを入力してください。<br>";
            
        if($e){
            forecho();
        }
    }
    //ページを開いた時の表示用
    if(empty($_POST["edit1"])&&empty($_POST["submit1"])&&empty($_POST["delete1"])){
        if($e){
                forecho();
        }
    }
?>

</body>
</html>