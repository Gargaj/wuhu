<?php
/*
Plugin name: Results export
Description: Allows for exporting of final results in JSON format
*/
if (!defined("ADMIN_DIR")) exit();

function resultsexport_preheader()
{
  if(!@$_GET["export"])
  {
    return;
  }

  $voter = null;
  $results = generate_results($voter, false, true);
  
  switch($_GET["export"])
  {
    case "json":
      {
        header("Content-type: application/json");
        die(json_encode($results, JSON_PRETTY_PRINT));
      }
      break;
    case "csv":
      {
        header("Content-type: text/csv");

        $f = fopen("php://output","wt");

        fputcsv($f,array(
          "Compo name",
          "Ranking",
          "Order",
          "Title",
          "Author",
          "Points",
        ));

        foreach($results["compos"] as $compo)
        {
          foreach($compo["results"] as $entry)
          {
            fputcsv($f,array(
              $compo["name"],
              $entry["ranking"],
              $entry["order"],
              $entry["title"],
              $entry["author"],
              $entry["points"],
            ));
          }
        }        
        fclose($f);
        exit();
      }
      break;
  }
}

add_hook("admin_results_preheader","resultsexport_preheader");

function resultsexport_preprint()
{
  printf("<h3>Export results</h3>");
  printf("<p><a href='?export=json'>as JSON</a></p>");
  printf("<p><a href='?export=csv'>as CSV</a></p>");
}

add_hook("admin_results_preprint","resultsexport_preprint");
?>
