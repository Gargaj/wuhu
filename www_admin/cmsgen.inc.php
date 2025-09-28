<?php
class CMSGen
{
  public $formdata;
  function ProcessPost()
  {
    if (!$_POST)
    {
      return;
    }

    if (@$_POST["__cms_canceldelete"])
    {
      return;
    }
    if (@$_POST["__cms_deleteconfirm"])
    {
      $sql = sprintf_esc("DELETE FROM %s WHERE %s='%s'",$this->formdata["table"],$this->formdata["fields"][$this->formdata["key"]]["sqlfield"],$_POST["__cms_id"]);
      SQLLib::query($sql);
      return;
    }
    if (@$_POST["__cms_deletefromeditform"])
    {
      $sql = sprintf_esc("DELETE FROM %s WHERE %s='%s'",$this->formdata["table"],$this->formdata["fields"][$this->formdata["key"]]["sqlfield"],$_POST["__cms_id"]);
      SQLLib::query($sql);
      return;
    }
    $sqlarray = array();

    foreach($this->formdata["fields"] as $fieldName=>$field)
    {
      if ($this->formdata["key"] == $fieldName && !isset($_POST[$fieldName])) continue;
      if (@$field["dontinsert"]) continue;
      $sqlname = $field["sqlfield"];
      switch(@$field["format"])
      {
        case "datetime":
          {
            $sqlarray[$sqlname] =
              sprintf("%04d-%02d-%02d %02d:%02d:%02d",
                $_POST[$fieldName."_y"],
                $_POST[$fieldName."_m"],
                $_POST[$fieldName."_d"],
                $_POST[$fieldName."_h"],
                $_POST[$fieldName."_i"],
                $_POST[$fieldName."_s"]);
          }
          break;
        case "datetime_easy":
          {
            $sqlarray[$sqlname] =
              sprintf("%s %s",
                $_POST[$fieldName."_date"],
                $_POST[$fieldName."_time"]);
          }
          break;
        case "date":
          {
            $sqlarray[$sqlname] =
              sprintf("%04d-%02d-%02d",
                $_POST[$fieldName."_y"],
                $_POST[$fieldName."_m"],
                $_POST[$fieldName."_d"]);
          }
          break;
        case "time":
          {
            $sqlarray[$sqlname] =
              sprintf("%02d:%02d:%02d",
                $_POST[$fieldName."_h"],
                $_POST[$fieldName."_i"],
                $_POST[$fieldName."_s"]);
          }
          break;
        case "checkbox":
          {
            $sqlarray[$sqlname] = ($_POST[$fieldName] == "on");
          }
          break;
        case "hex":
          {
            $sqlarray[$sqlname] = gmp_strval("0x0".$_POST[$fieldName],10);
          }
          break;
        case "bitfield":
          {
            $n = gmp_init(0);
            if (@$_POST[$fieldName])
            {
              foreach($_POST[$fieldName] as $enumKey=>$enumValue)
              {
                if ($enumValue=="on")
                {
                  gmp_setbit($n,$enumKey);
                }
              }
            }
            $sqlarray[$sqlname] = gmp_strval($n);
          }
          break;
        case "callback":
          {
            $c = $field["callback"];
            $sqlarray[$sqlname] = $c("result",$_POST[$fieldName],$fieldName,$field);
          }
          break;
        case "number":
          {
            $sqlarray[$sqlname] = (int)$_POST[$fieldName];
          }
          break;
        default:
          {
            $sqlarray[$sqlname] = $_POST[$fieldName];
          }
          break;
      }
    }

    if (isset($_POST["__cms_id"]) && !@$_POST["__cms_insert"])
    {
      $where = sprintf_esc("%s='%s'",$this->formdata["fields"][$this->formdata["key"]]["sqlfield"],$_POST["__cms_id"]);
      SQLLib::UpdateRow($this->formdata["table"],$sqlarray,$where);
    }
    else
    {
      SQLLib::InsertRow($this->formdata["table"],$sqlarray);
    }
  }

