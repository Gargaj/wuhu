<?
include_once("sqllib.inc.php");

function cmsProcessPost($formdata) {
  $mypost = array();
  
  $qcb = get_magic_quotes_gpc() ? "stripslashes" : "nop";

  $mypost = $_POST;

  if ($mypost["__cms_canceldelete"]) {
    return;
  }
  if ($mypost["__cms_deleteconfirm"]) {
    $sql = sprintf_esc("delete from %s where %s='%s'",$formdata["table"],$formdata["fields"][$formdata["key"]]["sqlfield"],$mypost["__cms_id"]);
    SQLLib::query($sql);
    return;
  }  
  if ($mypost["__cms_deletefromeditform"]) {
    $sql = sprintf_esc("delete from %s where %s='%s'",$formdata["table"],$formdata["fields"][$formdata["key"]]["sqlfield"],$mypost["__cms_id"]);
    SQLLib::query($sql);
    return;
  }
  $sqlarray = array();
  
  foreach($formdata["fields"] as $k=>$v) {
    if ($formdata["key"] == $k && !isset($mypost[$k])) continue;
    if ($v["dontinsert"]) continue;
    $sqlname = $v["sqlfield"];
    switch($v["format"]) {
      case "datetime": {
        $sqlarray[$sqlname] =
          sprintf("%04d-%02d-%02d %02d:%02d:%02d",
            $mypost[$k."_y"],
            $mypost[$k."_m"],
            $mypost[$k."_d"],
            $mypost[$k."_h"],
            $mypost[$k."_i"],
            $mypost[$k."_s"]);
      } break; 
      case "date": {
        $sqlarray[$sqlname] =
          sprintf("%04d-%02d-%02d",
            $mypost[$k."_y"],
            $mypost[$k."_m"],
            $mypost[$k."_d"]);
      } break; 
      case "time": {
        $sqlarray[$sqlname] =
          sprintf("%02d:%02d:%02d",
            $mypost[$k."_h"],
            $mypost[$k."_i"],
            $mypost[$k."_s"]);
      } break; 
      case "checkbox": {
        $sqlarray[$sqlname] = ($mypost[$k] == "on");
      } break; 
      case "hex": {
        $sqlarray[$sqlname] = gmp_strval("0x0".$mypost[$k],10);
      } break; 
      case "bitfield": {
        $n = gmp_init(0);
        if ($mypost[$k])
          foreach($mypost[$k] as $k2=>$v2)
            if ($v2=="on")
              gmp_setbit($n,($k2));
        $sqlarray[$sqlname] = gmp_strval($n);
      } break;

      case "callback": {
        $c = $v["callback"];
        $sqlarray[$sqlname] = $c("result",$mypost[$k],$k,$v);
      }

      default: {
//        if ($mypost[$k])
          $sqlarray[$sqlname] = $mypost[$k];
      } break; 
    }
  }
  
//  var_dump($sqlarray);
  if (isset($mypost["__cms_id"]) && !$mypost["__cms_insert"]) {
    $where = sprintf_esc("%s='%s'",$formdata["fields"][$formdata["key"]]["sqlfield"],$mypost["__cms_id"]);
    SQLLib::UpdateRow($formdata["table"],$sqlarray,$where);
  } else {
    SQLLib::InsertRow($formdata["table"],$sqlarray);
  }
}

