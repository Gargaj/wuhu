<?php
include_once("bootstrap.inc.php");

run_hook("admin_results_preheader");

include_once("header.inc.php");

if(@$_POST["upload_to_sceneorg"] && @$_POST["partyname"] && function_exists("ftp_connect"))
{
  $_GET["encoding"] = "utf-8";
  $_GET["suppressHeader"] = true;

  $partyname = $_POST["partyname"];
  sanitize_filename($partyname);

  ob_start();
  include_once("results_text.php");
  $data = ob_get_clean();

  function upload_results($partyname,$data)
  {
    $temp = fopen('php://temp', 'r+');
    fwrite($temp, $data);
    rewind($temp);

    if (!($ftp = ftp_connect("ftp.scene.org"))) return "Unable to connect to scene.org";
    if (!ftp_login($ftp, "anonymous", "wuhu@upload")) return "Unable to login to scene .org";
    if (!ftp_pasv($ftp, true)) return "Unable to change to passive mode";
    if (!ftp_chdir($ftp,"/incoming/parties/".date("Y"))) return "Unable to change directory to /parties/";
    if (!ftp_mkdir($ftp,$partyname)) return "Unable to create new directory";
    if (!ftp_chdir($ftp,$partyname)) return "Unable to change to new directory";
    if (!ftp_fput($ftp, "results.txt", $temp, FTP_BINARY)) return "Unable to upload file";
    ftp_close($ftp);
    return true;
  }
  if (($error = upload_results($partyname,$data)) === true)
  {
    printf("<div class='success'>Results successfully uploaded to %s to scene.org!</div>\n",_html($partyname));
  }
  else
  {
    printf("<div class='error'>Failed to upload: %s</div>\n",_html($error));
  }
}

echo "<h2>Results file</h2>";

echo "<p>Text-only version: <a href='results_text.php'>view</a> / <a href='results_text.php?filename=results.txt'>download</a></p>";
echo "<p>Text-only version (UTF-8): <a href='results_text.php?encoding=utf-8'>view</a> / <a href='results_text.php?encoding=utf-8&amp;filename=results.txt'>download</a></p>";

if (function_exists("ftp_connect"))
{
  $partyname = get_setting("party_name");
  sanitize_filename($partyname);
  echo "<h3>Upload results file to scene.org</h3>";
  echo "<form method='post' onsubmit='return confirm(\"Are you sure this is the final results file you want to distribute?\")'>";
  echo "<label>Party directory name</label>";
  echo "<input name='partyname' required='yes' placeholder='partyname".date("y")."' value='"._html($partyname?str_replace("-","",$partyname):"")."'/>";
  echo "<input name='upload_to_sceneorg' type='submit' value='Start upload!'/>";
  echo "</form>";
}

run_hook("admin_results_preprint");

echo "<h2>Results</h2>";

$voter = null;
$results = generate_results($voter, false, false);
foreach($results["compos"] as $compo)
{
  printf("<h3><a href='compos_entry_list.php?id=%d'>%s</a></h3>\n",$compo["id"],_html($compo["name"]));

  if (!$compo["results"])
  {
    echo "<p>No qualified entries.</p>\n";
    continue;
  }
 
  $lastRank = -1;
  echo "<table class='results'>\n";
  foreach($compo["results"] as $entry)
  {
    printf("<tr>\n");
    if ($lastRank == (int)$entry["ranking"])
      printf("  <td>&nbsp;</td>\n");
    else
      printf("  <td>%d.</td>\n",$entry["ranking"]);
    printf("  <td>%d pts</td>\n",$entry["points"]);
    printf("  <td>#%d</td>\n",$entry["order"]);
    printf("  <td><a href='compos_entry_edit.php?id=%d'>%s</a> - %s</td>\n",$entry["id"],_html($entry["title"]),_html($entry["author"]));
    printf("</tr>\n");

    $lastRank = $entry["ranking"];
  }
  echo "</table>\n";
}

include_once("footer.inc.php");
?>