  function RenderEditForm($id,$insert = false)
  {
    $row = null;
    if (!$insert)
    {
      $sql = sprintf_esc("select * from %s where %s='%s'",$this->formdata["table"],$this->formdata["fields"][$this->formdata["key"]]["sqlfield"],$id);
      $row = SQLLib::selectRow($sql);
    }
    $processingFile = ((@$this->formdata["stayonform"]&&!$insert)?basename($_SERVER["REQUEST_URI"]):$this->formdata["processingfile"]);
    echo "<form action='".$processingFile."' method='post'>\n";
    printf("<table class='%s edit'>\n",$this->formdata["class"]);
    foreach($this->formdata["fields"] as $fieldname=>$field)
    {
      $sqlname = $field["sqlfield"];
      $value = ($row && $row->$sqlname ? $row->$sqlname : @$field["default"]) ?: "";
      switch(@$field["format"])
      {
        case "text":
          {
            printf("<tr>\n");
            printf("  <td>%s:</td>\n",_html($field["caption"]));
            printf("  <td><input type='text' name='%s' value='%s'/></td>\n",_html($fieldname),_html($value));
            printf("</tr>\n");
          }
          break;
        case "number":
          {
            printf("<tr>\n");
            printf("  <td>%s:</td>\n",_html($field["caption"]));
            printf("  <td><input type='number' name='%s' value='%d'/></td>\n",_html($fieldname),_html($value));
            printf("</tr>\n");
          }
          break;
        case "hex":
          {
            printf("<tr>\n");
            printf("  <td>%s:</td>\n",_html($field["caption"]));
            printf("  <td>0x<input type='text' name='%s' value='%s'/></td>\n",
              _html($fieldname),
              str_pad(gmp_strval($value,16),$field["digits"],"0",STR_PAD_LEFT));
            printf("</tr>\n");
          }
          break;
        case "checkbox":
          {
            printf("<tr>\n");
            printf("  <td>%s:</td>\n",_html($field["caption"]));
            printf("  <td><input type='checkbox' name='%s'%s/></td>\n",
              _html($fieldname),
              $value?"checked='checked'":"");
            printf("</tr>\n");
          }
          break;
        case "textarea":
          {
            printf("<tr>\n");
            printf("  <td>%s:</td>\n",_html($field["caption"]));
            printf("  <td><textarea name='%s'>%s</textarea></td>\n",_html($fieldname),_html($value));
            printf("</tr>\n");
          }
          break;
        case "date":
          {
            $time = $value ? strtotime($value) : time();
            printf("<tr>\n");
            printf("  <td>%s:</td>\n",_html($field["caption"]));
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
          }
          break;
        case "time":
          {
            $time = $value ? strtotime($value) : time();
            printf("<tr>\n");
            printf("  <td>%s:</td>\n",$field["caption"]);
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
          }
          break;
        case "datetime_easy":
          {
            printf("<tr>\n");
            printf("  <td>%s:</td>\n",_html($field["caption"]));
            printf("  <td>\n");

            $time = $value ? strtotime($value) : time();
            printf("    <select name='%s_date'>\n",$fieldname);
            for ($x = 0; $x < ($field["days"] ?: 10); $x++)
            {
              $ftime = strtotime($field["firstday"]) + $x * 60 * 60 * 24;
              printf("<option value='%s'%s>Day %d - %s</option>\n",date("Y-m-d",$ftime),date("Y-m-d",$ftime)==date("Y-m-d",$time)?" selected='selected'":"",$x+1,date("M j, D",$ftime));
            }
            printf("    </select>\n");
            printf("<input class='easy_time' name='%s_time' type='text' value='%s' style='width:75px'/>",$fieldname,date("H:i:s",$time));

            printf("  </td>\n");
            printf("</tr>\n");
          }
          break;
        case "datetime":
          {
            $time = $value ? strtotime($value) : time();
            printf("<tr>\n");
            printf("  <td>%s:</td>\n",_html($field["caption"]));
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
          }
          break;
        case "select":
          {
            printf("<tr>\n");
            printf("  <td>%s:</td>\n",_html($field["caption"]));
            printf("  <td>\n");

            printf("    <select name='%s'>\n",$fieldname);
            foreach($field["fields"] as $selectKey=>$selectValue)
              printf("       <option value='%s'%s>%s</option>\n",$selectKey,$value==$selectKey?" selected='selected'":"",$selectValue);
            printf("    </select>\n");
            printf("  </td>\n");
            printf("</tr>\n");
          }
          break;
        case "bitfield":
          {
            printf("<tr>\n");
            printf("  <td>%s:</td>\n",_html($field["caption"]));
            printf("  <td><div class='columns2'><ul>\n");

            $gmpValue = gmp_init($value);
            foreach($field["fields"] as $enumKey=>$enumValue)
            {
              $mask = gmp_init(0);
              gmp_setbit($mask,$enumKey);
              printf("       <li><input name='%s[%d]' type='checkbox'%s>%s</li> \n",$fieldname,$enumKey,gmp_scan1(gmp_and($gmpValue,$mask),0)!=-1?" checked='checked'":"",$enumValue);
            }
            printf("  </ul></div>(value: %s)\n",$value);
            printf("  </td>\n");
            printf("</tr>\n");
          }
          break;
        case "callback":
          {
            printf("<tr>\n");
            printf("  <td>%s:</td>\n",_html($field["caption"]));
            printf("  <td>\n");
            $callback = $field["callback"];
            echo $callback("select",$value,$fieldName,$field);
            printf("  </td>\n");
            printf("</tr>\n");
          }
          break;
        case "static":
          {
            printf("<tr>\n");
            printf("  <td>%s:</td>\n",_html($field["caption"]));
            printf("  <td>\n");
            echo _html($value);
            printf("  <input type='hidden' name='%s' value='%s'/>\n", _html($fieldname),_html($value));
            printf("  </td>\n");
            printf("</tr>\n");
          }
          break;
        case "none":
        default:
          break;
      }
    }
    echo "<tr>\n";
    printf(" <td colspan='2'>\n");
    if (@$this->formdata["formid"])
    {
      printf("<input type='hidden' name='__cms_formid' value='%s' />\n",_html($this->formdata["formid"]));
    }
    if ($id!==NULL && !$insert)
    {
      printf("<input type='hidden' name='__cms_id' value='%s' />\n",_html($id));
      printf("<input type='submit' name='__cms_edit' value='%s' />\n",_html("Save changes"));
      printf("<input type='submit' name='__cms_insert' value='%s' />\n",_html("Save as new"));
      printf("<input type='submit' name='__cms_deletefromeditform' value='%s' />\n",_html("Delete"));
    }
    else
    {
      printf("<input type='submit' name='__cms_insert' value='%s' />\n",_html("Save as new"));
    }
    printf("</td>\n");
    echo "</tr>\n";
    echo "</table>\n";
    echo "</form>\n";
  }

