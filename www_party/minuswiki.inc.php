<?php
class MinusWiki
{
  public $PageTitle;
  public $TableName = "";
  public $CurrentLanguageCode;
  public $inDIV = 0;
  function __construct()
  {
    $get = clearArray($_GET);
    $this->PageTitle = @$get["page"]?:"";
    list($this->CurrentLanguageCode) = explode(":",$this->PageTitle);
  }

  function RetrievePage($pagename)
  {
    $row = SQLLib::SelectRow(sprintf_esc("select * from %s where title='%s' limit 1",$this->TableName,$pagename));
    return $row->content;
  }

  function InternalLinkCallback($matches)
  {
    $text = $matches[1];
    $link = $matches[1];
    if (strpos($text,"Image:")===0) {
      $text = substr($text,6);
      list($file,$text) = explode("|",$text,2);
      $a = explode("|",$text);
      $link = "images/" . $file;
      $style = "";
      $text = "";
      $width = -1;
      foreach($a as $v) {
        if ($v == "right") $style = "float: right; margin-left: 10px;";
        else if ($v == "left")  $style = "float: left; margin-right: 10px;";
        else if ($v == "center")  $style = "text-align: center;";
        else if (strstr($v,"px")!==FALSE) list($width) = sscanf($v,"%dpx");
        else if (strpos($v,"link=")===0) list(,$link) = explode("=",$v);
        else $text = $v;
      }
      //$thumb = str_replace(".","_th.",$link);
      if ($width > 0) {
        if (!file_exists("images/" . $width))
          mkdir("images/" . $width);
        $thumb = "images/" . $width . "/" . $file;
        if (!file_exists($thumb)) {
          thumbnail($link, $thumb, $width, $width);
        }
      } else
        $thumb = "images/" . $file;
      return sprintf("<div style='%s'><a href='%s' target='_blank'><img src='%s' alt='%s' title='%s'/></a></div>",$style,$link,$thumb,$text,$text);
    }
    if(strstr($text,"|")) {
      list($link,$text) = explode("|",$text);
    }
    if(strstr($link,":")===false) {
      //$link = $this->CurrentLanguageCode . ":" . ucfirst($link);
    }
    $link = str_replace(" ","_",$link);
//    $link = preg_replace("/(\&)[^(amp)]/","&amp;",$link);
    $link = rawurlencode($link);
    $link = str_replace("%3A",":",$link);
    $link = str_replace("%23","#",$link);

    return sprintf("<a href='/index.php?page=%s'>%s</a>",$link,$text);
  }

  var $linkNum = 1;
  function ExternalLinkCallback($matches)
  {
    $text = $matches[1];
    if(strstr($text," ")) {
      list($link,$text) = explode(" ",$text,2);
      $link = str_replace("&","&amp;",$link);
      return sprintf("<a href='%s' target='_blank' class='external'>%s</a>",$link,$text);
    } else
      return sprintf("<a href='%s' target='_blank' class='external'>[%d]</a>",$text,$this->linkNum++);
  }

  function IncludeCallback($matches)
  {
    $text = $matches[1];
    if(strstr($text,":")) {
      list($command,$arguments) = explode(":",$text);
      switch(strtolower($command))
      {
        case "include":
        {
          if (file_exists($arguments))
            return file_get_contents($arguments);
          else
            return "<b>Sorry!</b> No page titled '<i>".$arguments."</i>' found!";
        } break;
        case "eval":
        {
          if (file_exists($arguments))
          {
            ob_start();
            include($arguments);
            return ob_get_clean();
          }
          else
          {
            return "<b>Sorry!</b> No file titled '<i>".$arguments."</i>' found!";
          }
        } break;
      }
    }
    else
    {
      return $this->GetPage($text);
    }
  }
  var $inOList = 0;
  var $inUList = 0;
  var $inDList = 0;
  var $inParagraph = 0;

  function InsertClosingTags($exception="")
  {
    $output = "";
    if ($this->inUList && $exception!="ul")
    {
      $output .= "</ul>\n\n";
      $this->inUList = 0;
    }
    if ($this->inOList && $exception!="ol")
    {
      $output .= "</ol>\n\n";
      $this->inOList = 0;
    }
    if ($this->inDList && $exception!="dl")
    {
      $output .= "</dl>\n\n";
      $this->inDList = 0;
    }
    if ($this->inParagraph && $exception!="p")
    {
      $output .= "</p>\n";
      $this->inParagraph = 0;
    }
    if ($this->inDIV && $exception!="div")
    {
//      $output .= "</div>\n";
      $this->inDIV = 0;
    }
    return $output;
  }

