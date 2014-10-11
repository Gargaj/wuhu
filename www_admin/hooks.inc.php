<?
// todo: priority levels and/or possibility to cancel future actions?

$HOOKS = array();
function add_hook( $hookpoint, $func )
{
  global $HOOKS;
  $HOOKS[$hookpoint][] = $func;
}

function run_hook( $hookpoint, $args = null )
{
  global $HOOKS;

  if (!$HOOKS[$hookpoint]) return;
  
  foreach($HOOKS[$hookpoint] as $v) $v($args);
}

function add_activation_hook( $pluginPath, $func )
{
  if (preg_match("/plugins\/([a-zA-Z_\-0-9]+)[\/.]/",$pluginPath,$m))
    $pluginPath = $m[1];
  
  return add_hook( $pluginPath . "_activation", $func );
}

define(PLUGINREGISTRY,ADMIN_DIR . "/activeplugins.serialize");

function get_plugin_entry_path( $name )
{
  $entryfiles = array(
    ADMIN_DIR . "/plugins/" . $name . "/plugin.php",
    ADMIN_DIR . "/plugins/" . $name . "/" . $name . ".php",
    ADMIN_DIR . "/plugins/" . $name . ".php",
  );
  foreach ($entryfiles as $file)
    if (file_exists($file)) 
      return $file;
  return NULL;
}

function loadPlugins()
{  
  $data = @file_get_contents(PLUGINREGISTRY);
  $activePlugins = unserialize($data);
  if (!$activePlugins) $activePlugins = array();
  
  foreach($activePlugins as $dirname=>$data)
  {
    $path = get_plugin_entry_path( $dirname );
    if ($path && file_exists($path)) 
    {
      include_once($path);
    }
  }
}

?>