  function RenderInsertForm()
  {
    $this->RenderEditForm(NULL,true);
  }

  function RenderDeleteForm($id)
  {
    echo "<form action='".$this->formdata["processingfile"]."' method='post'>\n";
    echo "Are you sure you want to delete the record ".$id."?\n";
    printf("<input type='hidden' name='__cms_id' value='%s' />\n",$id);
    printf("<input type='hidden' name='__cms_formid' value='%s' />\n",_html(@$this->formdata["formid"]));
    printf("<input type='submit' name='__cms_deleteconfirm' value='Yes' />\n");
    printf("<input type='submit' name='__cms_canceldelete' value='No' />\n");
    echo "</form>\n";
  }

  function RenderListGrid()
  {
    $sql = sprintf_esc("select * from %s ",$this->formdata["table"]);
    if (@$this->formdata["where"])  $sql .= sprintf_esc(" where (%s),",$this->formdata["where"]);
    if (@$this->formdata["order"])  $sql .= sprintf_esc(" order by %s",$this->formdata["order"]);
    if (@$this->formdata["limit"])  $sql .= sprintf_esc(" limit %s",$this->formdata["limit"]);
    if (@$this->formdata["offset"]) $sql .= sprintf_esc(" offset %s",$this->formdata["offset"]);
    $rows = SQLLib::selectRows($sql);

    printf("<table class='%s'>\n",$this->formdata["class"]);
    printf("<tr>\n");
    $processingFile = $this->formdata["processingfile"].(strstr($this->formdata["processingfile"],"?")===FALSE?"?":"&amp;");
    foreach($this->formdata["fields"] as $fieldName=>$field)
    {
      if (@$field["grid"])
      {
        if (@$this->formdata["sortable"])
        {
          if ($k == $this->formdata["order"])
          {
            printf("<th><a href='%ssort=%s%%20desc'>%s</a> &dArr;</th>\n",$processingFile,$fieldName,$field["caption"]);
          }
          else if ($k." desc" == $this->formdata["order"])
          {
            printf("<th><a href='%ssort=%s'>%s</a> &uArr;</th>\n",$processingFile,$fieldName,$field["caption"]);
          }
          else
          {
            printf("<th><a href='%ssort=%s'>%s</a></th>\n",$processingFile,$fieldName,$field["caption"]);
          }
        }
        else
        {
          printf("<th>%s</th>\n",$field["caption"]);
        }
      }
    }
    printf("<th colspan='2'>Operations</th>\n");
    printf("</tr>\n");
    $columnCount = 0;
    foreach($this->formdata["fields"] as $v)
    {
      if (@$v["grid"]) $columnCount++;
    }

    printf("<tr>\n");
    echo "<tr><td colspan='".($columnCount+2)."'><a href='".$processingFile."new=add'>Add new item</a></td></tr>\n";
    printf("</tr>\n");
    foreach($rows as $row)
    {
      echo "<tr>\n";
      foreach($this->formdata["fields"] as $fieldName=>$field)
      {
        if (!@$field["grid"]) continue;
        $sqlField = $field["sqlfield"];
        $value = $row->$sqlField ?: "";
        switch ($field["format"])
        {
          case "select":
            {
              printf("<td>%s</td>\n",_html($field["fields"][$value]));
            }
            break;
          case "hex":
            {
              printf("<td>0x%s</td>\n",str_pad(gmp_strval($value,16),$field["digits"],"0",STR_PAD_LEFT));
            }
            break;
          case "callback":
            {
              $c = $field["callback"];
              printf("<td>%s</td>\n",$c("display",$value,$fieldName,$field));
            }
            break;
          default:
            {
              printf("<td>%s</td>\n",_html($value));
            }
            break;
        }
        $columnCount++;
      }
      $key = $this->formdata["fields"][$this->formdata["key"]]["sqlfield"];
      printf("<td><a href='%sedit=%s'>edit</a></td>\n",$processingFile,$row->$key);
      printf("<td><a href='%sdel=%s'>del</a></td>\n"  ,$processingFile,$row->$key);
      echo "</tr>\n";
    }
    echo "<tr>\n";
    echo "  <td colspan='".($columnCount+2)."'><a href='".$processingFile."new=add'>Add new item</a></td>\n";
    echo "</tr>\n";
    if (@$this->formdata["limit"])
    {
      $rowCounter = SQLLib::selectRow(sprintf_esc("select count(*) as c from %s",$this->formdata["table"]));
      $numrow = $rowCounter->c;

      echo "<tr>\n";
      echo "  <td colspan='".($columnCount+2)."'>";

      $a = array();
      for($x = 0; $x < $numrow / $this->formdata["limit"]; $x++)
      {
        $a[] = sprintf("<a href='%s%s=%d'>%d</a>",$processingFile,$this->formdata["startgetkey"],$x,$x+1);
      }
      echo implode(" | \n",$a);

      echo "</td>\n";
      echo "</tr>\n";
    }
    echo "</table>\n";
  }
  function Render()
  {
    if (@$_GET["new"])
    {
      $this->RenderInsertForm();
    }
    else if (@$_GET["edit"])
    {
      $this->RenderEditForm($_GET["edit"]);
    }
    else if (@$_GET["del"])
    {
      $this->RenderDeleteForm($_GET["del"]);
    }
    else
    {
      $this->RenderListGrid();
    }
  }
}
?>