  function ParsePage($text) {
    $lines = explode("\n",$text);
    $output = "";
    foreach($lines as $l) {
      $l = rtrim($l);

      /////////////////////////////////////////
      // non-paragraphic
      if (strpos($l,"=")===0)
      {
        $output .= $this->InsertClosingTags();
        $l = preg_replace("/====(.*)====/","<h4>$1</h4>",$l);
        $l = preg_replace("/===(.*)===/","<h3>$1</h3>",$l);
        $l = preg_replace("/==(.*)==/","<h2>$1</h2>",$l);
        $l = preg_replace("/=(.*)=/","<h1>$1</h1>",$l);
        preg_match("/>(.*)</",$l,$m);
        $l = "<a name='".str_replace(" ","_",$m[1])."'></a>\n".$l;
      }
      else if (strpos($l,"*")===0)
      {
        //$output.="#".$l."#";
        $output .= $this->InsertClosingTags("ul");
        if (!$this->inUList) {
          $output .= "<ul>\n";
          $this->inUList = 1;
        }
        $l = "<li>".substr($l,1)."</li>";
      }
      else if (strpos($l,"#")===0)
      {
        $output .= $this->InsertClosingTags("ol");
        if (!$this->inOList) {
          $output .= "<ol>\n";
          $this->inOList = 1;
        }
        $l = "<li>".substr($l,1)."</li>";
      }
      else if (strpos($l,";")===0 || strpos($l,":")===0) {
        $output .= $this->InsertClosingTags("dl");

        if (!$this->inDList) {
          $output .= "<dl>\n";
          $this->inDList = 1;
        }
        if (strpos($l,";")===0)
          $l = "<dt>".substr($l,1)."</dt>";
        if (strpos($l,":")===0)
          $l = "<dd>".substr($l,1)."</dd>";
      }
      else {
        if (strlen($l)===0 && $this->inParagraph)
        {
          $output .= "</p>\n";
          $this->inParagraph = 0;
        }
        else if (strpos($l,"<div")!==FALSE && strpos($l,"</div")===FALSE)
        {
          $this->inDIV = 1;
        }
        else if (strpos($l,"</div")!==FALSE)
        {
          $this->inDIV = 0;
        }
        else if (strlen($l)!==0 && !$this->inParagraph && strpos($l,"{{")!==0 && !$this->inDIV)
        {
          $output .= $this->InsertClosingTags();
          $output .= "<p>\n";
          $this->inParagraph = 1;
        }

      }

      /////////////////////////////////////////
      // paragraphic
      if (strstr($l,"[[")!==FALSE)
      {
        $l = preg_replace_callback("/\[\[(.*?)\]\]/",array(&$this,"InternalLinkCallback"),$l);
      }
      if (strstr($l,"[")!==FALSE)
      {
        $l = preg_replace_callback("/\[(.*?)\]/",array(&$this,"ExternalLinkCallback"),$l);
      }
      if (strstr($l,"{{")!==FALSE)
      {
        $l = preg_replace_callback("/\{\{(.*?)\}\}/",array(&$this,"IncludeCallback"),$l);
      }
      if (strstr($l,"''")!==FALSE)
      {
        $l = preg_replace("/'''(.*?)'''/","<b>$1</b>",$l);
        $l = preg_replace("/''(.*?)''/","<i>$1</i>",$l);
      }

      $output .= $l."\n";
    }
    if ($this->inParagraph)
    {
      $output .= "</p>\n";
      $this->inParagraph = 0;
    }
    $output .= $this->InsertClosingTags("");
    return $output;
  }

  function GetPage($pagename) {
    $pagename = str_replace("_"," ",$pagename);
    $data = $this->RetrievePage($pagename);
    if (!$data)
      return "<b>Sorry!</b> No page titled '<i>".htmlentities($pagename)."</i>' found!";

    $parseddata = $this->ParsePage($data);

    return $parseddata;
  }
};


?>