function cmsRenderEditForm($formdata,$id,$insert = false) {
  if (!$insert) {
    $sql = sprintf_esc("select * from %s where %s='%s'",$formdata["table"],$formdata["fields"][$formdata["key"]]["sqlfield"],$id);
    $s = SQLLib::selectRow($sql);
  }
  echo "<form action='".(($formdata["stayonform"]&&!$insert)?basename($_SERVER["REQUEST_URI"]):$formdata["processingfile"])."' method='post'>\n"; 
  printf("<table class='%s edit'>\n",$formdata["class"]); 
  foreach($formdata["fields"] as $k=>$v) {
    $fieldname = $k;
    $sqlname = $v["sqlfield"];
    switch($v["format"]) {
      case "text": {
        printf("<tr>\n");
        printf("  <td>%s:</td>\n",htmlentities($v["caption"]));
        printf("  <td><input type='text' name='%s' value='%s'/></td>\n",
          htmlspecialchars($fieldname,ENT_QUOTES),
          htmlspecialchars($s->$sqlname?$s->$sqlname:$v["default"],ENT_QUOTES));
        printf("</tr>\n");
      } break;
      case "hex": {
        printf("<tr>\n");
        printf("  <td>%s:</td>\n",htmlentities($v["caption"]));//printf("<td>0x%0".$v["digits"]."X</td>\n",(int)($row->$sf));
        printf("  <td>0x<input type='text' name='%s' value='%s'/></td>\n",
          htmlspecialchars($fieldname,ENT_QUOTES),
          str_pad(gmp_strval(  ($s->$sqlname?$s->$sqlname:$v["default"]).""  ,16),$v["digits"],"0",STR_PAD_LEFT));
        printf("</tr>\n");
      } break;
      case "checkbox": {
        printf("<tr>\n");
        printf("  <td>%s:</td>\n",htmlentities($v["caption"]));
        printf("  <td><input type='checkbox' name='%s'%s/></td>\n",
          htmlspecialchars($fieldname,ENT_QUOTES),
          $s->$sqlname?"checked='checked'":"");
        printf("</tr>\n");
      } break;
      case "textarea": {
        printf("<tr>\n");
        printf("  <td>%s:</td>\n",htmlentities($v["caption"]));
        printf("  <td><textarea name='%s'>%s</textarea></td>\n",
          htmlspecialchars($fieldname,ENT_QUOTES),
          htmlspecialchars($s->$sqlname,ENT_QUOTES));
        printf("</tr>\n");
      } break;
      case "date": {
        $time = strtotime($s->$sqlname);
        if (!$s->$sqlname) $time = time();
        printf("<tr>\n");
        printf("  <td>%s:</td>\n",htmlentities($v["caption"]));
        printf("  <td>\n");

        printf("    <select name='%s_y'>\n",$fieldname);
        for ($x=1900; $x<=date("Y")+10; $x++)
          printf("       <option value='%d'%s>%s</option>\n",$x,$x==date("Y",$time)?" selected='selected'":"",$x);
        printf("    </select>\n");

        printf("    <select name='%s_m'>\n",$fieldname);
        for ($x=1; $x<=12; $x++)
          printf("       <option value='%d'%s>%s</option>\n",$x,$x==date("m",$time)?" selected='selected'":"",$x);
        printf("    </select>\n");

        printf("    <select name='%s_d'>\n",$fieldname);
        for ($x=1; $x<=31; $x++)
          printf("       <option value='%d'%s>%s</option>\n",$x,$x==date("d",$time)?" selected='selected'":"",$x);
        printf("    </select>&nbsp;&nbsp;\n");

        printf("  </td>\n");
        printf("</tr>\n");
      } break;
      case "time": {
        $time = strtotime($s->$sqlname);
        if (!$s->$sqlname) $time = time();
        printf("<tr>\n");
        printf("  <td>%s:</td>\n",$v["caption"]);
        printf("  <td>\n");

        printf("    <select name='%s_h'>\n",$fieldname);
        for ($x=0; $x<=23; $x++)
          printf("       <option value='%d'%s>%s</option>\n",$x,$x==date("H",$time)?" selected='selected'":"",$x);
        printf("    </select>:\n");

        printf("    <select name='%s_i'>\n",$fieldname);
        for ($x=0; $x<=59; $x++)
          printf("       <option value='%d'%s>%02d</option>\n",$x,$x==date("i",$time)?" selected='selected'":"",$x);
        printf("    </select>:\n");

        printf("    <select name='%s_s'>\n",$fieldname);
        for ($x=0; $x<=59; $x++)
          printf("       <option value='%d'%s>%02d</option>\n",$x,$x==date("s",$time)?" selected='selected'":"",$x);
        printf("    </select>\n");

        printf("  </td>\n");
        printf("</tr>\n");
      } break;
      case "datetime": {
        $time = strtotime($s->$sqlname);
        if (!$s->$sqlname) $time = time();
        printf("<tr>\n");
        printf("  <td>%s:</td>\n",htmlentities($v["caption"]));
        printf("  <td>\n");

        printf("    <select name='%s_y'>\n",$fieldname);
        for ($x=1900; $x<=date("Y")+10; $x++)
          printf("       <option value='%d'%s>%s</option>\n",$x,$x==date("Y",$time)?" selected='selected'":"",$x);
        printf("    </select>\n");

        printf("    <select name='%s_m'>\n",$fieldname);
        for ($x=1; $x<=12; $x++)
          printf("       <option value='%d'%s>%s</option>\n",$x,$x==date("m",$time)?" selected='selected'":"",$x);
        printf("    </select>\n");

        printf("    <select name='%s_d'>\n",$fieldname);
        for ($x=1; $x<=31; $x++)
          printf("       <option value='%d'%s>%s</option>\n",$x,$x==date("d",$time)?" selected='selected'":"",$x);
        printf("    </select>&nbsp;&nbsp;\n");

        printf("    <select name='%s_h'>\n",$fieldname);
        for ($x=0; $x<=23; $x++)
          printf("       <option value='%d'%s>%s</option>\n",$x,$x==date("H",$time)?" selected='selected'":"",$x);
        printf("    </select>:\n");

        printf("    <select name='%s_i'>\n",$fieldname);
        for ($x=0; $x<=59; $x++)
          printf("       <option value='%d'%s>%02d</option>\n",$x,$x==date("i",$time)?" selected='selected'":"",$x);
        printf("    </select>:\n");

        printf("    <select name='%s_s'>\n",$fieldname);
        for ($x=0; $x<=59; $x++)
          printf("       <option value='%d'%s>%02d</option>\n",$x,$x==date("s",$time)?" selected='selected'":"",$x);
        printf("    </select>\n");

        printf("  </td>\n");
        printf("</tr>\n");
      } break;
      case "select": {
        printf("<tr>\n");
        printf("  <td>%s:</td>\n",htmlentities($v["caption"]));
        printf("  <td>\n");

        printf("    <select name='%s'>\n",$fieldname);
        $valz = $s->$sqlname?$s->$sqlname:$v["default"];
        foreach($v["fields"] as $k=>$v2)
          printf("       <option value='%s'%s>%s</option>\n",$k,$valz==$k?" selected='selected'":"",$v2);
        printf("    </select>\n");
        printf("  </td>\n");
        printf("</tr>\n");
      } break;
      case "bitfield": {
        printf("<tr>\n");
        printf("  <td>%s:</td>\n",htmlentities($v["caption"]));
        printf("  <td><div class='columns2'><ul>\n");

        $value = gmp_init($s->$sqlname."");
        
        foreach($v["fields"] as $k=>$v2) {
          $ander = gmp_init(0);
          gmp_setbit($ander,($k));
          printf("       <li><input name='%s[%d]' type='checkbox'%s>%s</li> \n",$fieldname,$k,gmp_scan1(gmp_and($value,$ander),0)!=-1?" checked='checked'":"",$v2);
        }
        printf("  </ul></div>(value: %s)\n",$s->$sqlname);
        printf("  </td>\n");
        printf("</tr>\n");
      } break;
      case "callback": {
        printf("<tr>\n");
        printf("  <td>%s:</td>\n",htmlentities($v["caption"]));
        printf("  <td>\n");
        $c = $v["callback"];
        echo $c("select",$s->$sqlname ? $s->$sqlname : $v["default"],$k,$v);
        printf("  </td>\n");
        printf("</tr>\n");
      } break;
      case "static": {
        $z = $s->$sqlname ? $s->$sqlname : $v["default"];
        printf("<tr>\n");
        printf("  <td>%s:</td>\n",htmlentities($v["caption"]));
        printf("  <td>\n");
        echo htmlspecialchars($z);
        printf("  <input type='hidden' name='%s' value='%s'/>\n",
          htmlspecialchars($fieldname,ENT_QUOTES),
          htmlspecialchars($z,ENT_QUOTES));

        printf("  </td>\n");
        printf("</tr>\n");
      } break;
      case "none":
      default: {
      } break;
    }
  }
  echo "<tr>\n"; 
  printf(" <td colspan='2'>\n"); 
  if ($formdata["formid"])
    printf("<input type='hidden' name='__cms_formid' value='%s' />\n",htmlspecialchars($formdata["formid"],ENT_QUOTES)); 
  if ($id!==NULL && !$insert) {
    printf("<input type='hidden' name='__cms_id' value='%s' />\n",htmlspecialchars($id,ENT_QUOTES)); 
    printf("<input type='submit' name='__cms_edit' value='%s' />\n",htmlspecialchars("Save changes",ENT_QUOTES)); 
    printf("<input type='submit' name='__cms_insert' value='%s' />\n",htmlspecialchars("Save as new",ENT_QUOTES));
    printf("<input type='submit' name='__cms_deletefromeditform' value='%s' />\n",htmlspecialchars("Delete",ENT_QUOTES)); 
  } else {
    printf("<input type='submit' name='__cms_insert' value='%s' />\n",htmlspecialchars("Save as new",ENT_QUOTES)); 
  }
  printf("</td>\n"); 
  echo "</tr>\n"; 
  echo "</table>\n"; 
  echo "</form>\n"; 
}

