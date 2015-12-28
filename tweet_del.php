<?php

  $tweet_id = $_GET["tweet_id"];
  echo $tweet_id;

  $connect = mysql_connect( "localhost","root","" );
  mysql_query( "SET NAMES utf8",$connect );

  mysql_db_query( "test","delete from tweet_tbl where tweet_id = tweet_id" );

  mysql_close( $connect );

?>
