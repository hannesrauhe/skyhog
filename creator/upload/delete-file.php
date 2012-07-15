<?php
require_once("../base.inc.php");

header("Content-type", "text/json");
$filename = $_GET["filename"];
$id = intval($_GET["id"]);
$filename = UPLOAD_DIR.$filename;
$rc = unlink($filename);
echo json_encode(
  array(
    "filename" => $_GET["filename"],
    "id" => $id,
    "status" => ($rc)? "ok" : "failed"
    )
  );