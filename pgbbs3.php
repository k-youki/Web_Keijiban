<?php
$con = pg_connect("dbname=postgres user=postgres password=postgres");
if ($_POST['write']) {
    $name = get_form($_POST['name']);
    if (strlen($name) > 100) exit();
    if (!$name) $name = "名無しさん";
    $title = get_form($_POST['title']);
    if (strlen($title) > 100) exit();
    if (!$title) $title = "無題";
    $contents = get_form($_POST['contents']);
    if (strlen($contents) > 500) exit();
    if (!$contents) error("本文を入力してください");
    $delkey = get_form($_POST['delkey']);
    $expire = time() + 3600 * 24 * 30;
    setcookie("name", $name, $expire);
    setcookie("delkey", $delkey, $expire);
    $tname = $_FILES['image']['tmp_name'];
    if ($tname) {
        if (!is_uploaded_file($tname)) {
            error("不正なアップロード");
        }
        $type = $_FILES['image']['type'];
        if ($type != "image/jpeg" && $type != "image/pjpeg") {
            error("JPEG形式ではありません");
        }
        $rs = pg_query($con, "select last_value from pgbbs3_no_seq");
        $no = pg_fetch_result($rs, 0, 0) + 1;
        $path = "image/$no.jpg";
        move_uploaded_file($tname, $path);
        $path_t = "image/{$no}_t.jpg";
        list($sw, $sh) = getimagesize($path);
        $dw = 128;
        $dh = $dw * $sh / $sw;
        $src = imagecreatefromjpeg($path);
        $dst = imagecreatetruecolor($dw, $dh);
        imagecopyresized($dst, $src, 0, 0, 0, 0, $dw, $dh, $sw, $sh);
        imagejpeg($dst, $path_t);
    }
    pg_query($con, "insert into pgbbs3(name,title,contents,delkey) values('$name','$title','$contents','$delkey')");
} else {
    $name = $_COOKIE['name'];
    $delkey = $_COOKIE['delkey'];
}
if ($_POST['delete']) {
    $no = get_form($_POST['no']);
    $delkey = get_form($_POST['delkey']);
    $rs = pg_query($con, "delete from pgbbs3 where no=$no and delkey='$delkey'");
    if (pg_affected_rows($rs) == 0) error("記事削除に失敗しました");
}

// フォームの文字列を取得する
function get_form($str) {
    $str = pg_escape_string(htmlspecialchars($str));
    $str = ereg_replace("\n|\r|\r\n", "<br>", $str);
    return $str;
}

// エラー表示して終了
function error($msg) {
    print "<p><font color='red'>$msg</font></p>\n";
    exit();
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
    <title>画像アップロード掲示板</title>
</head>
<body>
    <p>JPEGファイルのみアップロードできます。</p>
    <form method="post" action="pgbbs3.php" enctype="multipart/form-data">
        お名前：<input type="text" name="name" value="<?php print $name ?>"><br>
        題　名：<input type="text" name="title"><br>
        <input type="hidden" name="max_file_size" value="30000">
        画像：<input type="file" name="image"><br>
        削除キー：<input type="password" name="delkey" value="<?php print $delkey ?>"><br>
        <textarea name="contents" cols="60" rows="5"></textarea><br>
        <input type="submit" name="write" value="送信">
    </form>
    <hr>
    <form method="post" action="pgbbs3.php">
        記事番号：<input type="text" name="no">
        削除キー：<input type="password" name="delkey" value="<?php print $delkey ?>">
        <input type="submit" name="delete" value="記事削除">
    </form>

    <?php
    $rs = pg_query($con, "select * from pgbbs3 order by no desc");
    while ($row = pg_fetch_array($rs)) {
        $time = substr($row['time'], 0, 19);
        $no = $row['no'];
        print "<hr>No.{$no}　<strong>{$row['title']}</strong>";
        print "　投稿者：{$row['name']}　投稿日時：$time";
        $fn = "image/{$no}.jpg";
        $fn_t = "image/{$no}_t.jpg";
        if (file_exists($fn)) print "<br><br><a href='$fn'><img src='$fn_t' border='0'></a>";
        print "<br><br>{$row['contents']}\n";
    }
    pg_close($con);
    ?>

</body>
</html>
