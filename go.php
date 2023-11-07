<?php
 if(isset($_GET["list"])) {
  $list = (int)$_GET["list"];
  $lfile = "lists/$list.json";
  if(!file_exists($lfile)) {
   header("Location: ?list=1");
   die();
  }
  $fd = fopen($lfile, "r+");
  if(!flock($fd, LOCK_EX)) {
   fclose($fd);
   header("Location: ?list=1");
   die();
  }
  $list = fread($fd, filesize($lfile));
  flock($fd, LOCK_UN);
  fclose($fd);
  $list = json_decode($list);
  $chosen = false;
  $ichosen = 0;
  for($i=0;$i<count($list);$i++) {
   if(random_int(0, 100) < $list[$i]->weight) {
    $chosen = $list[$i];
    $ichosen = $i;
    break;
   }
  }
  if($chosen == false) {
   $ichosen = array_rand($list);
   $chosen = $list[$ichosen];
  }
  print_r($list);
  $list[$ichosen]->hits++;
  copy($lfile, $lfile.".bak");
  $fd = fopen($lfile, "w");
  fwrite($fd, json_encode($list, JSON_PRETTY_PRINT));
  fflush($fd);
  fclose($fd);
  header("HTTP/1.1 302 Moved Temporarily");
  header("Location: {$list[$ichosen]->url}");
  die();
 }
?>
Not found.
