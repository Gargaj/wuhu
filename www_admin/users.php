<?php
include_once("header.inc.php");

run_hook("admin_edituser_start");

if ($_POST["id"] && $_POST["action"]=="Delete") {
  SQLLib::Query(sprintf_esc("delete from users where id = %d",(int)$_POST["id"]));
  SQLLib::Query(sprintf_esc("delete from votes_preferential where userid = %d",(int)$_POST["id"]));
  SQLLib::Query(sprintf_esc("delete from compoentries where userid = %d",(int)$_POST["id"]));
  SQLLib::Query(sprintf_esc("delete from votekeys where userid = %d",(int)$_POST["id"]));
}

if ($_POST["id"] && $_POST["action"]=="Set new password") {
  $a = array();
  $a["password"] = hashPassword($_POST["newpassword"]);
  SQLLib::UpdateRow("users",$a,"id = ".(int)$_POST["id"]);
  printf("<div class='success'>New password set.</div>\n");
}
if ($_GET["id"]) {
  $user = SQLLib::selectRow(sprintf_esc("select * from users where id = %d",(int)$_GET["id"]));
  printf("<h2>Users - ".htmlspecialchars($user->username)."</h2>");

  $votekey = SQLLib::selectRow(sprintf_esc("select * from votekeys where userid = %d",(int)$_GET["id"]));

  printf("<ul>");
  printf("  <li><b>Nick:</b> %s</li>\n",htmlspecialchars($user->nickname));
  printf("  <li><b>Group:</b> %s</li>\n",htmlspecialchars($user->group));
  printf("  <li><b>Public:</b> %s</li>\n",$user->visible?"yes":"no");
  printf("  <li><b>IP:</b> %s</li>\n",$user->regip);
  printf("  <li><b>Registration time:</b> %s</li>\n",$user->regtime);
  if ($votekey)
    printf("  <li><b>Associated votekey:</b> %s</li>\n",$votekey->votekey);
  printf("</ul>");

echo "<hr/>\n";

$entries = SQLLib::selectRows(sprintf_esc("select *,compoentries.id as id from compoentries join compos on compos.id=compoentries.compoid where userid = %d",$_GET["id"]));
?>
<table class='minuswiki'>
<tr>
  <th>Order</th>
  <th>#</th>
  <th>Title</th>
  <th>Author</th>
  <th>Compo</th>
  <th>File name</th>
  <th>File size</th>
</tr>
<?php
$n = 1;
foreach($entries as $t) {
  printf("<tr>\n");
  printf("  <td>%d.</td>\n",$t->playingorder);
  printf("  <td>#%d</td>\n",$t->id);
  printf("  <td><a href='compos_entry_edit.php?id=%d'>%s</a></td>\n",$t->id,$t->title);
  printf("  <td>%s</td>\n",$t->author);
  printf("  <td><a href='compos_entry_list.php?id=%d'>%s</a></td>\n",$t->compoid,$t->name);
  printf("  <td>%s</td>\n",$t->filename);
  printf("  <td>%d bytes</td>\n",filesize(get_compoentry_file_path($t)));
  printf("</tr>\n");
  $n++;
}
//printf("<tr><td colspan='9'><a href='compos.php?new=add'>add new compo</a></td></tr>\n");
echo "</table>\n";

run_hook("admin_edituser_beforeactions",array("user"=>$user));

?>
<hr/>
<form action="users.php" method="post">
  <input type="hidden" name="id" value="<?=$_GET["id"]?>"/>
  <input type="submit" name="action" value="Delete"/>
</form>
<hr/>
<form action="users.php" method="post">
  <input type="hidden" name="id" value="<?=$_GET["id"]?>"/>
  <input type="password" name="newpassword" />
  <input type="submit" name="action" value="Set new password"/>
</form>
<?php

} else {
  printf("<h2>Users</h2>");
  printf("<table class='minuswiki'>");
  $n = 1;
  switch($settings["voting_type"])
  {
    case "range":
      {
        $sq = "select count(*) from votes_range where votes_range.userid = u.id";
      } break;
    default:
      {
        $sq = "select count(*) from votes_preferential where votes_preferential.userid = u.id";
      } break;
  }

  $s = SQLLib::selectRows("select *, ".
     " (".$sq.") as votes, ".
     " (select count(*) from compoentries where compoentries.userid = u.id) as entries ".
     " from users as u order by regtime");
  foreach($s as $t) {
    printf("<tr>");
    printf("  <td>%d.</td>",$n++);
    printf("  <td>#%d</td>",$t->id);
    printf("  <td><a href='users.php?id=%d'>%s</a></td>",$t->id,htmlspecialchars($t->username));
    printf("  <td>%s</td>",htmlspecialchars($t->nickname));
    printf("  <td>%s</td>",htmlspecialchars($t->group));
  //  printf("  <td>%s</td>",$t->regip);
    printf("  <td>%s</td>",htmlspecialchars($t->regtime));
    printf("  <td>%d votes</td>",htmlspecialchars($t->votes));
    printf("  <td>%d entries</td>",htmlspecialchars($t->entries));
    printf("</tr>");
  }
  printf("</table>");
}

include_once("footer.inc.php");
?>
