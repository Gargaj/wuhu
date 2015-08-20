<?
include_once("sqllib.inc.php");

function load_settings()
{
  global $settings;
  $s = SQLLib::selectRows("select * from settings");
  $settings = array();
  foreach($s as $v)
    $settings[$v->setting] = $v->value;
}
load_settings();

function get_setting( $key )
{
  global $settings;
  if (!isset($settings[$key]))
    return null;
  return $settings[$key];
}

function update_setting( $key, $value )
{
  global $settings;
  $settings[$key] = $value;
  
  $a = array();
  $a["setting"] = $key;
  $a["value"] = $value;
  if (SQLLib::selectRow(sprintf_esc("select * from settings where setting='%s'",$key)))
  {
    $s = SQLLib::updateRow("settings",$a,sprintf_esc("setting='%s'",$key));
  }
  else
  {
    $s = SQLLib::insertRow("settings",$a);
  }
}
?>