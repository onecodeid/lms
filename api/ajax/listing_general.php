<?php

include_once('../class/database.php');
$db = new database();
$conn = $db->connect();

include_once('../class/data.php');
$list = new data($conn);
$r = $list->listing_general();

if ($r)
  echo json_encode(array('status'=>'OK', 'data'=>$r));
?>