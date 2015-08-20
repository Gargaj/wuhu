<?
include_once("header.inc.php");

if ($_POST["amount"]) {
  SQLLib::Query("truncate votekeys");
  $len = (int)$_POST["length"] ? (int)$_POST["length"] : 8;
  
  $abc = str_split("BCDFGHJKLMNPQRSTVWXYZ");
  for($x=0; $x<$_POST["amount"]; $x++) 
  {
    $str = "";
    for ($y=0; $y<$len; $y++)
      $str .= $abc[ array_rand($abc) ];
    
    $hash = strtoupper($_POST["prefix"].$str);
    SQLLib::InsertRow("votekeys",array("votekey"=>$hash));
  }
}
if ($_POST["mode"] && is_uploaded_file($_FILES["votekeyfile"]["tmp_name"])) 
{
  if ($_POST["mode"] == "reset")
  {
    SQLLib::Query("truncate votekeys");
  }
  
  $f = file( $_FILES["votekeyfile"]["tmp_name"] );
  foreach($f as $v)
  {
    $v = trim($v);
    $v = preg_replace("/\s/","",$v);
    //$v = preg_replace("/[^a-zA-Z0-9]/g","",$v);
    if ($v)
    {
      try{
        SQLLib::InsertRow("votekeys",array("votekey"=>$v));
      } catch(Exception $e) {}
    }
  }
  
}

printf("<h2>Votekeys</h2>");
printf("<a href='votekeys_print.php'>Print votekeys</a>");
printf("<table class='minuswiki' id='votekeys'>");
$n = 1;
$count = SQLLib::selectRow("select count(*) as c from votekeys where userid!=0")->c;
printf("<tr><td colspan='3'>%d votekeys used</td></tr>\n",$count);

$s = SQLLib::selectRows("select * from votekeys");
foreach($s as $t) {
  printf("<tr>");  
  printf("  <td>%d.</td>",$n++);  
  printf("  <td class='key'>%s</td>",$t->votekey);  
  printf("  <td>%s</td>",$t->userid?sprintf("<a href='users.php?id=%d'>used</a>",$t->userid):"");  
  printf("</tr>");  
}
printf("</table>");

?>
<h3>Generate votekeys</h3>
<form action="votekeys.php" method="post">
  <label>Amount:</label> <input type="text" name="amount" value="200"/>
  <label>Prefix:</label> <input type="text" name="prefix"/>
  <label>Length:</label> <input type="text" name="length"/>
  <input type="submit" value="Generate new!"/>
</form>
<h3>Load votekeys from text file</h3>
<form action="votekeys.php" method="post" enctype="multipart/form-data">
  <label>File:</label> <input type="file" name="votekeyfile"/>
  <label>Usage:</label> 
  <input type="radio" name="mode" value="reset" id='load1' /> <label for='load1'>Replace existing</label>
  <input type="radio" name="mode" value="merge" id='load2' checked='checked' /> <label for='load2'>Merge with existing</label>
  <input type="submit" value="Upload!"/>
</form>
<?
include_once("footer.inc.php");
?>