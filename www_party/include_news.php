<?php
if (!defined("ADMIN_DIR")) exit();
?>
<dl id='news'>
<?php
$news = SQLLib::selectRows("select * from intranet_news order by `date` desc");
foreach($news as $n) {
?>
<dt><?=date("Y-m-d",strtotime($n->date))?> - <?=_html($n->eng_title)?></dt>
<dd><?=$n->eng_body?></dd>
<?php
}
?>
</dl>
