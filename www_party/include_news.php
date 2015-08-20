<?
if (!defined("ADMIN_DIR")) exit();
?>
<dl id='news'>
<?
$news = SQLLib::selectRows("select * from intranet_news order by `date` desc");
foreach($news as $n) {
?>
<dt><?=date("Y-m-d",strtotime($n->date))?> - <?=$n->eng_title?></dt>
<dd><?=$n->eng_body?></dd>
<?
}
?>
</dl>