<?php
 $password = "jsontds";
 if(isset($_POST["password"]) && $_POST["password"] == $password) {
  setcookie($password, "true");
  header("Location: ?");
  die();
 }
 if(!isset($_COOKIE[$password])) {
?>
<form method="post" action="">
<input type="password" name="password" value="">
<input type="submit" name="login" value="Go">
</form>
<?php
  die();
 }
?>
<form method="get" action="">
 <input type="number" name="list" value="1">
 <input type="submit" name="submit" value="go">
</form>
<?php
 if(!isset($_GET["list"])) $_GET["list"] = 1;
 $list = (int)$_GET["list"];
 $lfile = "lists/$list.json";
 if(!file_exists($lfile)) {
  file_put_contents($lfile, "[]");
 }
 $ldata = json_decode(file_get_contents($lfile));
?>
<table>
<thead>
<tr bgcolor="black">
 <td><font color="white">ID</font></td>
 <td><font color="white">URL</font></td>
 <td><font color="white">Hits</font></td>
 <td><font color="white">Weight</font></td>
</tr>
</thead>
<tbody>
<?php
 $lid = 0;
 foreach($ldata as $ld) {
  echo("<tr bgcolor=\"#ffffd0\">
   <td>$lid</td>
   <td>{$ld->url}</td>
   <td>{$ld->hits}</td>
   <td>{$ld->weight}</td>
  </tr>
  ");
  $lid++;
 }
?>
</tbody>
</table>
<br><br>
<form method="post" action="">
 <input type="number" name="id" placeholder="id">
 <input type="text" name="url" placeholder="url">
 <input type="number" name="hits" placeholder="hits">
 <input type="number" name="weight" placeholder="weight">
 <input type="submit" name="action" value="update">
 <input type="submit" name="action" value="delete">
 <input type="submit" name="action" value="add">
</form>
<p>Weight more or equal to 100 makes it always redirect</p>
<?php
 function save_ldata($ldata) {
  copy($GLOBALS["lfile"], $GLOBALS["lfile"].".bak");
  file_put_contents($GLOBALS["lfile"], json_encode($ldata, JSON_PRETTY_PRINT));
  die("<script>document.location.href=document.location.href;</script>");
 }

 function updatez() {
  $ldata = $GLOBALS["ldata"];
  if(!isset($_POST["id"])) return "<p>Missing id</p>";
  $id = (int)$_POST["id"];
  if(isset($_POST["url"]) && filter_var($_POST["url"], FILTER_VALIDATE_URL)) {
   $ldata[$id]->url = $_POST["url"];
  }
  if(isset($_POST["hits"]) && $_POST["hits"] != "")
  $ldata[$id]->hits = (int)$_POST["hits"];
  if(isset($_POST["weight"]) && $_POST["weight"] != "")
  $ldata[$id]->weight = (int)$_POST["weight"];
  save_ldata($ldata);
 }

 function deletez() {
  $ldata = $GLOBALS["ldata"];
  if(!isset($_POST["id"])) return "<p>Missing id</p>";
  $id = (int)$_POST["id"];
  array_splice($ldata, $id, 1);
  save_ldata($ldata);
 }

 function addz() {
  $ldata = $GLOBALS["ldata"];
  if(!isset($_POST["url"]) || !isset($_POST["hits"]) ||
   !isset($_POST["weight"])) {
   return "<p>Missing fields</p>";
  }
  if(!filter_var($_POST["url"], FILTER_VALIDATE_URL)) return "<p>Invalid url</p>";
  $hits = (int)$_POST["hits"];
  $weight = (int)$_POST["weight"];
  $newobj = new stdClass();
  $newobj->url = $_POST["url"];
  $newobj->hits = $hits;
  $newobj->weight = $weight;
  $ldata[] = $newobj;
  save_ldata($ldata);
 }
 if(isset($_POST["action"])) {
  switch($_POST["action"]) {
   case "update":
    echo updatez();
    break;
   case "delete":
    echo deletez();
    break;
   case "add":
    echo addz();
    break;
  }
 }
?>