function cmsRenderInsertForm($formdata) {
  cmsRenderEditForm($formdata,NULL,true);
}

function cmsRenderDeleteForm($formdata,$id) {
  echo "<form action='".$formdata["processingfile"]."' method='post'>\n";
  echo "Are you sure you want to delete the record ".$id."?\n";
  printf("<input type='hidden' name='__cms_id' value='%s' />\n",$id); 
  printf("<input type='hidden' name='__cms_formid' value='%s' />\n",htmlspecialchars($formdata["formid"],ENT_QUOTES)); 
  printf("<input type='submit' name='__cms_deleteconfirm' value='Yes' />\n"); 
  printf("<input type='submit' name='__cms_canceldelete' value='No' />\n"); 
  echo "</form>\n"; 
}

function cmsRenderListGrid($formdata) {
  $c = SQLLib::selectRow(sprintf_esc("select count(*) as c from %s",$formdata["table"]));
  $numrow = $c->c;
  
  $sql = sprintf_esc("select * from %s ",$formdata["table"]);
  if ($formdata["where"])  $sql .= sprintf_esc(" where (%s),",$formdata["where"]);
  if ($formdata["order"])  $sql .= sprintf_esc(" order by %s",$formdata["order"]);
  if ($formdata["limit"])  $sql .= sprintf_esc(" limit %s",$formdata["limit"]);
  if ($formdata["offset"]) $sql .= sprintf_esc(" offset %s",$formdata["offset"]);

  $s = SQLLib::selectRows($sql);
  printf("<table class='%s'>\n",$formdata["class"]); 
  printf("<tr>\n");
  foreach($formdata["fields"] as $k=>$v) 
    if ($v["grid"]) {
      if ($formdata["sortable"]) {
        if ($k == $formdata["order"]) {
          printf("<th><a href='%ssort=%s%%20desc'>%s</a> &dArr;</th>\n",$formdata["processingfile"].(strstr($formdata["processingfile"],"?")===FALSE?"?":"&amp;"),$k,$v["caption"]);
        } else if ($k." desc" == $formdata["order"]) {
          printf("<th><a href='%ssort=%s'>%s</a> &uArr;</th>\n",$formdata["processingfile"].(strstr($formdata["processingfile"],"?")===FALSE?"?":"&amp;"),$k,$v["caption"]);
        } else {
          printf("<th><a href='%ssort=%s'>%s</a></th>\n",$formdata["processingfile"].(strstr($formdata["processingfile"],"?")===FALSE?"?":"&amp;"),$k,$v["caption"]);
        } 
      } else
        printf("<th>%s</th>\n",$v["caption"]);
    }       
  printf("<th colspan='2'>Operations</th>\n");       
  printf("</tr>\n");
  $n = 0;
  foreach($formdata["fields"] as $v)
    if ($v["grid"]) $n++;
    
  printf("<tr>\n");
  echo "  <td colspan='".($n+2)."'><a href='".$formdata["processingfile"].(strstr($formdata["processingfile"],"?")===FALSE?"?":"&amp;")."new=add'>Add new item</a></td>\n"; 
  printf("</tr>\n");
  $key = $formdata["fields"][$formdata["key"]]["sqlfield"];
  foreach($s as $row) {
    echo "<tr>\n"; 
    foreach($formdata["fields"] as $k=>$v) {
      if (!$v["grid"]) continue;
      $sf = $v["sqlfield"];
      switch ($v["format"]) {
        case "select": {
          printf("<td>%s</td>\n",htmlspecialchars($v["fields"][$row->$sf]));
        } break;
        case "hex": {
          printf("<td>0x%s</td>\n",str_pad(gmp_strval($row->$sf,16),$v["digits"],"0",STR_PAD_LEFT));
        } break;
        case "callback": {
          $c = $v["callback"];
          printf("<td>%s</td>\n",$c("display",$row->$sf),$k,$v);
        } break;
        default: {
          printf("<td>%s</td>\n",htmlspecialchars($row->$sf));
        } break;
      }
      $n++;
    }
    //printf("<td><a href='%s?view=%s'>view</a></td>\n",$formdata["processingfile"],$row->$key);
    printf("<td><a href='%s%sedit=%s'>edit</a></td>\n",$formdata["processingfile"],strstr($formdata["processingfile"],"?")===FALSE?"?":"&amp;",$row->$key);
    printf("<td><a href='%s%sdel=%s'>del</a></td>\n"  ,$formdata["processingfile"],strstr($formdata["processingfile"],"?")===FALSE?"?":"&amp;",$row->$key);
    echo "</tr>\n"; 
  }
  echo "<tr>\n"; 
  echo "  <td colspan='".($n+2)."'><a href='".$formdata["processingfile"].(strstr($formdata["processingfile"],"?")===FALSE?"?":"&amp;")."new=add'>Add new item</a></td>\n"; 
  echo "</tr>\n"; 
  if ($formdata["limit"]) {
    echo "<tr>\n"; 
    echo "  <td colspan='".($n+2)."'>";
    
    $a = array();
    for($x=0;$x<$numrow/$formdata["limit"];$x++) {
      $a[] = sprintf("<a href='%s%s%s=%d'>%d</a>",
        $formdata["processingfile"],strstr($formdata["processingfile"],"?")===FALSE?"?":"&amp;",$formdata["startgetkey"],$x,$x+1);
    }
    echo implode(" | \n",$a);
    echo "</td>\n"; 
    echo "</tr>\n"; 
  }
  echo "</table>\n"; 
}
?>