<?php
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

$CRONS = array();
function add_cron( $key, $func, $frequency_in_seconds = 15 * 60 )
{
  global $CRONS;
  $CRONS[$key] = array(
    "func" => $func,
    "frequency" => $frequency_in_seconds,
  );
}
function run_cron()
{
  global $CRONS;
  $_logs = SQLLib::SelectRows("select cronName, lastRun from cron");
  foreach($_logs as $l) $logs[$l->cronName] = $l;
  foreach($CRONS as $key=>$cron)
  {
    if (!$logs[$key] || time() - strtotime($logs[$key]->lastRun) > $cron["frequency"])
    {
      $output = $cron["func"]() ?: "";
      SQLLib::InsertRow("cron",array(
        "cronName"=>$key,
        "lastRun"=>date("Y-m-d H:i:s"),
        "lastOutput"=>$output,
      ), array(
        "lastRun"=>date("Y-m-d H:i:s"),
        "lastOutput"=>$output,
      ));
      return; // spread load
    }
  }
}
function has_cron()
{
  global $CRONS;
  return count($CRONS) > 0;
}
function get_cron_log( $cronName )
{
  return SQLLib::SelectRow(sprintf_esc("select * from cron where cronname='%s'",$cronName));
}

define( PLUGINREGISTRY, ADMIN_DIR . "/activeplugins.serialize" );

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
