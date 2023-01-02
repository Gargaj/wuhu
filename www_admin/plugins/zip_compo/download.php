<?php
include_once("../../bootstrap.inc.php");

// utils
function zip_dir($dir, $zippath)
{
  $zipArchive = new ZipArchive();
  $zipArchive->open($zippath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

  zip_subdir($zipArchive, $dir);

  $zipArchive->close();
}

function zip_subdir($zipArchive, $dir, $parentdir = '')
{
  $dirHandle = opendir($dir);

  while (($entry = readdir($dirHandle)) !== false) {
    if ($entry != '.' && $entry != '..') {
      $localpath = $parentdir . $entry;
      $fullpath = $dir . '/' . $entry;

      if (is_file($fullpath)) {
        $zipArchive->addFile($fullpath, $localpath);
      } else if (is_dir($fullpath)) {
        $zipArchive->addEmptyDir($localpath);
        zip_subdir($zipArchive, $fullpath, $localpath . '/');
      }
    }
  }

  closedir($dirHandle);
}

// main
if (!class_exists("ZipArchive")) {
  printf("<div class='error'>You need php-zip to export the compo archive</div>");
  exit();
}

$compoid = $_GET["id"];
$compo = SQLLib::selectRow(sprintf_esc("select * from compos where id=%d", $compoid));
$dirname = get_compo_dir($compo);

if (is_dir($dirname) === false) {
  printf("Failed to read %s", $dirname);
  exit();
}

$zippath = $settings["private_ftp_dir"] . "/" . $compo->dirname . ".zip";
zip_dir($dirname, $zippath);

if (($data = @file_get_contents($zippath)) === false) {
  printf("Failed to read %s", $zippath);
  exit();
}

header("Content-type: application/octet-stream");
header("Content-disposition: attachment; filename=\"".basename($compo->dirname).".zip\"");
header("Content-length: ".filesize($zippath));
echo $data;
?>
