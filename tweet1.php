<?php

  $connect = mysql_connect( "localhost","root","" );
  mysql_query( "SET NAMES utf8",$connect );
  $contents = $_GET["contents"];
  $account = $_GET["account"];
  $len = mb_strlen( $contents,"utf-8" );
  $result;

  if( $len == 0 ){
    echo "空白です";
  }else if( $len >= 140 ){
    echo "文字数オーバーです。";
  }else{
    mysql_db_query( "test","insert tweet_tbl(account,contents,input_datetime)
    values('$account','$contents',sysdate())" );
    $result = mysql_db_query( "test", "select * from tweet_tbl");

    while( true ){
      $row = mysql_fetch_assoc( $result );
      if( $row == null ){
        break;
      }else{
        echo $row["tweet_id"];
        echo $row["account"];
        echo $row["contents"];
        echo $row["input_datetime"];
        $tweet_id = $row["tweet_id"];
        echo "<a href='tweet_del.php?tweet_id=$tweet_id'>削除</a>";
        echo "<br>";
      }
    }
  }

  mysql_close( $connect );

?>
