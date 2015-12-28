<tbody>
  <tr>
    <td>小谷優樹</td>
    <td>
      <?php
        $contents = $_GET["contents"];
        if( $contents == "" ){
          echo "空白です";
        }else if( 140 < mb_strlen($contents,"utf-8") ){
          echo "文字数オーバーです";
        }else{
          echo $contents;
      }
      ?>
    </td>
    <td>test</td>
