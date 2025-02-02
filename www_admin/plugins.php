<?php
include_once("bootstrap.inc.php");

$data = @file_get_contents(PLUGINREGISTRY);
$oldActivePlugins = unserialize($data);
if (!$oldActivePlugins) $oldActivePlugins = array();

$activePlugins = $oldActivePlugins;

$success = false;
if (@$_POST["submit"])
{
  $activePlugins = array();
  if(@$_POST["plugin"]) foreach($_POST["plugin"] as $dirname=>$on)
  {
    // this leaves room for future activity
    $activePlugins[$dirname] = array();
    $activePlugins[$dirname]["active"] = true;
    $activePlugins[$dirname]["directory"] = $dirname;

    //$entryfile = ADMIN_DIR . "/plugins/" . $dirname . "/plugin.php";
    $path = get_plugin_entry_path( $dirname );
    if ($path && file_exists($path))
    {
      include_once($path);
      run_hook( $dirname . "_activation" );
    }
  }
  $success = @file_put_contents(PLUGINREGISTRY,serialize($activePlugins)) !== false;
}

include_once("header.inc.php");

if (!is_writable(PLUGINREGISTRY))
{
  printf("<div class='error'>Please make sure %s is writable!</div>\n",_html(PLUGINREGISTRY));
}
if ($success)
{
  printf("<div class='success'>Plugins activated/deactivated</div>\n");
}

printf("<form action='%s' method='post'>\n",$_SERVER["REQUEST_URI"]);
printf("  <input type='submit' name='submit' value='Activate/Deactivate'>\n");
printf("<table id='pluginlist'>\n");

$files = array();
$files = array_merge( $files, glob(ADMIN_DIR . "/plugins/*.php") );
$files = array_merge( $files, glob(ADMIN_DIR . "/plugins/*/", GLOB_ONLYDIR) );
usort($files,"strcasecmp");
foreach($files as $v)
{
  if (!preg_match("/\/plugins\/(.*)[\/\.]/",$v,$m))
    continue;

  $name = $m[1];
  $path = get_plugin_entry_path( $name );
  if ($path)
  {
    $pluginDirName = $name;
    $pluginName = $name;
    $pluginDescription = "";

    $f = fopen($path,"rt");
    $data = fread($f,1024);
    fclose($f);

    if(preg_match("/^Plugin name: (.*)$/im",$data,$m))
      $pluginName = $m[1];
    if(preg_match("/^Description: (.*)$/im",$data,$m))
      $pluginDescription = $m[1];

    printf("<tr>\n");
    printf("  <td class='checkbox'><input type='checkbox' name='plugin[%s]'%s></td>\n",_html($pluginDirName),@$activePlugins[$pluginDirName] ? " checked='checked'" : "");
    printf("  <td onclick=\"this.parentNode.querySelector('input').checked = !this.parentNode.querySelector('input').checked\" class='name' style='cursor:pointer'>%s</td>\n",_html($pluginName));
    printf("  <td class='description'>%s</td>\n",_html($pluginDescription));
    printf("</tr>\n");
  }
}
printf("</table>\n");
printf("  <input type='submit' name='submit' value='Activate/Deactivate'>\n");
printf("</form>\n");

include_once("footer.inc.php");
?>
