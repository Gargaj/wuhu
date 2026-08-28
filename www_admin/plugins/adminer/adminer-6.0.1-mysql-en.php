<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.0.1
*/namespace
Adminer;const
VERSION="6.0.1";error_reporting(24575);set_error_handler(function($Hc,$Jc){return!!preg_match('~^Undefined (array key|offset|index)~',$Jc);},E_WARNING|E_NOTICE);$md=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($md||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$ok=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($ok)$$X=$ok;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($f=null){return($f?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Eb=adminer()->credentials();$J=Driver::connect($Eb[0],$Eb[1],$Eb[2]);return(is_object($J)?$J:null);}function
idf_unescape($t){if(!preg_match('~^[`\'"[]~',$t))return$t;$cf=substr($t,-1);return
str_replace($cf.$cf,$cf,substr($t,1,-1));}function
q($Q){return
connection()->quote($Q);}function
idx($ua,$w,$j=null){return($ua&&array_key_exists($w,$ua)?$ua[$w]:$j);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_searchable(array$l,array$X){if(!isset($l["privileges"]["where"]))return
false;$U=$l["type"];$_i=$X["val"];$Ka='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Ka~",$U))return
false;if(preg_match(number_type(),$U)){$tg='-?\d+(\.\d+)?';return(bool)preg_match('~^'.$tg.(preg_match('~IN$~',$X["op"])?"( *, *$tg)*":'').'$~',$_i);}if(preg_match('~^(small)?date|^timestamp~',$U))return(bool)preg_match('~^\d+-\d+-\d+~',$_i);if(preg_match('~^time~',$U))return(bool)preg_match('~^\d+:\d+~',$_i);if(preg_match('~^bool~',$U)||(JUSH=="mssql"&&$U=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$_i);return
true;}function
remove_slashes(array$Hk,$md=false){$J=array();foreach($Hk
as$w=>$X)$J[stripslashes($w)]=(is_array($X)?remove_slashes($X,$md):($md?$X:stripslashes($X)));return$J;}function
bracket_escape($t,$Da=false){static$Yj=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($t,($Da?array_flip($Yj):$Yj));}function
url_escape($Q){static$Yj=array();if(!$Yj){$Yj=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$Ua)$Yj[$Ua]=sprintf('%%%02X',ord($Ua));for($r=0;$r<256;$r++){if($r<32||$r>126)$Yj[chr($r)]=sprintf('%%%02X',$r);}}return
strtr((string)$Q,$Yj);}function
min_version($Kk,$wf="",$f=null){$f=connection($f);$Ii=$f->server_info;if($wf&&preg_match('~([\d.]+)-MariaDB~',$Ii,$A)){$Ii=$A[1];$Kk=$wf;}return$Kk&&version_compare($Ii,$Kk)>=0;}function
charset(Db$e){return(min_version("5.5.3",0,$e)?"utf8mb4":"utf8");}function
ini_set($Mg,$Y){return(function_exists('ini_set')?\ini_set($Mg,$Y):false);}function
ini_bool($_e){$X=ini_get($_e);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($_e){$X=ini_get($_e);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
max_input_vars($K,$Zg){$zf=(int)ini_get("max_input_vars");return($zf?(int)floor(($zf-$Zg)/$K):0);}function
max_input_vars_error(){$_e="max_input_vars";return
sprintf('Maximum number of allowed fields exceeded. Please increase %s.',"<b>$_e = ".ini_get($_e)."</b>");}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($Jk,$N,$V,$F){$_SESSION["pwds"][$Jk][$N][$V]=($_COOKIE["adminer_key"]&&is_string($F)?array(encrypt_string($F,$_COOKIE["adminer_key"])):$F);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$l=0,$tb=null){$tb=connection($tb);$I=$tb->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return($K?$K[$l]:false);}function
get_vals($H,$c=0){$J=array();$I=connection()->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$c];}return$J;}function
get_key_vals($H,$f=null,$Li=true){$f=connection($f);$J=array();$I=$f->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($Li)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$f=null,$k="<p class='error'>"){$tb=connection($f);$J=array();$I=$tb->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!$f&&$k&&(defined('Adminer\PAGE_HEADER')||$k=="-- "))echo$k.adminer()->error()."\n";return$J;}function
unique_array($K,array$v){foreach($v
as$u){if(preg_match("~^(PRIMARY|UNIQUE)$~",$u["type"])&&!$u["partial"]){$J=array();foreach($u["columns"]as$w){if(!isset($K[$w]))continue
2;$J[$w]=$K[$w];}return$J;}}}function
escape_key($w){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$w,$A))return$A[1].idf_escape(idf_unescape($A[2])).$A[3];return
idf_escape($w);}function
where(array$Z,array$m=array()){$J=array();foreach((array)$Z["where"]as$w=>$X){$w=bracket_escape($w,true);$c=escape_key($w);$l=idx($m,$w,array());$gd=$l["type"];$Le=$l&&(is_blob($l)||preg_match('~binary~',$gd));$J[]=$c.($Le&&!is_utf8($X)?" = ".driver()->quoteBinary($X):(JUSH=="sql"&&$gd=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$l["full_type"])?"::jsonb = ".q($X)."::jsonb":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($gd,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($l,q($X)))))));if(JUSH=="sql"&&preg_match('~char|text~',$gd)&&preg_match("~[^ -@]~",$X))$J[]="$c = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$w)$J[]=escape_key($w)." IS NULL";return
implode(" AND ",$J);}function
where_columns(array$m){$J=array();foreach((array)$_GET["null"]as$w)$J[$w]=true;foreach((array)$_GET["where"]as$w=>$X){$w=bracket_escape($w,true);foreach($m
as$C=>$l){if($w==$C||strpos($w,idf_escape($C))!==false)$J[$C]=true;}}return$J;}function
where_check($X,array$m=array()){parse_str($X,$Xa);remove_slashes(array(&$Xa));return
where($Xa,$m);}function
where_link($r,$c,$Y,$Jg="="){$Gg=($Y!==null?$Jg:"IS NULL");return"&where[$r][col]=".url_escape($c).($Gg!=first(adminer()->operators())?"&where[$r][op]=".url_escape($Gg):"")."&where[$r][val]=".url_escape($Y);}function
convert_fields(array$d,array$m,array$M=array()){$J="";foreach($d
as$w=>$X){if($M&&!in_array(idf_escape($w),$M))continue;$va=convert_field($m[$w]);if($va)$J
.=", $va AS ".idf_escape($w);}return$J;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($C,$Y,$mf=2592000){header("Set-Cookie: $C=".rawurlencode($Y).($mf?"; expires=".gmdate("D, d M Y H:i:s",time()+$mf)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($C=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
get_url($wk,$xb){$http_response_header=null;$Ic=array();set_error_handler(function($Hc,$k)use(&$Ic){$Ic[]=preg_replace('~^file_get_contents\([^)]*\):\s*~','',$k);return
true;});$J=file_get_contents($wk,false,$xb);restore_error_handler();$Yd=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($J,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($Yd,0,''),$A)?$A[1]:''),(array)$Yd,($J===false?implode("\n",$Ic):''),);}function
get_settings($_b){parse_str($_COOKIE[$_b],$Mi);return$Mi;}function
get_setting($w,$_b="adminer_settings",$j=null){return
idx(get_settings($_b),$w,$j);}function
save_settings(array$Mi,$_b="adminer_settings"){$Y=http_build_query($Mi+get_settings($_b));cookie($_b,$Y);$_COOKIE[$_b]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($sd=false){$zk=ini_bool("session.use_cookies");if(!$zk||$sd){session_write_close();if($zk&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($w){return$_SESSION[$w][DRIVER][SERVER][$_GET["username"]];}function
set_session($w,$X){$_SESSION[$w][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($Jk,$N,$V,$i=null){$vk=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($i!==null?"db|":"").($Jk=='mssql'||$Jk=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$vk,$A);return"$A[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($Jk!="server"||$N!=""?url_escape($Jk)."=".url_escape($N)."&":"")."username=".url_escape($V).($i!=""?"&db=".url_escape($i):"").($A[2]?"&$A[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($_,$B=null){if($B!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($_!==null?$_:$_SERVER["REQUEST_URI"]))][]=$B;}if($_!==null){if($_=="")$_=".";header("Location: $_");exit;}}function
query_redirect($H,$_,$B,$Zh=true,$Qc=true,$bd=false,$Lj=""){if($Qc){$dj=microtime(true);$bd=!connection()->query($H);$Lj=format_time($dj);}$Xi=($H?adminer()->messageQuery($H,$Lj,$bd):"");if($bd){adminer()->error
.=adminer()->error().$Xi.script("messagesPrint();")."<br>";return
false;}if($Zh)redirect($_,$B.$Xi);return
true;}class
Queries{static$queries=array();static$start=0;}function
queries($H){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$H:(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";");return
connection()->query($H);}function
apply_queries($H,array$T,$Kc='Adminer\table'){foreach($T
as$R){if(!queries("$H ".$Kc($R)))return
false;}return
true;}function
queries_redirect($_,$B,$Zh){$Uh=implode("\n",Queries::$queries);$Lj=format_time(Queries::$start);return
query_redirect($Uh,$_,$B,$Zh,false,!$Zh,$Lj);}function
format_time($dj){return
sprintf('%.3f s',max(0,microtime(true)-$dj));}function
relative_uri($vk=''){return
preg_replace_callback('~^[^?]*~',function($A){return
str_replace(":","%3A",$A[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($vk?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($eh=""){return
substr(preg_replace("~(?<=[?&])($eh".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($C,$Sb=false){$id=$_FILES[$C];if(!$id)return
null;foreach($id
as$w=>$X)$id[$w]=(array)$X;$J=array();foreach($id["error"]as$w=>$k){if($k)return$k;$n=$id["name"][$w];$Tj=$id["tmp_name"][$w];$vb=file_get_contents($Sb&&preg_match('~\.gz$~',$n)?"compress.zlib://$Tj":$Tj);if($Sb){$dj=substr($vb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$dj))$vb=iconv("utf-16","utf-8",$vb);elseif($dj=="\xEF\xBB\xBF")$vb=substr($vb,3);}$J[]=array($n,$vb);}return$J;}function
get_file($w,$Sb=false,$Yb=""){$ld=get_files($w,$Sb);if(!is_array($ld))return$ld;$J='';foreach($ld
as$id){$vb=$id[1];$J
.=$vb;if($Yb)$J
.=(preg_match("($Yb\\s*\$)",$vb)?"":$Yb)."\n\n";}return$J;}function
upload_error($k){$Gf=($k==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($k?'Unable to upload a file.'.($Gf?" ".sprintf('Maximum allowed file size is %sB.',$Gf):""):'File does not exist.');}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
format_number($X){return
strtr(number_format($X,0,".",','),preg_split('~~u','0123456789',-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$S,$w){$X=idx($S,$w,'?');if(!is_numeric($X))return
h($X);if($X<0)return'?';$ra=($w=="Rows"&&(JUSH=="sqlite"||$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));return($ra?"~ ":"").format_number($X);}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$cd=false){$J=table_status($R,$cd);return($J?reset($J):array("Name"=>$R));}function
column_foreign_keys($R){$J=array();foreach(adminer()->foreignKeys($R)as$o){foreach($o["source"]as$X)$J[$X][]=$o;}return$J;}function
fields_from_edit(){$J=array();foreach((array)$_POST["field_keys"]as$w=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$w];$_POST["fields"][$X]=$_POST["field_vals"][$w];}}foreach((array)$_POST["fields"]as$w=>$X){$C=bracket_escape($w,true);$J[$C]=array("field"=>$C,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($C==driver()->primary),);}return$J;}function
dump_headers($ke,$dg=false){$J=adminer()->dumpHeaders($ke,$dg);$bh=$_POST["output"];if($bh!="text"||$J=="tar"){$qb=($bh!="text"&&$bh!="file"&&preg_match('~^[0-9a-z]+$~',$bh)?".$bh":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($ke).".$J$qb");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv(array$K){$gk=$_POST["format"]=="tsv";foreach($K
as$w=>$X){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($gk?'\t':'[,;]|^$').'~',$X))$K[$w]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($gk?"\t":";")),$K)."\r\n";}function
parse_csv($Hb,$Hi){$J=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Hb,$xf);foreach($xf[0]as$K){preg_match_all("~((?>\"[^\"]*\")+|[^$Hi]*)$Hi~",$K.$Hi,$yf);$J[]=$yf[1];}return$J;}function
csv_value($X){return(preg_match('~^".*"$~s',$X)?str_replace('""','"',substr($X,1,-1)):$X);}function
apply_sql_function($q,$c){return($q?($q=="unixepoch"?"DATETIME($c, '$q')":($q=="count distinct"?"COUNT(DISTINCT ":strtoupper("$q("))."$c)"):$c);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($n){if(is_link($n))return;$p=@fopen($n,"c+");if(!$p)return;@chmod($n,0660);if(!flock($p,LOCK_EX)){fclose($p);return;}return$p;}function
file_write_unlock($p,$Lb){rewind($p);fwrite($p,$Lb);ftruncate($p,strlen($Lb));file_unlock($p);}function
file_unlock($p){flock($p,LOCK_UN);fclose($p);}function
first(array$ua){return
reset($ua);}function
password_file($g){$n=get_temp_dir()."/adminer.key";if(!$g&&!file_exists($n))return'';$p=file_open_lock($n);if(!$p)return'';$J=stream_get_contents($p);if(!$J){$J=rand_string();file_write_unlock($p,$J);}else
file_unlock($p);return$J;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($X,$z,array$l,$Jj){if(is_array($X)){$J="";if(array_filter($X,'is_array')==array_values($X)){$We=array();foreach($X
as$W)$We+=array_fill_keys(array_keys($W),null);foreach(array_keys($We)as$Ve)$J
.="<th>".h($Ve);foreach($X
as$W){$J
.="<tr>";foreach(array_merge($We,$W)as$Dk)$J
.="<td>".select_value($Dk,$z,$l,$Jj);}}else{foreach($X
as$Ve=>$W)$J
.="<tr>".($X!=array_values($X)?"<th>".h($Ve):"")."<td>".select_value($W,$z,$l,$Jj);}return"<table>$J</table>";}if(!$z)$z=adminer()->selectLink($X,$l);if($z===null){if(is_mail($X))$z="mailto:$X";if(is_url($X))$z=$X;}$X=driver()->value($X,$l);$J=adminer()->editVal($X,$l);if($J!==null){if(!is_utf8($J))$J="\0";elseif($Jj!=""&&is_shortable($l))$J=shorten_utf8($J,max(0,+$Jj));else$J=h($J);}return
adminer()->selectVal($J,$z,$l,$X);}function
is_blob(array$l){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$l["type"])&&!in_array($l["type"],idx(driver()->structuredTypes(),'User types',array()));}function
is_mail($zc){$xa='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$oc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$uh="$xa+(\\.$xa+)*@($oc?\\.)+$oc";return
is_string($zc)&&preg_match("(^$uh(,\\s*$uh)*\$)i",$zc);}function
is_url($Q){$oc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($oc?\\.)+$oc(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$Q);}function
is_shortable(array$l){return!preg_match('~'.number_type().'|date|time|year~',$l["type"]);}function
host_port($N){return(preg_match('~^(:([^:].*)|(\[(.+)\]|(([^:]+://)?[^:]+))(:(\d+))?)$~',$N,$A)?array($A[4].$A[5],$A[2].$A[8]):array($N,''));}function
count_rows($R,array$Z,$Me,array$Id){$H=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($Me&&(JUSH=="sql"||count($Id)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$Id).")$H":"SELECT COUNT(*)".($Me?" FROM (SELECT 1$H GROUP BY ".implode(", ",$Id).") x":$H));}function
slow_query($H){$i=adminer()->database();$Mj=adminer()->queryTimeout();$Qi=driver()->slowQuery($H,$Mj);$f=null;if(!$Qi&&support("kill")){$f=connect();if($f&&($i==""||$f->select_db($i))){$Xe=get_val(connection_id(),0,$f);echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$Xe&token=".get_token()."'); }, 1000 * $Mj);");}}ob_flush();flush();$J=@get_key_vals(($Qi?:$H),$f,false);if($f){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$Xh=rand(1,1e6);return($Xh^$_SESSION["token"]).":$Xh";}function
verify_token(){list($Uj,$Xh)=explode(":",$_POST["token"]);return($Xh^$_SESSION["token"])==$Uj&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($Q,$ec=""){$oa=array_flip(str_split(compress_alphabet()));$x=strlen($Q);$Fk=($x?13*($x-1)/2-$oa[$Q[0]]:0);$Ka="";$li=0;$mi=0;for($r=1;$r<$x;$r+=2){$li=($li<<13)+$oa[$Q[$r]]*93+$oa[$Q[$r+1]];$mi+=13;while($mi>=8&&$Fk>=8){$mi-=8;$Fk-=8;$Ka
.=chr($li>>$mi);$li&=(1<<$mi)-1;}}if($Ka=="")return"";if($ec!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$ec)),$Ka,ZLIB_FINISH);return($ec==""&&function_exists('gzinflate')?gzinflate($Ka):inflate($Ka,$ec));}function
inflate($Ka,$ec=""){$jf=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$kf=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$ic=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$kc=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$J=$ec;$G=0;do{$nd=inflate_bits($Ka,$G,1);$U=inflate_bits($Ka,$G,2);if(!$U){$G=($G+7)&~7;$x=inflate_bits($Ka,$G,16);$G+=16;$J
.=substr($Ka,$G>>3,$x);$G+=$x<<3;}else{if($U==1){$rf=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$lc=array_fill(0,30,5);}else{$qf=inflate_bits($Ka,$G,5)+257;$jc=inflate_bits($Ka,$G,5)+1;$D=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$Tf=array_fill(0,19,0);$Sf=inflate_bits($Ka,$G,4)+4;for($r=0;$r<$Sf;$r++)$Tf[$D[$r]]=inflate_bits($Ka,$G,3);$Uf=inflate_table($Tf);$lf=array();while(count($lf)<$qf+$jc){$oj=inflate_symbol($Ka,$G,$Uf);if($oj==16)$lf=array_merge($lf,array_fill(0,inflate_bits($Ka,$G,2)+3,end($lf)));elseif($oj==17)$lf=array_merge($lf,array_fill(0,inflate_bits($Ka,$G,3)+3,0));elseif($oj==18)$lf=array_merge($lf,array_fill(0,inflate_bits($Ka,$G,7)+11,0));else$lf[]=$oj;}$rf=array_slice($lf,0,$qf);$lc=array_slice($lf,$qf);}$sf=inflate_table($rf);$nc=inflate_table($lc);while(($oj=inflate_symbol($Ka,$G,$sf))!=256){if($oj<256)$J
.=chr($oj);else{$x=$jf[$oj-257]+inflate_bits($Ka,$G,$kf[$oj-257]);$mc=inflate_symbol($Ka,$G,$nc);$zg=strlen($J)-$ic[$mc]-inflate_bits($Ka,$G,$kc[$mc]);for($r=0;$r<$x;$r++)$J
.=$J[$zg+$r];}}}}while(!$nd);return($ec==""?$J:substr($J,strlen($ec)));}function
inflate_bits($Ka,&$G,$Bb){$J=0;for($r=0;$r<$Bb;$r++){$J+=((ord($Ka[$G>>3])>>($G&7))&1)<<$r;$G++;}return$J;}function
inflate_table(array$lf){$R=array();$fb=0;for($La=1;$La<=max($lf);$La++){foreach($lf
as$oj=>$x){if($x==$La){$R[$La][$fb]=$oj;$fb++;}}$fb<<=1;}return$R;}function
inflate_symbol($Ka,&$G,array$R){$fb=0;$La=0;do{$fb=($fb<<1)+inflate_bits($Ka,$G,1);$La++;}while(!isset($R[$La][$fb]));return$R[$La][$fb];}function
script($Ui,$Xj="\n"){return"<script".nonce().">$Ui</script>$Xj";}function
script_src($wk,$Vb=false){return"<script src='".h($wk)."'".nonce().($Vb?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($Lc,$Qd,$sa=null){$ta=array();foreach(array_slice(func_get_args(),2)as$X)$ta[]=json_encode($X,256);return" data-on$Lc='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$Qd(".implode(", ",$ta).")")."'";}function
input_hidden($C,$Y=""){return"<input type='hidden' name='".h($C)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$Q);}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($C,$Y,$Za,$Ze="",$b="",$eb="",$bf=""){$J="<input type='checkbox' name='$C' value='".h($Y)."'".($Za?" checked":"").($Ze==""&&$eb?" class='$eb'":"").($bf?" aria-labelledby='$bf'":"").$b.">";return($Ze!=""?"<label".($eb?" class='$eb'":"").">$J".h($Ze)."</label>":$J);}function
optionlist($Ng,$Ei=null,$_k=false){$J="";foreach($Ng
as$Ve=>$W){$Og=array($Ve=>$W);if(is_array($W)){$J
.='<optgroup label="'.h($Ve).'">';$Og=$W;}foreach($Og
as$w=>$X)$J
.='<option'.($_k||is_string($w)?' value="'.h($w).'"':'').($Ei!==null&&($_k||is_string($w)?(string)$w:$X)===$Ei?' selected':'').'>'.h($X);if(is_array($W))$J
.='</optgroup>';}return$J;}function
html_select($C,array$Ng,$Y="",$b="",$bf=""){static$Ze=0;$af="";if(!$bf&&substr($Ng[""],0,1)=="("){$Ze++;$bf="label-$Ze";$af="<option value='' id='$bf'>".h($Ng[""]);unset($Ng[""]);}return"<select name='".h($C)."'".($bf?" aria-labelledby='$bf'":"")."$b>".$af.optionlist($Ng,$Y)."</select>";}function
html_radios($C,array$Ng,$Y="",$Hi=""){$J="";foreach($Ng
as$w=>$X)$J
.="<label><input type='radio' name='".h($C)."' value='".h($w)."'".($w==$Y?" checked":"").">".h($X)."</label>$Hi";return$J;}function
confirm($B=""){return
on('click','confirmClick',$B?:'Are you sure?');}function
print_fieldset($s,$if,$Nk=false){echo"<fieldset><legend>","<a href='#fieldset-$s' class='toggle'>$if</a>","</legend>","<div id='fieldset-$s'".($Nk?"":" class='hidden'").">\n";}function
bold($Na,$eb=""){return($Na?" class='active $eb'":($eb?" class='$eb'":""));}function
js_escape($Q){return
str_replace("<","\\x3C",addcslashes($Q,"\r\n'\\"));}function
js_escape_re($Q){return
addcslashes(preg_quote($Q,"/"),"\r\n");}function
pagination_href($E){return
remove_from_uri("page|next").($E?"&page=$E".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($E,$Ib){return" ".($E==$Ib?($E?"<b>".($E+1)."</b>":$E+1):'<a href="'.h(pagination_href($E)).'">'.($E+1)."</a>");}function
hidden_fields(array$Qh,array$ne=array(),$Ih=''){$J=false;foreach($Qh
as$w=>$X){if(!in_array($w,$ne)){if(is_array($X))hidden_fields($X,array(),$w);else{$J=true;echo
input_hidden(($Ih?$Ih."[$w]":$w),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$uk){$uk=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($uk?on('submit','uploadProgress',ME."upload=$uk",SESSION_NAME."=$uk"):"");}function
file_input($b,$li=""){$Af="max_file_uploads";$Bf=ini_get($Af);$Gf="upload_max_filesize";$Hf=ini_bytes($Gf);$Fh=ini_bytes("post_max_size");if($Fh&&$Fh<$Hf){$Gf="post_max_size";$Hf=$Fh;}$If=ini_get($Gf);return(ini_bool("file_uploads")?"<input type='file'$b".on('change','fileChange',(int)$Bf,sprintf('Increase %s.',"$Af = $Bf"),$Hf,sprintf('Increase %s.',"$Gf = $If")).">$li":'File uploads are disabled.');}function
enum_input($U,$b,array$l,$Y,$Bc=""){preg_match_all("~'((?:[^']|'')*)'~",$l["length"],$xf);$Ih=($l["type"]=="enum"?"val-":"");$Za=(is_array($Y)?in_array("null",$Y):$Y===null);$J=($l["null"]&&$Ih?"<label><input type='$U'$b value='null'".($Za?" checked":"")."><i>$Bc</i></label>":"");foreach($xf[1]as$X){$X=stripcslashes(str_replace("''","'",$X));$Za=(is_array($Y)?in_array($Ih.$X,$Y):$Y===$X);$J
.=" <label><input type='$U'$b value='".h($Ih.$X)."'".($Za?' checked':'').'>'.h(adminer()->editVal($X,$l)).'</label>';}return$J;}function
input(array$l,$Y,$q,$Ba=false,$sk=false){$C=h(bracket_escape($l["field"]));echo"<td class='function'>";if(is_array($Y)&&!$q)$q="json";$Te=($q=="json"||preg_match('~^jsonb?$~',$l["full_type"]));if($Te&&$Y!=''&&(JUSH!="pgsql"||$l["type"]!="json")&&(is_array($Y)||!$_POST["save"]))$Y=json_encode(is_array($Y)?$Y:json_decode($Y),128|64|256);$ki=(JUSH=="mssql"&&$sk&&$l["auto_increment"]);if($ki&&!$_POST["save"])$q=null;$Cd=(isset($_GET["select"])||$ki?array("orig"=>'original'):array())+adminer()->editFunctions($l);$Gc=driver()->enumLength($l);if($Gc){$l["type"]="enum";$l["length"]=$Gc;}$b=" name='fields[$C]".($l["type"]=="enum"||$l["type"]=="set"?"[]":"")."'".($Ba?" autofocus":"");echo
driver()->unconvertFunction($l)." ";$R=$_GET["edit"]?:$_GET["select"];if($l["type"]=="enum")echo
h($Cd[""])."<td>".adminer()->editInput($R,$l,$b,$Y);else{$Sd=(in_array($q,$Cd)||isset($Cd[$q]));$od=0;foreach($Cd
as$w=>$X){if($w===""||!$X)break;$od++;}echo(count($Cd)>1?"<select name='function[$C]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($Cd,$q===null||$Sd?$q:"")."</select>":h(reset($Cd)))."<td".($od&&count($Cd)>1?on('input','skipOriginal',$od):"").">";$Be=adminer()->editInput($R,$l,$b,$Y);if($Be!="")echo$Be;elseif(preg_match('~bool~',$l["type"]))echo"<input type='hidden'$b value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked":"")."$b value='1'>";elseif($l["type"]=="set")echo
enum_input("checkbox",$b,$l,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($l)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$C'>";elseif($Te)echo"<textarea$b cols='50' rows='12' class='jush-json'>".h($Y).'</textarea>';elseif(($Ij=preg_match('~text|lob|memo~i',$l["type"]))||preg_match("~\n~",$Y)){if($Ij&&JUSH!="sqlite")$b
.=" cols='50' rows='12'";else{$L=min(12,substr_count($Y,"\n")+1);$b
.=" cols='30' rows='$L'";}echo"<textarea$b>".h($Y).'</textarea>';}else{$jk=driver()->types();$Jf=(!preg_match('~int~',$l["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$l["length"],$A)?((preg_match("~binary~",$l["type"])?2:1)*$A[1]+($A[3]?1:0)+($A[2]&&!$l["unsigned"]?1:0)):($jk[$l["type"]]?$jk[$l["type"]]+($l["unsigned"]?0:1):0));if(JUSH=='sql'&&min_version(5.6)&&preg_match('~time~',$l["type"]))$Jf+=7;echo"<input".((!$Sd||$q==="")&&preg_match('~^'.int_type().'$~',$l["type"])&&!preg_match('~\[]~',$l["full_type"])?" type='number'":"")." value='".h($Y)."'".($Jf?" data-maxlength='$Jf'":"").(preg_match('~char|binary~',$l["type"])&&$Jf>20?" size='".($Jf>99?60:40)."'":"")."$b>";}echo
adminer()->editHint($R,$l,$Y),(count($Cd)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$l){$t=bracket_escape($l["field"]);$q=idx($_POST["function"],$t);if($q=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$l["on_update"])?idf_escape($l["field"]):false);if($q=="NULL")return"NULL";if(is_blob($l)&&ini_bool("file_uploads")){$id=get_file("fields-$t");if(!is_string($id))return
false;return
driver()->quoteBinary($id);}$Y=idx($_POST["fields"],$t);if($Y===null)return
false;if($l["type"]=="enum"||driver()->enumLength($l)){$Y=idx($Y,0);if($Y=="orig"||!$Y)return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($l["auto_increment"]&&$Y=="")return
null;if($l["type"]=="set")$Y=implode(",",(array)$Y);if($q=="json"){$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}return
adminer()->processInput($l,$Y,$q);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Gi="<ul>\n";foreach(table_status('',true)as$R=>$S){$C=adminer()->tableName($S);if(isset($S["Engine"])&&$C!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array())),1));if(!$I||$I->fetch_row()){$Mh="<a href='".h(ME."select=".url_escape($R)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$C</a>";echo"$Gi<li>".($I?$Mh:"<p class='error'>$Mh: ".adminer()->error())."\n";$Gi="";}}}echo($Gi?"<p class='message'>".'No tables.':"</ul>")."\n";}function
on_help($Ij,$Oi=0){return
on('mouseover','helpMouseover',$Ij,$Oi).on('mouseout','helpMouseout');}function
on_help_value($gi="",$ji=""){return
on('mouseover','helpValueMouseover',$gi,$ji).on('mouseout','helpMouseout');}function
edit_form($R,array$m,$K,$sk,$k='',$H='',$Lj=''){$sj=adminer()->tableName(table_status1($R,true));page_header(($sk?'Edit':'Insert'),$k,array("select"=>array($R,$sj)),$sj);adminer()->editRowPrint($R,$m,$K,$sk,$H,$Lj);if($K===false){echo"<p class='error'>".'No rows.'."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$xc=false;$Tk=($sk&&!isset($_GET["select"])?where_columns($m):array());$yb=(count($Tk)!=count($m));if(!$yb)$Tk=array();if(!$m)echo"<p class='error'>".'You have no privileges to update this table.'."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Ba=!$_POST;foreach($m
as$C=>$l){echo"<tr".($Tk[$C]?on('change','whereChange'):"")."><th>".adminer()->fieldName($l);$j=idx($_GET["set"],bracket_escape($C));if($j===null){$j=$l["default"];if($l["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$j,$hi))$j=$hi[1];if(JUSH=="sql"&&preg_match('~binary~',$l["type"]))$j=bin2hex($j);}$Y=($K!==null?($K[$C]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$l["type"])&&is_array($K[$C])?implode(",",$K[$C]):(is_bool($K[$C])?+$K[$C]:$K[$C])):(!$sk&&$l["auto_increment"]?"":(isset($_GET["select"])?false:$j)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$l);if(($sk&&!isset($l["privileges"]["update"]))||$l["generated"])echo"<td class='function'><td>".select_value($Y,'',$l,null);else{$xc=true;$q=($_POST["save"]?idx($_POST["function"],bracket_escape($C),""):($sk&&preg_match('~^CURRENT_TIMESTAMP~i',$l["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$sk&&$Y==$l["default"]&&preg_match('~^[\w.]+\(~',$Y))$q="SQL";if(preg_match("~time~",$l["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$q="now";}if($l["type"]=="uuid"&&$Y=="uuid()"){$Y="";$q="uuid";}if($Ba!==false)$Ba=($l["auto_increment"]||$q=="now"||$q=="uuid"?null:true);input($l,$Y,$q,$Ba,$sk);if($Ba)$Ba=false;}}if(!fields($R)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($xc){echo"<input type='submit' value='".'Save'."'>\n";if(!isset($_GET["select"])&&$yb){$fc=($Tk&&($k!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($sk?'Save and continue editing':'Save and insert next')."' title='Ctrl+Shift+Enter'$fc".($sk?on('click','ajaxForm','Saving…'):"").">\n";}}echo($sk?"<input type='submit' name='delete' value='".'Delete'."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($uh,$x){return
str_repeat("$uh{0,65535}",$x/65535)."$uh{0,".($x%65535)."}";}function
shorten_utf8($Q,$x=80,$kj=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$x).")($)?)u",$Q,$A))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$x).")($)?)",$Q,$A);return
h($A[1]).$kj.(isset($A[2])?"":"<i>…</i>");}function
icon($je,$C,$ie,$Oj,$b=""){return"<button ".($C?"type='submit' name='$C'":"draggable='true' tabindex='-1'")." title='".h($Oj)."' class='icon icon-$je".($C?"":" jsonly")."'$b><span>$ie</span></button>";}function
copy_icon(){$Ab='Copy';return"<a href='' class='jsonly icon-copy' title='$Ab'><span>$Ab</span></a>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('*c0=@iDWB2P?H*{U)^:;B/4!N2Ch9&hJv;rrHHN,,V&KA"nRfwb9E:tfItOm[T$"DXBX~p!.VU_tTHo)6Y?9q/$mNiohTvI>+a<Y{uWk}`:y,3U4,>E(&Rg+L!L
o2PEgsnloQe<:k0oib.Mj<,:^!s
zL
u)CIc01D]MByKv5]ERUpyZdlppD_9oR;P9hVvq0j@^d:4.VmF0)NWe(|3H6L6o0Ws>bwAO`m9rG2Wt;hhRg!_Vf4_nB|@W/dk68Q<R1B`1Dm8:;z,2
U)U-,adrWa=3ExJ-QhN)_F%%ndS%Ly!><d61_"qU8+_TBHX@<(PwklcY!u8hbgk-
[;Rh`$j<M_/v1L?D1XOE!*3"aaCtLgr:&VCx-w"#!BYQEeE<?Ub!+aqVSz#me-7jAZ&6o[("Z/yYlM,,wx$LBFo4W,*sEWn"i_Dff8!3g>THyt3FDVLmGrq+phsrc9%K8ON1DXS`8$MRiPoh2TUidX#(7Q"{]y^3OK78@
M)W+3D81"Xj{qO"5CA8MS-J<&Xp_$*vN4|H#V:%`!.1
M+_Qf
`08<KQReU[2vX<g$^DbKIY*I/x
~,"#:J<<j(P[w8x_[QP`MX*51&AacRfmtF~1idOxM:15UQ]83D1aNR|ozOBHe8j34;O0RJ4YKp
E}#/y/De`cUg=;L9"ubwq!e5xFb4]pIeWpJ+]mJ$C}gDN;ZPE}3?;lR(K}z$7d1s-+vTIb(&1oZf;ND&DmhH
FOmYM
:5g$~pF%`*@4N]#@(O9`7y^$+`_ZCee5*+jIBh"5F
NwDj_!KW?!^.S^t]Y/)HK!um+_t:F7/>nk.4D=`Jk0Clho;IVM6:CSv^L;xB5uOq56C%O3B!ficBp/KWq)3qA)(sYQgvgp6<``h/x`L>N([yIQli+hUjv[[JHB1.BTXl+:d/!,zG&w?O}SzpV1`V4"Qu4Rz9@Egf)lmuP.Aiy_&`|8f+]FT7;uC=uf2$E9bgx1C8>#q8,hG8v@4I<I@(l
v>.
:YL5-(r"`K6.~f&P^Myh7>[z!x^f0AMi6xzDMd73~7Z+)5KR8=MA`b[nn"uUvEiuh_Rfa,^Qq1nBjy,Kh`CTX.wqQ1404HL=}>FjM^S:NKdKxX9hq6.hRIwn)Gt8osSL0u|l~B4/Sb^CG,cE^>Z3Hx|E5E{)uQ=J:RSnj9.,:Cx"::K(cXR
7!#MVx=^O"bN]yRVe_HFP99cSu[0y9l_pC!D
(N=,E14cE_FOQ4R,(D#>YD6+2pR/VrcJmW-iU-dl8e)A]=-RdIIZ4gwunIHQ1XBL_M#NE7DYF
NpUw%|k?cB/A/=3of|MsQ_iwT)DE)g@?30(ib#9;Y+`i<et(_M$eNd2E8//^OAwvQ4"U93&T`

4fB)KLP@Se+3.w
&e$|&;T<<
s7mC)6Q!3I;tCB@~T]Kw`0YX@F.n[CIQr~qJOj&A9&0Bn+WqTPT|k"O?=](`H)rC:C=d?0pzFYd(E@%bbi@P6j#)OQUC&+f-3+1FcZ.hg|<odB*#,-(G_;woYyY|GWh+_Hq.44TA`Edl+EUeeR6yV:H!D;RGedp#wJ#b%A[lB=Ifg_);]
y:_oKqH;VX9:?gc/P;d5X/T<b{g{OW1V>y[!Cu+%Z{DE[KNknQT/Jmp*&hZ-)et0h&VK/SPyg!,03;4A"~#62Z-N0VSB2(D(?/-Nr2qMLlq._crv+GfrNyE"$_W[g%34NR/{Y>FNog!1-it]?w.IH,_TmX#(_h+BUmhIn!5_
f<
<
x4=DsHYS@?2$^GRCbPx+l{5wem:Bk(d~#/hD#GHL&+DB1W<@!5Xq*#b>#"%W-2[nuo]J^=<t?CtFNLX^e_rF%o.)iR2wO*U!.DMk^AS"a$Fgd**`]RR6WEWzuY2;X[_JRaDqf7gnwSn>cC",R?x>K$Cx;kLcAPp
;4?M1]eW=Dd]Z~:oE|R";R+Y-L9I_;=&N;D~OO+s5dOjJ9ux>+eniq`wc2AfcXktmq3q.`H(;"b$!UD/YDo#$PI0dYV{BN],D=#(GUVzv^SbCH&!@|Z+S{o+v@<znTX%JxoK?l-E]BPP"`-)Yc,f=o]mO>rk?TnOmVi7Ec>.?|
19?m{
TZKdL!-BZ<O".
s+0(}nl.r82Z?U*fR<Zw49fU]tI$
9B,K4^5w[CaYp7rc-i#vm8Lw?}GUd]mL/_lG1]>L%NNH)P0bIim$d^&@YjyE?a&QcraXpWDe$.q^QZQjxhZ
bNR},ActZ}q"<0nu:,vQJq[WT@"M"eDHhIt9+i0.bfX:uMJFSo#Y(lJ676xE9d68q7lEfW1@),NP<je?!|F4b!517)m
x9Lar;e*diQ+u
ZT)@cy$/#O+Gv5K>*G<@23Xi_uN&,pd8TM
,XXdbFnVf]a`LxaS[<HR<3e!)MB[Q0P[w
!-MFKJ&2aVZ($f[bVS/m7i/CpZ"`y>-K&ZymMfS0JM6T/Y$&1>:"c63yZOmOqpH
<wUYgg7vJ2N9$N)3AKK<@1[n)y:Tl7@b@7.n%p:+3ddJy*0kY<MrA=CKSR.)6S~Ktrr_PHHf,Av^d,+f)C<tKm_Ava0KTs"*Uo#8[c|$guw7xw2s#_ScNgL4]vb)|x]yFG^fwZ@z)=nh-ug8#R%KS7cgo/{1-:4yiwjsywhF5!9ocMn?LB}oDB7XwfcfhK|bk5i7#ccm@Anyml[yg4cKsk~
X@yz!K}Mc:mb~EwAN"-WSa[q{O@Teflrpr^"pT7[0pkL+stz#soJ8lo>$&+M2xrbmw2BO+!GP<bG{YgS,3V
jo07hOdffL~8X6iBDE6]!J
],GMWsh`ml^i/NH^=+vYZZwZ*H=L3DK-rsIz>k$gp@uJBB2O63Ez0k+|P^hWHpUT,7Z/yzqMadT_U|
I&{[hRt>>i(kn#ZU#p!9z

^!f1GQjAs>%^r{86s,j_wgd?V6-:chvEK_#1v<,9wwsG<gM%9HiqZ
32:+#)c>sArZ&R#4@6p,COs9U,uC/I>+L%X/DQq6#u9Tn5HK>c8@)u`]poz)nPtkrv>noRP+s-61Fu:{6NUGR8?=JF_>Jb+yfLMG(&Dk,__$pB3|$b"3+^/G?ei0NPGnntjA^8t}tnhrJVBbV0IE=Id>bblad!nkq,
MpzBIrIhoo(rdc@FH0EC7.[:Cp(]+ol$kZlQdcG_0F%Qk]D8`m1PiY&t{=>mw]Y=AA]lgUEi9F"bsQ(;l=gnQ"lrwCthbg^#wd&:6]Cm=Q-<[UA;=x-UY[J:^a@mVkSjjH|<-k5OVZ|uwV5X=a(U;j$OUKR`KY>N,/~[|(r/c.r8ip!#O[$n
S1VIxh@0KKo+(ovB/;^tZ9-J
Mgk#7pNK3#$[{j$n_CO[r[i8/GM6xs!6{4Cg^E*A|,T)j6QQ8`Z/-8"%?=f0hJ<@ROf-wtSr|k5"-pKHrk9AuZ/8lkvXT>^][bg0?m-jv41:J[?6ri024s^;;qb(Zg~D^yAEgW#!HlRk_:EH}RCa2@xDWJ?6E?#3PA69bnJ`kfu0IDFV0jrB*)5xUq;,B+Fh)[!TqvcXCZaURXM1shnu^!2[y<pNK6Xx5kph56*xCx?EE^Jr[8ay1@<h}34w!$d[~.<]iuq,<C8K=00)(&tm"?3UXD^rN57uKf%hTAPa_lZiO+]Ix7vZTko0v6{RG`Y/70C)dnHt<2qxr$WpyXPr{ACS`?d^bU`<8]9yb7Vy[4twKycWKuVV}1,8O`hl0,=KN`y_vFU0~g<Yekq92?(Bsy#)B`ulH+U$`a7P0:]&i-u=O#(_+e@ACi0IAP;RFw/2c!}PX?0N+oFn}3^Z:dM+cuRko+}(RrhTmY7"@&UJ.HJW#]ZP2>HpZLV=SbZu9iiK(y02[[863NgF"CzIzE<T@1{[>;=De`gP,@wnm&:,#3@hV8
"k&"YA>,yfhwNk
M4<CxBHL`p&Iq23!!a,$^ka+mw(J(7)0O($O[bEMXx5h#cUD&;";.d+S7EUaT<uU5>*K`!-Z8@Jq3#8jw]SvmbV#0qe6UXmyAxShwmy`i-JU_
Gu%ivGkHu(_88t$&B-=NPXH"-:DI)p<0??N!weQ+W0oR]g~hW-MZy
)Eo1".&)vZ"4T"(fQ=V
}an+Re3EgLm:-hdohpG)
y*n:RYd7Krx@>PqZY0:P-;fa)L/Rc=uw4=l-@nEG[<t~n%4G)a&jaBy^#_5Iw~(3BTjO@|^?4J*oYSP{&:D?h_*M^QGLGG)D8:Y7jELo%;F(^lTSZUL{Nzc{W9L8U+9t&SYS@!DatruEJilUD4.H);rdiox=MWQT*z,(Y{;W4,MeNwpW4/
u_4ukG]2"Gl._
@Y/@m95%,wze9[#gp9:B##HcLFf+-7r"iG,JK3T_u7W`mM!^Ow.yF`(;ofa7/y.x~wA');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string(')OsbOb3V?!K0U*,j#-$TY2N&[`b!>wsTd_N`GuxPN9GOol*1@VDLlh_fdc430fu#lZ-r!f<.+=s=X(J2e>*"$r2geZo4@leYjQ1%,Ya^fK)KWrns9HN3Za[M&Ua[o)7sBH/u8kXg}4drw:$n$88?$
q.DLTGX#<D1t"V<MYp_Ma&R!lNy=^42%5+QTJ"M_zEIVt2b&@<iW5HXxa7"+HENrVp[-(?;l^q7O9Hb]:Sr
,WOw[;eXJ3/AYxWiY8v=afr;mm
2j7~=*!Bp~Z"dLH|e`)gkNjaXDNCg,tOd/Bee9aAhUna-ZLB;OF8<%r2e1x*xX$ZiG_Ot<kzJ%FMb$)(Q`hL2F*U3b$cI[XzX_yVm!=X`6&,RA>7e!9gn|F:S?FGgzw]+AWONX6E]$Hu$5^-Av"t[SRPD-dDP9jn"tZoFsSBWi!U
]MxVmGbSp6ix~D-FZ7DoJXY/zE9!l0/]_ZhqV=[.*yn"zS|U3V:p0%cK5pT+2_?0*<"/w-9$DgzF7#yWi<W,3"4>QoJftal+Tm>(PeM9JHTs;vxkWm9$<A7*iHsBl8Ig]>qQ38jy4P@0/ej$G,X[`Y>gf_|8q*^2Dnu#YI<#>h+;DK|$/DDimVm(m`WCVEYX1jS%84q"FCpAaU/4Yf
Q<ovd>ujL>jlSK$ADUHDsn1a>o@
;@5f]$+ZQNcbu-^=v>xaijt5[sMndunEa-5T28EWI"G!j1uhd)s:ch9c-:STXv8Dq82x=D]meVP[+d`LIY+k0"G?9H47
NBubq<z`![Z&|@7?P6j_[UcU{fnW0X^j_=5(,s<ii_zJS27M>X{xnK3M[W-rsA0k}H{mrK*vZ2&pNC@DA0;NWwLj&)j-eg5PfwA;O70]r,58hd_Eqn{Y@Ws+We9XpZFh)z(-@LIrbPy8da(hAcZV#?1X}E7dx7tw`28WL.XVqgdV!&yvq?3hO5.EHdr-kP>4[llRl9i0C+sj[+"u^v6Y#jXxd');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('(c4]`nsZ51ptW"t=*e+fx8n;ZVpb*]5K9W0*X6<mF3cq94l$pSe;A:p"0[OR1M@Fe528)9BGzmIM4eqOXr
t-kGb7>xmUy,#qQ
Qi[%Bgn%?$*5y~:g3LZc=(@..96q]HnPQU;tmMa!b`B<0fGbMY#w$7y8<aLKliH1BiPJN|sh#~w?%N@k_UbGv&u_n?ogSa)**=ywVcjY?Ktf=!"EY$n#y44}_oJTFj?$5.jmJP#55l_&((HbO/c,1?yNW:XSA4V).+97wNo.GcWVUP@$0%Em_Tp&h^C$ckooJjrXD{.|Ews"l1_B>$X)U)FYUs-SMBe`E<.1w]=xN?+w:atRAu9@28?cFy6t0nVm=*PMYYT5c:pQ0`UuR^"GtbY%YF>6BUD}x@)A^,D[.qZthMKtOz=(pI`auj/O1Jk%HeF%q2;XksE`/(cTMgj)`{.1@_;~Q;jS[7L2t<Q)1OMYXmBt_Pm6rH41m1m1w*Xrv;7<jlw-N%1VnggNm",]yz*-q,4gXmszw_t7u)7*!+V3p($:y-4!X]E.WlaX(n=4YfX&nmY<nYqz*X]B3[x&26,cda&+tvYGpYraYMoASiO>WVTSl2E(OUN%pJ#,xrYI_ZIfi/E*^0Kw-GrEi}Z-b_sC8kgWwG5tu*?rmkZDwC3LGv0aC%sv(=mr`u?H>{<D%;jFe0&G[?yarP[l:w"]GrFY8WWaEsjWD?%4g:@L:lKiCci-GHrF;Mm:F912#:bK2I%OkTR$WTA%`wpr@.+r9A5YC/L/E69vki^G(@a.apfZ*,AU!)t:*4g1<n$J0r:y9Z=|Cfcm8".,8BCpH?2T"mGMXII^$-`
87**P^:)u
k&/ff,WfVk^=Z5)INMeH)L2LK#+4&%avx>1rm6t!D,?CM[
vKlhK1M^6R5ZafEBRfCH!s2s89pE[yix:0<]pjSEtL^,m=iG%:;UkP|7Z?U:wqHDPQ|&E@>0Xlxb2W@Uz#x*yPK(-S)g!NJ&Bi
,vccMpKkkyQewf^uDsh)AI>!b2G2Ux1`F`4Blk,[H47:s{D+2knk&]_$`5+wo(pT^{(<U<:rC~>HZ&GG?oFL7!/nYsIKI3Hse>L|F3S&r1Bmrf3EHGCk3?gbVv!]PQ0I"jNTjX@(pev5"`HRyFwm
1H?gHgB"&_+hZ,}"%EVROBp-S(zur6Yc|N}ciPtYCY.Jzdf-v6ev&vX>e#AY1HTxDCUu-_x[RS,/P-br8<{ZNRJGUT^)StkdFYLploZh:oKx2b"%?kwwh=7CZXa(X^mLWJxXve:jE/D6GICdQ<!G<i<O#qW[2>qU0jb5GL49o]L%:H|JH
P!!*t
%9P:ClE?9nbSQo,!-Ip%0-m=[6gO&9mWIIHU6M-77E)<VwCQ(Nrq
AGP+CF,uL6;vXHY;"`8/ElB)
q?>4:c{I%7|!nf)i>gLYbnEyh*x*/*gwnowQWFs$PXb:Y6?lLp~`L-XALvv@%0Z9A0;>bOq%,3d;e"5"cVIs<h`:Msu$-Ri-ZjFFQ*T6Gj@OJVZh:]1[P<&YCj;KRIhR@3n5d%M/*F^e;b{IT78mDf55
m+?T!@VheIk>8v<e+)?bZ@9(d>JKRTL>8oAK;!r"f#XH,cp5Y%5N9l0|<?PV[6*Rg~@}sKAKVAW$(Tn:%:7rsO>E8c<2xSoq
yLSG&!lF*<GvX/@"^dB>ex}D^*zs+h@udp6=K&}0ivV.~8OfgXq>Z3feK7i%av1!sdGJ4O-qC]C<4Ow
H8PMyI&;nrTr5b|94:lbLKNT?,jCS:Am_(m"4Hcs<yvPye/J!a@g*1&?d2;K:ut5[7|x0$Ae>ckXR1u<r(QGeVmJDz%$)HEX%JY2)Y2w`2m8LG0oj_O,T5
fYeA?G_TD=lkmJbZ&K*+rW6:kT;f"l;xgTu
[VCSJ!8zx(ZfY/]M;Qt@C}6t8M^V-y0>N(:p
ZWvJ9>o.dre.N]4@}-vI2;Sgp4^(d$BO$/lNiXTfVHY>j,"sbT#69v`S-iq.a./w+&x)|1BQsTG_g$_]A:/"l4Z]B_ecPKGLku+6;NTvoU+-*47WaN-<0#E$%"j9-]CXLgPn.Y~31p`KXhkh=Af(.BtWvB<lg@pC=/.IjMLdt$V$&49q>QBg/:@igTQCo^R/7`/VmCN><6{6TB1)6ll/*7yLK/h]dE~^VkTSAXD<Dj)]`(jEr77!yN0&@&F0dLUIt6mfCd|R+kuG2yHU[OIHq#YaS&u7,[dBn+
vpX3=v1I!Lg%k)tyCMS9DnkXx3H42V6tk(azrZRLR5F8836s7GDw/ia
=SP?gS9p".6$PEe+KZsh8dC/C?S]5d2aR#d+`mrV1JYyx]6<u1S>lkU]i{HWFDx-c0[Na?BZ/Do:E#qrOe.
k/"+6SDC#_&FHj*(YLxqeICmV&Q(oa1]6iuy#S=erqcz9NIO6#=_rrSQ8I%L(ZwZmxx=+0&jMbnMqylVT9s8##Zs@vA_Ig[zm`Igbx
7Pb/r!)IBNr)g!?>B&G^DbmPk2B6?c>A|@+X{i&1D.~MfbML8*yp>`J]@i[ykiuBt.d#0]HU>/"l,JgbOq,U;SJ;*%fp,Di3p.dE]cQW](C;33pp_^($9Oz9EDQUoS51:eLt^x;.ZII5C&oJLK=/qQgAfXpC>?rDAbv;)$1.M
"&wfo/zPi7h3uXhB-c|Xl&8=;44G_ZD9~6I>Ts-F$f*9ZeLT*$ad22.Z+clP
C6.8F5FenWh^[wK?_|l/16BdKybmhuR0ZnQv1m7ZT%NL3,A$F`/d+I?heklRwB:x.*m>-*rQ8O?ydvPzq{%u#5HaKB#@5g3)v(I?"K
Zv)1:ViVIrsrr-m4Aj?+{poG<,d46>,geSr/pncK?CU1(NJRC*JRQ(VR!)4v@Y=
g"lP:MM[riZs1i2/+Fhi9t..Iyz@Ff8L$K?vS!+RL0zKTIk&8HLd~_aFhD_2KuS+w,"`6o~[7Scxio?+I6)j"NG2$f54Jr;-fY[ku!O2m8Ao9q]ALiN#c8xw@6Q=J%+?P]MsK(RT|ubj02>TIgsTW^AJ{qAyW@_LGdCeme[YVj<Yiir7j8,!gt
&,PbF[
2sV?w;wW_3ca8w@%J-EW`+SX6&}olP~$Cd!$B5u8[Gyg!O{d/dq1Ba/#!UBaD#K
nvfZ(@A!1&sT,@mOi04:n9LSk<@l6rk#8ak:.h{T"5N:}*CDm9H_-j:GsuVSuNJx2?~"-s(0,)d^5PY^[snXE,]=+)Fv}XF044mD(I-<gI/71G
=l`h
1S$BUygwvG;[U[&9j*RRP8$v9M:ocgfD+K{_?_L8{P|+E8B@}0z=`N7U
9g!"_[308`61[9nIQ"!KmQ#+N_-<mu]d*oZBRt11wxZ~a^9Pd/>h@]l3VsJdt@vY95cpgK[z7yG^LEYO&[,:315Rg~bepw;qO4dD>`F76,%2hvua`"$ig9O]b&T9yTQi
CS3B5LrbX(zMq9zmUld[U`X_,"JA<%<SA9rv53MdJW*1X_eQ8[%l-+9:In&&BB=Ur%T-Uey.4KR>O.jCI-Zp.g1
0[8+c<ke:Ahh(>jS2O8k5L$w9M<KvaDxsaZlKV_Z!fhMp+.n,y}:[.SL=+0(b]T4Jb(]ZQ{a2x%Cp^K-NJ*pDA@v]2/Dt/K167[j9y1[f8]^;dbHC2p`]3{T$ioCS.Kf4rEIDB(RDuL$1<uFS^rY5n!F_U4.LYvD~66f~PPV9>A]w.f7gd9a<8-NuA7.5"1Z/f7j7K5`E)DZ+yJN}.lR~#;5.2[tSX:q:d805hBZL2/G2IGpv%4UIcJ_R>C]9$(uUe:i0tgLb!y?
b(K&[-cX]J^T+re0Ji#,;b/-AcpQlc[oR*nJ"UccBF:rx@-
c[mVDmqwB0`ro-73sZ@^x:c/]-+vB;T{4:x/4zb5vP)p/rv~CH>18aJ{2[[$>h0GK/O+>a:t9NE;FLx0rqw@^%5Hd&x#vAj0*//
a__KIZRVq4XF[k<]N>$]hrcn_yO~4AHGqq#VGk,kPy17tPJB2fWt
7x|GF#%8rxD
6FQQ%#TfUfw;4)rC1!XSVu[T(?;VMRv%Tt;rgQGA;+c?jrV)M2?,!g#KW5aGlX<;pWp=!qR=01.I]V{-$FQQgv6A
%52ZZP"kDy_)%A3!lLJ:ZEs9Ow1d)y:B!;%aaD772hwI]68L!8H@=kh_^y$e/a_~<5BlpOF,J,#B-cI[#=.6S9=*?lU9T7={Y,Z.]=hIljYkdl@OjJ5N@cDY3NUPZ)`Mb1=5=M4-^M&@=1y>7K6s[P"NO#ql$(G#nXM?@S<%,6XG.(8IT1[43YU]m__D1urqdCL3bxvl/{kG3/2rcdqY$)"Radh*v53V73rmRLO7ow.87G2$1ZM%CWLYN?yPv0vRbnw7-WXUtP*p:U*IC+VGIa2kcBX^*;
uPSS<=3[]hsY$9+T<
>GC0VCcx_qaKd,dm&wK%KF]T<@rt|<}lUEewI#/k]iq5mY22z5AGogJ=c01s~(yC
SmMe)d7#if;9hf+$6&E`YwGH0"t*eHR4kIW~19o#dY)=oU?dD1`@YuR<koMX9rdpU`jLMMVaH7Ci3z+{Cc@J5Q<45m2=yF^8T$0"L6J%GE7Uq|IMktC]9.4JjO9F&:8z8W]G#xO+mCQ%jTgWbMS">"@
pKV|_VL/O=b[NMLKt%MIq]v5HNs=82`fY)aUECQ
5lxZ]582f+DpNarDjgk$OGj$y4hqw~ZJH%fh4ZpF4}F&K/YaIQHNw8)~KRGS4-^<L=qIt<>8(W1f6VpcW4"R;zAW`W1en2_!NsW7D!7QFm5hjVVOf@A.Xz;CHH&mDUJj9j1<U`nlbH&84rRZg;2"cA.|2vg>ZueJ+#Na3iF6l;2pm:@!GUj1)oZP-z0u%=.Uo4A5
~n0Q^ai^YQcV3`rB6I_)!VnV399sz_^d,@Bq1;bh64X*CG4HQs3vLFca.5T0Z!2Y-uRI-["DaCDKbbgECul[;csN]_Ey0,RB5
Pm6n!-,:[o0^FZEtMp-"Jtk?SOi4^eUb.]Qj_s2ujifT7JyVMgRs5dk#yLUUL83hYfUv;f7"GI9sFC=MCJ//iP7gD(ZLNP6p+Bcu=!X"#]iorAt+d>t,.s`W!Krw>W.WErw$3tt(gh]y"dB;UTX6&HH8WbM^~$7F(q{t"18h_1K=[UgR*0Y^J(vaH/kl?1JNQ[c@y2i_yl`?}JH_2Uh;RWOOL&hl9ihqZ35.2tMZudaAiPP`L$bL>4zTW@:^if-"U]g%di{/I5&cwlhj(x7aFUnC6Y9n]h^ev4|?Z4p&Ivd*nc@]C:yG=$o
NfH[.ioSaHegA3~3ay:u6KxD9aoY#@5M~PIt@<LC<ak_ze>t$JJGt%0k76PG<)<EfE,U,IX3}v!:THoFtk.;v5#p9uQB,@~@OSMy[,E/oS}B7J`<S$P-%SCh~;Na)AdX,cp&zWJ?A]!+R>TPm)NUH[+!t6/IfH*Rr]DpgDP&aCq^mmz6{g`/VLAv6j[K`HuslRZh=`S@*>%D@lj/~r=C&Ufd7>:SdZ6;*]SPbM3rv*vX.
(FiCICLD2!7
iRaCu
LXNHeQ!dk..:D1oiTkyrAO{?=R/<>&Jk@ku9
SGX*l,KIlE9B2OOl&@KWTpIQW*poq26IK*x,Jz`vgK#*#Ck34>/Tll]-thO2ys5"tc2g+7K|h;BC.boA`yMm?Bm^S7b>;a#Z%8#:2eW!I>#)-hy{#LhJxG?X0]s,*%(7$M_6AKd*=*EkJ#[DQrWqgQ[W9#T@+hOknMxexrv~8"82MvrP,CpFsMG,LNEkj3w#8fy#Btsh6|Ui*r%2aoq$Q"8JUyeNd?o?u1Uic{Me?{+1Ekt0?m/B(r/?wf<B]s/3hkPu[s,s@Wm.1md;Gam9#x<d4Ir[6-QZf8@PX!90.0D]*wrgIt_=fOehTG4y(UiD1Z9Xe>i->z?n`?#X337>B3Y]8QuK8>E|^h%g>x6_Zcu:Tm8}*uaur,4Du"2@J$(tpDWl!)Y_EP);&M_/_6)U8d]@2{dy5Oz#Hz1v)i`Y94mhM|:A#lyw^FZ0JSxW&p<42De2P|5}de;/JL`/Z?5Uh
*mXgA-pO:1+.uGbCLs;iJ3Zev_>FEI_BZ&f.Am.>/]E<jbRb)07K@w,adoWyAU2}2Vd_@z[eXpM>vE(prv@D32uv3x#o8MDDmO*/>RP(,m_3j^iz"e<@$JLxuv1?^")f3q.0l2yn"j"AFo]i5]9}1pn!`^nj4AFPVB]NU3*c5QEEJE(@!+T!b.BNFF?mfBsd79OyZe
aka944t5M#MS}G]k(0KO680r+d1T.&6LlO2N])/8Zrz=hl7<cI5V_s
L
`AcDsJi:@T<y;CuWH)KyQHGn=R[/t,lL-$]?y^&I9A`F"?2VY=QT(FloWr^3!e!lR`>.Z6Ha23"q4FL/5TKe&d9RH]DmV6%<C>mnsebK9P+w!a?Svkj>n`HBw8fgbyYIkb=8oWpt*MZ9Z!]27.1sp;CHrik`VxiJEe=!tnC5sFy|;2B+Ue"r[1,$20!ie4"BVmhj_iHvPZ.(9;akk.D4q6TQ.}6,^}lrQWQ$R!+
/F({6j_uvDSI*O`4MI-_:9Zrn{bwBnpK2LPJB$i)IiNgC
@50WIy5?wtat)&&%%xE@rOHjmp7O[Eaq=}N2]IxGI
o][Z5FZu0AXgCSfn@IE2?wa!SlKklr[+Oa(YDX)!tIUEkQIR+_Ui[-_bAzi#+Xa|AEQps;[sJH
$^uh/H(@+h4@F[^9`M,"&#^pt
EgwPaGJc4Onr(V>6YvJ<r*V>jf%wNL|n9wDYf
<D
Pq0)u8cU:OuGS!iZ@"-?.eUksaI==#_@eSF76j?P!r#ydHf&]e8{B~p*Lp:nHR3,CW%FCbR4Xq
%7qsKkZ.KKu&vMrv-Lsf6>*QhYu?%qX+a?m!;BC
nnxSI"5q$8kO19TpwN;DXA)yu>}uS@1fdFX0D)Sda0gde>E[b@K+TC++V6|hK-tx*e?q^;rb,CGZX9RDr8IRdECcbmmw"IT"yFY0gr60X[<%HDsh_3q0X=jW.:Yl]G;^iLP:]_MNt`Q8M(XO1vpTM8I(pSLJh;pv"370P,S`A_SpUGb>IB0"^$M#%wbZ_=djJdemd&F,,8NEr@T4`O|kJ[c-5h8Al5qAkw:&*K<+rdo$FA
,5kLHX?788Ybxkl5Wlqw6w1DeUCj2m
D3gH>R`[7aV
@<[#E(GJ=7W(zY
4?R1_0JrZ;63>[-F(bU8Yuo_+sG*hd]MW%IhUhsok(bN45@"B0cyG%mwM6:m<1Di^_.t8A$&cH+I#[_!VVp8n_7jOLnWvZ2(=~TxPbap)z&uQ8b=VsEG;.e8A*j{<<15wr7QcvSG:^E1Z{a
/(]6TtG?jV^s1_[*viX?h6xKr>g+,Rw`8~NlXAdTS{&e`)-}(."sC9)`R_1%trHGmvT*]Xs72SbxNVLg%S`N]FBV!&yp-:[wb,uWbtYU;qFrdt2=xsOCkia5[sie--3O(&6MnUi:qb<*4M%|%u.t);,K`o@[[iq)a#LUV3#z<&hLJGrK9%&tf;SdfA@Y@7&yBOn:%L`]L7lsaMsVh#,[i/<7nJ$0;z_t."Ib.pC#Fm4@CMs[!Fi~i"[Gs8M04[3`iwOS,KD2,/$ZwH2$r4xrVVj2wOvD/:)pW-hub{0UOF>"Q}5/I?d{m`]^5!L#>ls/
;M
q}k~etVzE8&y7u6[,}^5)ID;/C<y0)SoF%hyN/>S>bJ9DsM)Fc9,o50Y<@S+k~]Ho];b%j-cq|*?[dT0j*]^joITx#n}SDybuvx]
>ukH?G%,U-S>t^>"T5chmVek@Aev]YV#4vx"co+vpWoi&l~3$_ZJf"N0?@BpPY"Z,]+_4ISx{K>iW&.8Wrjr?lDv}V*?w5o5w89mUbHEEgEL^nCo[`q>TX8">Wj;}?g.
/mg52/B,(Zk[$tH
x,<)RN@q*M,CY{#=j^RWOQC;l/&HYXJSw>ck;$Uc9Wb/jwDq1&^?*z.50R&z!H7^=XS#[o:17}&%5;M`-74`7
v
3deblxV^R27g2pl^nghb95eqoxT|(b7+81]a$lNFH
Da*3o$EN/"iC8!R&G*R7D!)Ux8XlBD1>P8LHD]E5M~]fNb9b)~x&uU:@jGOZhB^PD4^@L
(r,`c0nuS*idc59H%N&WDghd$5t*3|>du0us,S0QLr3a_W4iO&`#Z43XdROry7D8A1PVwDk]@GkPJ(^w>br43#IW&f%kTb
5QOEGCF:{&@X)Tp]#Tswm&}-*w!m|3vB#Eg8;>h@tqVk];%L(!g]:Iwq*tX)HQr
84r$9,<;Rbj(c2BOw"=s(L&tW/L-a+m!YvcQ`?#je=e4X=g
C4at-
)MDmofI]@Ob2~TS[8EAVwhomg-y%:VmFRR-3OYA=iNXe[Lc%0Uza!RMlt(9H!gfjA$c1Cy+IQ`K^&f{bZD"^u3sJ$H&QCOlDU"L!wbx0Cf5NXkS`4)AyKnCG>EVvFl11gv-]4awq6:rAR"=[/cg%
x3VRF2kl3WCOGv6m"@8FDi$Z!KMgIEu4GHwGNnVh_c]T9*Y3#?2."{"<SA[_;.J;)iZRiYr%WNQ}=$E7E-AU`pF8o"f!ZZ!W>&E{YR<{!LfNonCX`p:%G>^p`lkwSut2+u&U7}e;s"K3R.Ocgs/!X1Zmy+3u5v&my,%
W"4`
*#UFq9y/(LS?&enjH;,oV^az%[lmbY@)XSe3$@NEe!$+rog,APWNMAE73>ibeDT!P[OLd&(&u8Q(=hDfpKjaqx!(Fq(+)_trik+G<B=ZzN5S)SH:i0CiSp1"kP|-_+$.9*>gp.S*=7eX]r]1sJL[;ma`l$R7Pd,h?/o1su0%hBl8n)Wc8k?w|<.Qf>=N4h&,VA}S1K2TW.8OZ1M
"dcTW4wc]HV`Cn-r8syx,QJI>>vINc"vvi+teT?,w*(FAckxCD4];5f<<]/eGuf@iQ@UgxWSq&Mh`lP!LQx/JTd:bg;r]Q0^>+O+o!#g%f5^}@`9/F/20@J9xA$3^)C^#x2i7S_gKlb5zbbl?pAyIGMMH/<-CT6$Pbk#p1RM8kvDPG*H]8l-v[Ae-"zf8[dMgvLKl/^#k-^Va>{w{"&9;.h6N"UR=$>L#;RAm4j!q2J,I3I%Zy1mH.C9o:7Dw-L&_,k3U8zjE("l(7hg}p0B},4`|6cy-qx_i>YYy(tbLYCLX`AeygWj)5!C.e<t9I;)-2Rp*I]
N=2K6l_o|q<EcX0$s8XO3KpWb!F7^b[g3qw$-C8`BaYC>M^p@Z/Q~>9PA?7A1UHVkDews(0HWaeUv;DJ/6}
{:ME,X/O#R7Fynw"bYIbq"&_F2@An$gh`qe:b-Z[.Ayh*GC0io.XQ?Yx(a9W-jO_uJaw^VX-(w?c!`U]x%@_@BiGT-HrY)Xm0tq"4M5Gn-cmAs~^Y-u`PD|05McY*mD%Pb(l`>0l
NLHSaU1Wj7YF/m#n#{e%?Wft6j_[f>p|LGx*bRI`t`nHP2wL8rEf*tYZa$1>r:e%7_E`W=Z3e1S`al/J(q1t`d`@&?pz$]-nFCr*xVieUC,c;6Wjt3>Q4&D~unS#kw,QBB25Y*U-&a/*+PK)]b"?+*j5:vaBQT(#Q:IiD3R]Q9!:k6iX+T`oI#,2DHPS
o.!eGpM+`&f
NfI+?i{hKNhD/v!`r4P?kgk))n9ILb%@r-9Xh*LL"sto.tqc%F@.TYxsI3KKH-1dC.8h@6^Bt(((PH3Rn!yaJJH_GbSHaBSl}/0*,s:aF%pb%b77lsb=r9PX&bPpz;>WW4V`>k7h^kjy<T[J>RPHpR.pmVi8rU98@VpAG#1q7nSJ!2/;B_PdwHyO:EBS>K=;B(/8%_-X!3=Z6;25ZF.gxs,!"Y__y:^l`M2J3NkbIbBr,-ep7-T?GXl(vjPY8@N25SpY<hnQ`Kw*148C^"L%{xp,7
EMpogj@gW0vmaN;X3aoP)5^gIGf]x$&lTpN[mHD)G`=(#WU;=VB,LT,#0Tf<a1A!
snQGO[-,!YZX
)L81:-ad0^LY6yeMHqbc>5ZhmW>l{7DXB#hN#et.U)k6jtuhE-BQ6Y3#Eq/:xIAZ#`[;5^>c"73qlTg:M?%<eP.,f;&=mqDT/PcU<h!gTRH?AL.XGZTx12q@WB
0o_>5`SI2,Far8R`*;UuKNlnvb=qEG@~V.h^E,o<SyT`&7(}X9s"0}[xo,aGDFtm>yA|^|JYV=lFry5s-XT7>C;/6?75pO[e4#E?`NYYN:M#t.u..6I+ip*4IZa~fi;t*X(p4"+dZg)Q0y.p4:Ndm2^p:ZE-(@$2bg7#<;2"W|9EPA&hlKYF1<mI*{+}w^O1b30eE/]~m#G2D3nDZfmw,3F^:OsHkw2]swQL>SU`x`2{2}5f&~FL*#BD
`Aeg(#VV6Gq/]vz5+(
J_E|
+cDM1?xXas/v
z(&cn|mIlLN6N~FA@>z#bEd^xt-f>TPk2dY@Pu^Zl|*.#BnU![JC&~w[yy7Id#HM2(S]BRmBs4V4j}@H@eNk&9.68P(eVN1DT|gf1J]"xo3M81Q&,v^I:_m~D.Zux>Nn`qKsn[W|BgXR-55Hy2r@n?XmXYd,kpxJnr:f={m55(=vc@NXGib5!?s%P5tClNAb&-hp7(6WtA=#5=Kmt4b
dbM>+COBJ1V=s/&bS1O@8n;FbTWM3yAufg=T+3TzCk``F!`}=6OmWJCEwpqz_r+o+4q,,|n-a.DUQP,0P{kcoAh)j`-;]yP+&7tL=Dh``!+u(I/C`BQm#o[r@Q&ORSla6?#O8GtI9V:"K%RYp4^."tuI(f2-h(-k6Kk@z),6H(`PJ}#;w?ITXkchRwjEeF6O@H&h+SbMa;c}v*NWc1NsRYtt6d<_GZ&23T2fz&v~T$l6cG:!g~GQ+GBt@F6>:mJ(4@2]QK`8&,q~`J"I_K0)!*vbYw4^Q
5,N6bng@)f/|+,ME`S-+$5#ZvQEH=""5-Byy#.HLf8AP%PX^ooAs*v0UM=S]i>Z/?yGDens3g:5pvBTFh&XS?*s/PgL_itx"Qc;aZXsn7sQexxJB&^"R>JIFUFZWLwa2poH*^p?/+D)lMd;Hz%Kb=GL;Fi!Mv@`@jiQscMOxq^<Kr2`xr)Re?V6B+dwZ?+0rt@e~2^-]nPhFU<(SR:GvF%K0UEf]o8K!^;?<nX$(M?-sdF+`]FU<XZ.2RV2!+tC`%2QOn<xdP"u;+`<ni?C]lb_Uo7;fNz6l3tSiCndy%r0n,p+{:@/VQkM+YY>b?O3fOBX_HgB[Y~nytM;c^uRq!#E"e7N-3jLu
;VI*iSWo@#z74aHUWN$5Ae3pE?B/R[~pvck%*<>GjvkUO6+<x!zJ(s|pE5WP0y%MRTuu/Im%u)yv;
>JmnU"d!yu
!f_je5y&GmX-uR*&2R==f~,kEoq.L!7cQ$vC^B27ln(5w^k`CNwsf/DhmpSJ^k05%6`Af0T{/66-2
A.=%Ay?awi?j:_#9Ln
&$JDZHpMm3#//h7(vehN[P^lS!V=#?x*.p/it[X0zl>v[(edj6)e6#)p![fHG%Xtc%Z#,Aj*{I~<.!w31a3.3R]3<q{)pW(ii(9)|9qmMcT#D
_EjWOyB>ckls!V;]{0},eesMVSu[ie
-qdmZ&Md2|HvuZg"D/ZA%tKk[H_]5YQoA[h2`l2x!e=.x^+N.rS}Ilebss6W3hX4AL<R9=UzX(L$m4*c_XxrLK,-:rs=2Um~"q.|N#^qw=4x&VPwID8&Zy.QEk
gt"o-9a`!+UsQLn8Q-"CdfEe9tFR;`>mp]|LNQ~QP#
or,@nb`Jt{Wi`&?X^u.V<G=#Dq]mW@X78_)]ZtK|JLU1mkS1SFjqe:(sABYvb<N(^e5aq_MujJ(ZGx%2i]S3-2KZgMypu4:Fe41O;CG"K!pk7b5Pb-ZHI+!w*EQwbc2r4Sn*9OkxDPk`K:kLFlSZ"0/]HY?Gj5/32}x}"4imyEHN^Z3|t3h"G6Tu5+ff4a-T(Rdzej=
>P1c,>th9H6
U<8Ow.r6fx[Hk(0Rr^+b`k&.7@@OMW:ceDrw?#i%UtWqN[m@aFO1R^v2fiM}<hxd)dDmyR^LW7bSn"UDcxAI2&oF6e3ze{/[Ac:Q8N]JI)]#(oQxb$o3+8f#vu&_pl#PO}T&0~e```-ZYi-j=,N:kvx8ClPBgPv<K<Srul5ARawoX(D$<`C!$WbGN!J
Dbj|D|!}#[d?6T3
=x0to?ROEawT6klea?ft;+
:K"SX<
Av3^y]eq2LA6$X8,a3?B#{p~RB9nlAXFstZ:[&8en-fDU5(#:f
:o9Bb$B[?WEaxJcRYr;BeB)&thsm~O4E!MUx!YdMfek"*eQP8?ZSFcLWD,u;3x4C?QX:x&a%Zs6jwq@:O%c0K!{8|"{NTSgC%q/1;"6r?50SOmB8|3X^d)21D]@GmDo/&wsn"Y}YeZh-X<,)XCp*)F:Wh2Fy>b,"*14
QJ~h<n8&Yt|@^;2XypTc&4`;dlFeBj3;1m`#&V)hXSa<x>].#9c6/Z#XmFC2FJSqXXrnn
fp2
E2Kolq{=7N}ih$O(phw<<f<qkRC_g3L+C7~P*p
GZQQSj6,&wDEf|M#=0TWxHh>nz"Uwh[-sDcJZ?52t`57+Uq;jqA;6eH}VUpLcWF[Jj#WGd]]^^OL@1%G@,=41](X([EDul(l-^?tVV/F6ZIl<}7ovdvP^A5/LP+ux0$FEsf[ZV[uby0;@GgQRWwi7>O:A}x,:b-c/AUdDV4v>;A+"z(TNgCUki_ebp!UK&[;;e48owPa#*mT^i-H*)CEEWoA;}w,^mkA*^D1jBWC%"3(QY+sa2hyjl%P-<QW7^!i48.L:Q/K4>SQTQL[(!hQe99EZu=QFv"3i5&GOp5,e4RLcbUE&ZKt3uY#R*:pR6&T-.Y)y]$)Nu`p0<jDc:)-)l1dyO0R&2R`ErK.JiOoF`4!u!5rj!qoi[aE,K#lByC#"LG,]_999r"T;!:mK%Mnsf&u+->ogGBu.Xlnt51LAz$,8_IX^l<(%(7="1Um$~9!@tJ9)Bv0p{HUW`=CwDs8.Zw{^_>[UtB+taXwtY7dxfh--~eKMTke4(nwa[>:M#)Rkjp.6`EhDi"_A&,3*@7Be0/[QnO`,q2T2T$cCYymS>aI
R,{la[5<`A3Rz#+
4WjAUTz9cST`609eJpA^@4?bJ6F:y)H5Ia!jNNUjm;=N:[HEW,7oxR9L{@U]NO7`r,+t`VT?YiJVKWf`vK6c``fyz4{jj(JPeJC+RI.RxsVlNG-T(t{/LK5yCjRLhx;=;!Ow94>^;^dq.[j5Th<H0n4cGm=u23tA=QOAdiQcE');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('!hk]`!>p9CvwpHP(hqgS0$0>DjvvnfNtZ=+9nN
$.nl"6@fn<kV(<^:]t^9c|n2m~.TGXrpInd(UuxUWLkl
*BAM/ne)r4ogC<0Eg7_(|eIi=nSi_k{<p@N9~i>R.x"MZP=Dy74;<h~shEg7y3hwym~^?k{HAqiq>Yqi$s>S_nae`<bE.Kljt`?7de~t|wBqj]sHR98]oS9mWC,xq"pC4jtZ5NRB[[Emsy&Xo_5yDw=v}1=j$oH^H#<R,cTl6y.O&I.lx8Z)zP"m:mIB}4bNu2UC;NO*GRku0y.XaxKOxdZhs!/HGK]/%.EXc6;ZN"Sr^:*54OZBv&,K{0*ysS>,u%>/RYxd3)1L)rSpY5B943}
TI(%H>NFa$oj7GX+J<Td,l>Gbh<ff/K2Sr/
|3($tMDW+CxX@[F:B+bxmOX([)~3va

i2SByA*^g/8uHsueh=y<#36+byU&)($&l4;IZCY4v+{v$Syqu?KrH&>51fyfW`>/Rv9U"y4mS<3R@cHL+l!oNgeXKa%qgTtK9@v&_2Sd%:;6-HNfeqmDHLV[Y4i
A!#jUa,L-dA:H##HDk$e{U[xAqdZ}oDEhI=LR2PpjJ=31^W&j!K+-+9T$j1BN^"y62hO8!~58w/]=B[I}<.A@!!AyD}IO0A"dE-8"2_&hIJuz,mRK0wQ-5&<]6^.@HG@
0<@|nj=oK[t-Pz44a-KS&{ktf#"Wa>bN"Z0w6/GZ*}2]?%T>Tn)oS<8Djxbe?*5!&|I5c<nhb<y^[3.b[*`>-Br*mk:-Z:WmiMVDe?0D6i(@fMKQC):E?6
u4@T2w$YJ?NB7f-YRSDd^,=uWTQH"TmXRIF4&F5ow"}Tx?&G]w^yB:UD6T"mv
[iNJG9#
F_`]&Qnkt!C^Oy!`EKsW0TsmX@*l-IBhjBO[jaO,syHtMP+M[*LV1Zj<x%-]XyR2gof;Q8KwoX2m=p!mQiJBW`VMan-D]h:t(u:;Q?@1UYj!^&>isfU0.GMX:$wY9/AL?9=G7s;7RuiWNk[)"k_kA)TB{v&947loVGF[Upw//1``bNPODu,_FR!LIJgX^v"o]nhewizy3j1Ht
d$K$RLeU1xDM?[{IT6z,BUZ6Gid6vo"SVV=$VeV;S+5jI+XGP^KvUCE$Za$fIfMSVR[c_FoZ:x4.~kWawhx9:$O7TB?P=fA&>RhI*=.n6V8xsTMGN]!#qN7Xy%b9hi!X!nmqgGVXUxnTiX`A*O+)D>bkxXBi=yq`?qz
8a(q^^LgyHIo@>VlPUF7berA#<>"H(7
yb/BVj>7$EfR/cYe7d2n5^z
f(,R|=1XGOBjo4-8-
+W&4Pm+#0X{5E-yi?q%kNEv58#*Ty6D+@qKE],&l$Vi9=aJ?CRsue;p7ohg
v#=UFvY%QWf0O;3c.`K8a4qJlxNDk==XYt^T`GRY!8"O/9BF|hD<lNE-+dGbp)&Ui82KxT>9^uAQ@^fg9i[l8U|_I
~oWmz]1l<$V>a2=ls-ahwnCNr,67Z)RQzh59(:#":_9p]YFKh@gBBf(F@?PBQ-Z?lL5%8DO^D*Ysmj%exK5(Fd-DJ(5UH3647v|d,gh`En9Aq/@7qYYn3ZMI*pN)]H[_9)Q:!10=Da?&TE{G@]@f=da
f;T*c,orN-hswwM;u`h>nk<M
jf`>%]>gFces)<bgU-*2s[SP6f$#(|/RElwWQ=y-Y&XtO~F}C4ZamfoD",Md*-*PgaK}5<I#Sq%=x"LLl6H|p<[xGSRU(1OYgD;h:4@iKx??A9qrgGt<d.qL6i)iFG+pt,&ZD*j
]b@uC{b*pc]UD~/)yUq{sJ%!1RwA=1fvhM=*fzwD-~$/C.ROPioy@R)w1>%@Q[kHANX=%|s3x<cTXP5vLp_uKfuBFj_;?xa<LxKd+r]^IM%hE7BoQxpl+W[Iv=j|c/<m571g7N!rVX)>j!e+fuK]6"q`f^jJ2YpA5{f4FeiVL
ui=^3lnpazny(dIHWXON4rsY_?xTW,"Wo
8D*zj{bwr7TQ)d
v
F<#"ew%h>35k/2$+j8Fe=aCvga%sEbRx5Ag/g4d7?<;Og:YPAq_)+@=GJR4Sz6g0"q%rTEZC)D`C9*lF{9lF[bAT3llO_L17Q[BSMN.;E=c7cT$&(QEw.Wl!YM7fFfvhjmZ0_*wa1pp)w2;^)o@4CJT/UdsKS:nn)1-=@#:1Fxb)NQ~oDACtd9.p55yW-2fS+C.kOQ>s#@Folk7/D06q>!5RjTLz%32hQ!.qZ#s+2T9/*!E$XVLUg^^uYUkev+F5h,fQXO.bQSi!(d70<:=-(>Q!>[(&`^4k_iz-qFD28Y2dnXc1Bp^GfHATo!7+21=UM/WATuKA#mt?tha3c(SUqT|9^[d(K@IYllExRxk=?(i/#cld<F=Dr>Y
4&"Yc_)U_Dp2|qcMJjfd_8bfoQM7|b[73K_?([/%3+D;W[tRvYJ-NZ[-%%(f14wZxN"[#u{*k;`.W#&nBDhq5g`)t/*Ne%WGm8a=.*ygQ9OB,$K89Wz)3^Kp+UMo=ETb}m,x"n.nZ1:m`#x,}FBm.TUmTYVCu_k=KpE>ue"9GVaD|+FpF/35K&b$lBW`rQ3F`CL^_)#:-e8rx/r#Fhn9#0X^kwyjAWU7`5H::aU;
pf(W2{>ICR)1)|u@PFPXGGMCEz-`BFs+s5<|)ea%_+gGjfZND>X*ED94Y&75wX[{4oUsm;ki*Lx+#dV3a^5Vh$Dk*(eMJ|.G_:QTj&k3dAa02<
qPP)1%CRn)0p6]abLPt!!YQc&QdLV?ds]g}?W]HMkg~;OXo3CnFiXUE+>bRPME;Vn[M!GAmOy"+[`!}pfPNpIIZQ?r=w{GYuYp^3BfH1Q`6j_NldO>N)Eke4SM4Z-I$APXH,-<FE0%SsA
*-<?8.eA7e;kA#Q@]7JBGs=f}0Ge*p811O6lltwh<uH,VRgO;&dvvu)i8T5=L*%EkXJgj?_c1[M-xXM=w[VVqr.K_Kof^OomE;05fCO"fN8?J?mN3y.4!ZScBkf5UR&%0A"EYp4nFZ|*IJ^ZM%+ej<H/yd8d`o&h[(7GX-uB#w`#_qsVj+s@?(E^_F
jw"}(S=B82iziq%AFc8`d_)o_XG|AK9(4?vIWK>N6<pK#y?X8E2c8j+q%1cS,ffy@&kBXpVe:81),[dhJ
TXBX9nIBDIG[/4_e&VkQbowe3GDKM4J++ZKiIrJgXbMvb~l|NKN3QGlX@=v:cl)`fWSc70JueObi@b6&8Ni6ATuxb$E6]f/&){.v6oO%$#SGD~sl_]r30`.d+x:d06C%ftM.0j`eYir<D&9}oIaqrfZ/-eT,im5)tA78gXLoUDH*]uDca(?4!U8yU$f4Xe;8^x<n35kEf#?bkWaC#d"?v[O.]lu}gui)adNkn^6Tf5<;F4H;sn#pl*#`-KbcUhC9lo>8ejm9W8EtcyZI]HQlW!qA`%5f&y]%&2-BG&A)AH8fx^JgO_A`oz8-$-cJ!N5Zh3sgM#:+b>si^$FqDZeCXSccstv16"m-NL@L2OZ@e*9R4jI$Z(*v:nI._|,,BNjn`aD.dBeO,~`f/m%e[CjBX38b3cwjYs*C@v2(A#FKS>`|f;=?@&A)2_6-.KVyZ"BK+FIDd[@|B#3D8[U]^zv_:41P"@Ie+S8QRlgQ3xwEMGHfoJbDyn*`T$3$4Eh?$1>p4EUzX3cMaj,S1qL;NOU5r%8)hSdH94#{1_dZ:rfKb5Y)%U;1(Dyf_U>(V?jZJr+J5s2rPl[#THqIxJ-ZW9f"4rK4sWr
j9xfcV*KVjwCx7:oeMwN@^3[MiTBI9Kc>!VcZ7mHN|hDD*;hoMl35ona%{ll,EuX"=SCMzx2XzT!yN*&)ty}SFq{bUaw[*bB5LJ.k~_`1^ynxE))Wu8v92&U.OuB-K3JNiCo$F;:]rct@E_dq[i>f5(O#=kd?Y8AWd0.fq$]"qZq3LDAn[CWyb!DF|j,_+`.JN*xY1C|FKjH6!M/.p01Pb))FWAR!IK#)[`_w`Qw)?ayw=xY%qK#&}++qm`~.W%>pC5p]ZysVicCw#LZvwTeG>[i.qG:,f?_]R4K09a8$b:COqE=I7hTXFH9?GT<`v4u^pnmV+"wx#r<R^ZG?-]IqJcpOKp)[zr]Z(^}9uDxegfCGs7nT<U`jYipsZFk;8*e.m?D/
D1hf^FhZabv}5Rg]&f`Sa1HNrGZ1.?6*-14^s0qRM7(q1JBPe7i+Q=cdHJq<`iE>3!5/0$75FR8:eEj{ySx>rh1Im&H!MYyO+(cyW,dgQds2z$_loxkL]2ae9w5-=f2URMm.g_g4,G`)L89BV,=8V[=oT8e2J!]lH7m8;1Sch]#PuDmRn/mDZ?&_B8Cw&)D=/J>]pph&(Rc;B^9TvN@-O]cMU?%.Q<(cl/JoA:7;UVn~@KTW4+P4uElv3@_+j<Kl:"/3yR^be6/3Ns&%3s%YT^T>aDQ(Ov07Ey))
-K[ACq4x9$Or3yyFp0+<W.vOux8a#cRXSc8D[.vp_s!;{LMjx%Q@W+JF%Ws5us+;s_z;$+xO94Nizw$"YnEahw#5mPW_*VB)a-ySl)cy9c8t0BmJugt_"G6?h*$bdl7Ba;(>YFCi3Fg;3HW+j
aroi3l[8<[!>l
pEka)m5etS)s$+Xsnz"^<saF+Us&dt";h`+4HALaxy&X+&fH*^pO8j-[E)KB)<!<njkA5ZPZ{%Q?6RpEphqayJYl}<4qhxy6WhFq^RjhV;xGy1dYNayYz7kbwt|?vi/f%66U
M9h$)dW5dU<=i0lY
{aNA`%!_O:i2"Ud>0_4a:>Y,5<-+YXkUtGq#R,j8OpwR},^P#HAaB&:Z:tPwoiOAs=^e.q|K-8DhK]_]}dc:XgeE=p]iI*5Jr3$Spix:FvpV2xQkjr`U8T)h^K,@H!C^Y
f;onNU~sBZ5gH-zuQ#WcchZKp/x;UE6^-glcKJlT8@l6|hu
h3&WEQHyzE)?O,CUeo;4Q#9OlVqQ?<]E}"x`N?fHKLu;CXF`vN/>Pk]kFT!sIEr`Z34d!BIj-+xYbcO8wG|?te5oO@=)N`gBCh$/kBiEmF,Nmy^"Zi`^}tw6iqhi5J7`+h59"<~-vB(ao0UFwEY@/*!G~g$.vy~aomokiJf-<lhObmb^}^Td,*_"Qlqqpq.B1MD60&eE"!k<QFB.xslP<
<#I5g.QVJJor|q:m"29Rc1C@*e!Tb21b$bFA&.>gf(A<YI!;aBIiYp]UQ5z/D`ZC6w<N^SM/zEGej6@hcJ^/fO5tabHF;R3!tkj:u/,Y&n%-;XA*M+xVIyb:L,":E^|1NSlL;^R3LGA1.BrTPN,yG5;0P(D6`N79E"Ypfb?2;kkM(A@x4qTYy/QqNMPCC-?V-kHGMyE;gN#Juyu`;p;9rhX+c=@??V>[7.5Up#@r+g^nh<-MZ6?9,d4dXxUy-)8c~mP2PZk*~WiT$aiN=wa<|HwT4JT7yR!4)@>Y?izd=E";/kiozX(swWGUfj;_/P-]7)Nuv1i6!Snnd!)BjS<X)u04_[2&*?L:AtG95XZW9G.ZIkzp>INvogBs.6=`blyrJ@HL2=J9vbic=J{sqXgj.;~6SMbRxfG7n<"^pXpXjU3k7bjC:4`$]+DELw,1>yu[{xU`J=ZAgf|7
Z{L/m]R#Gdye5b,7mpQ}Z-%sEbB%c9IMw*P
HI_8IQM`RwL-)AQzrq6El5FX7x"jlN2_>!HQINj)v^XQCfW"=&gvN$Wd^Z5.DvsSQ~GFqMjIWXU&S/nmtAA_3?@G>Z-*1):Q!OCg9#y%&}%![c,9n!xc_HwR=E0.)NcLMqj5C:m*jW`1%ThJA)T/%iw]w*fh`zLeW
d&:3nV6Yn2Mty3,}eJNiZSPU"1:kt;pA#T&ni@es7`_=74I]Y[l;En:!y8FZE:[
H)5o`pwT)EqiTI9@8SA:A7?kuZ(Z##d!_J"=Q}W,KGD9"R^&^+M.c|$wJiBxK1-qn#B%dFZQm!7$,,0/3aTv#HQ]H9[+6Ksen&($0Qs|JOv+:Ot_lK/^^g&[jx2KKwh^m{Uc^BTPoTF^&c+uuZLloq0Kj{$4d800Yf8EO=*h
6vHlFZNw0OJupdWRg1~QZJ?nNC-,U5HqtMRYdd}S~B"
G@^E,o1Y@124!Y]TZG~C/@PSj
Q3H$sDh3#V&8C[pOHO8"#m^gwS4p/:&,M>Mn"6APoqUlmjJR]5m`a)%K:7yR!G}4-HTl?Ne)_2>Rp]]<d
c+EZg(#X@qG,SLr]P%pTY.u*XOJwW00l5Ub!bnZ_mFxuOL#7iI=FbjPLj>90jCYJ4@yZFEYIt9MR#L8BUgd3QuK?#D>B?iyHm3D5[aIOPcLqh*w^^FnSH5QT^x9aay_Y
#?Uo;hCxU;D#W,J|o48dWAeVNnY);ASW"y13P"(0&B_X)*C4lb]-lQ)[k_h!#cj:9j9&L`xqG*vs2(fKAL1cm~(EKu7o"dYohEk~,YN~*pc&L`RWe~=}BJr"*.["+s-%qy7-T|hP
:=lPy.CEX(1-`84e$$f:x]S/$qEx=d~p:U;SuhUY`_P):h=werZo;j,h_t(Sj?q>#CoSt1LQSS/p@+A#mL^bQ*/0F9dC37.m<pp^/.HTC`Le["
cM^>H4p}/*[,<}kKLOmL]>Jy`ux#BQmP[Y(dt+[mh)NB4jNJvY6["W6~/mEBB^"2wM[rs;rw9RW^Lw[HW,Z.w|y$E5Zt0(4]4x9HL;&j&0k)"x<)RRR_W(FTo
2g${@KN`bptNC/ErFN7((SIM*gi5py92GpC)TB:r#QWv2Y_3lH]8y73Cu4UOabP6/Fg
.le#CNYVRm`ynYk$#?%BHa4U@{siFkt],CeSBD.:LuP>,2[HE`0~E3C(3e=bdWgW.`yR]QH
8g_rx[j`SB%z](;^Jy]2UJs(S{^3W0elr%X8--Q9U<7_Kpc63aV4(q1,B8X9e>O#jcm/M?Z8sf9#^lcw"@o
4*b=9Zf)dcY%R#b#&(<jZQ4"7wRF!2<~FX7u8-0+=Km%u(ONj#"P=cj{3Jhq]HV"H)F}Ty$v4$h%Mam+xp[-?;9(upfGC/WRk>3"3)RwB|OnZ>k^c,AYygEzZ`qYc7ao)l"B"hBINho4!;rqyq/"!i]c]6JJd^a|pMi{KnvbHe"6eua1*6-N;`uVB}d"a)Yn1*)QNk0,9/3[wQ)0wWkbI%Sn+m[siY`!:5
S-CnJe5+,=mDLP_g/dqeaV&FJU&&$Ey5Wi84%A%n;u7
*Gr7`Qjvas
&uYBVTIin$76*tnEbovpW[TS`<y5.
fR"+38iX2Hj|f*q:#O.4
2(PP!jl*g>TGym?]7CmURHAONpjhDMf4^]av=QL/@@g*7,Y9$ou]2<+!;""y-XDs[`mV?y=Ck[|Yhr8+ERHY*DP60DJ+_8,ZjiAC%GS$bK})"KPrR+Q(i2SQ;K.Oy=0?dl@+-LI+2#N4Q@y>)&&Q97rS`=VLOY"/X4|ocO6&d[Rpb(Fs.SU+_1.WJt_<|>JpS9/_"!fjhQDg&nr0sv]%y]*sJ>{Ck7)]D=;6qky<@>DH$)TmB+Z#aS:m6sFf8mwEqF^KUrHH*]<AbJROp%lrT^G)&i*
VZ9ygKwng0a(pIx[%FtY&ZG$:I|Udx/a(e)?pP%;mN|Z0(?p?IP6WX#g=3ob8`Ilc$I&h8/y/Ldxxk+Cu7kKqVpSD*MrHK
jMYq;%w#*wYAUl:60.eS:9CBvy3H2+g*@_iWGK`[h(:9JG?C,_ZeBgaz"u_TR`vRtP3a;R;VL`or`!-1jT<`s12~jT.wNe_3`;ANL9#hqe1LS!B|k,nL8Y6w">/?]DYLeOqa,W#Ho!Zr_6audtP|NhamYr<Gyf!d&#rbwysoIA4.^!12Ep2=

+A/?^w#"J5M;,=$lAXGUnNa5%7a`S/QS?Eo1)KOjb!&d`u0=XD(-9n_R
J.(i6dS:NopM|O&9sO/kZK?,si!M(e}yhCrhuENu<4Z^SV!Jn)mM
`.Qee`I#;T*"&CK*MGV.mFg?[4p*O;O<)7fOKZYa1/(!9NBB2P6DM4v.^S_.%N6rA2ey).o4>KU[gN1qfkFnP*oe5rtk<KU&1gyOg%r97&G{?)6pqs(zqN?{Zj_r"l1E>$"2b
*=7yw!0;8$aCw_!D=K0=:bJ<mvwhQ5H9$xBv[l)-)Zu}5nw8h7t0Rz/D$iA<EMI>wUpwX|jcjbVicB%"R_c<X[?32Len/som2v>QtM9tJgFzv3evghhpy^)$IU1mN4iN?p@>PdEU%ZY%X_),S7gT>?VA]Pr{J+!1V+@@7``&*OD+t#Hkq~;w"]iWgLJ1r_yz&%7vp>Hvk/q0jdQLF`X2x~4BsLZLD9somxH;R9XzEP_MsYE&_ydy/$K0LKgt$z[vZSxXs#u%JtWv/0.-3{wfC^8+O[3OF~7jTr_p10nSAEZmBitE@?Ukv@m!6PA.:.DjH{Ep0u6mFgFOgx:E^bF$Y^_L)Ita2stafU/eJ+XFcC<
[t:lJ/FKq$Oh*]_-+"&d^@Le7r0"7YXaH!ayyVvVcPE#qt4[8#n<7*gcYD/=:Dn.QTdR8"RfM7b+*fd1[wn02=fROT$6lY?OmKCAJD@{1k*%H~4Hpm
<8VCixtmfI!Yrcbl2b}:P.[PRs->%v$]5RRdiw=nKn57oW;m`Hl#3;Uov?VgQ7*"`"Ri]kLtcRyPaXm%DX3sZ<]+Ba5;ROYl4BG0[>>KmhVmQ4-$i9v?ajL$yw_P|.D/`=K9@UkL)Kbx}2)beC.$Z4yt^G-G
Ya*Kg}P:%lbp
sczAMS+9<C30"mD!V93&D0#89hMq)S=Ozg"C2$Y51jvV7wu+2(ew1EGg
4U&p+rex=!QyT=H"+RIQoa<#P!sFITYS:8Z#UC1"=zD%x3W!dUmG30+!gH!8<I*om2g7Ai`sVEX7@8LkLLp`p1
VKihXgFd=_HBe!2ZxP-o+g5KjgwFrel]BYmBc/v<NL(`}pLlj#A!_0:MR2nj()1W+v;<<$yWfrCY`=<D?@e?UeZAT/CVCkW7iR0l1?GdXh0a[hBm;gXQt?mk3ndgkTg
Mn?F3D$e0sam*CEd[u>$P&av.nt;T@O7;=jh.7Hb<(Y;<6OU)d_^{M1v8DmFhD:ZaQ9vcEEi>iLxDpf$FkN++EPZLov9
d9tL5-Zn5&)YKUQ,ig;@5@-e"zr<%PAa/pUKmVF~sXWEnS6GDo;4VtsysgC8E{:H?naRmcZknEL6,~i9QT()kA0-m:49UjdBFlU0VP^h7-d|9)BT^k+m;T@=g0U1*;+1)<FQO
t-LrR!4}r6l;jwH;3J0cszQ<[E-rwV:"-y-TTXTOHOHVv4`tUDP0m<8uNlP4rv8[+4
Tc4[KD.7C)G+DOb1hR8bpu0PJ(;19TQ-7-%DuuN)*2:`kkZN[0K!eBM`&5x+0j,y5hUBPgEu+Zm?mrbj,)wD2-W]]l75dJ$HGO_tAgne~RlrZ<"I1`|m"3x5/(IsjvH]%0QbJ[:qDMO4trcO!*pfb1F^ADJ+M"~ME`PpI)L)+h-ar!q2mwqm<v*vC@X]xrY>tj;bx-U-cTl$uCnX1o$4y7inE2RcW7lYJJ.<5Lt=~+t9"_"KD!kBIVM){%YZv0fatqRG~?:wwS7UJ]VBlF|=hq$I}9VBJ4sr69=;?RUJD8dhO$^Ym4x.jRwjB:Rm)"q-~gmem-NQSqFU`CEFK0f]}"/#OSTE@[TXin%f{]Tnc*GspgGub##RftFG>JmJ,)sI10fk+I5_.
uo$hN.)k[sX^EErH7$7neAet[X)(641Q!U]p5FEiC17(Yg+?Axx%*3xetr+rQw,Fd3y*gpWb&2J](
qz(c9O0ShxqPEdOr"StvAsJ%8$?os
PM:Q9=a;mT--0F3mPI@AM*~e;sE+Nj=1J?X+6`rb6!.KED[4#o]Sik|N1
Q:1A8lHC[i["<&b#[N/+BE@%9lZ(F91:c,z&3QjoG*.X=M^<KK"ICq{6RZS5Ji*<%%C!ES[Ps+o5*S-BZf#QubJ`L4/Qf+KP
&e90Hv%fvg6*YQS!21*i<xp_3@NX*e(4]&R^7Jf$MY52^)OY^P<r?G81tpPY)*iF6OPr<WDV^rA603z!;Ad/4uC@9NJh9.X$S9k=6en~hd4e(JLfOPq]s?S~v25(NAi[Nx;sa"S4NP>Q5#N")x1b3|yHHekZy+tyw;e`0Qe>j@"!_.6#E]s022^[ZYCo"7<he9Q>*qH]av@Xl(;$4mprA;W=".5j)C:lv>oj@Y97kM[(5HAA;pN313i~>&$X[pG<)df!$ev@07p`#X`B@:[AAKa,i
k>rI/Uf=HRIlqvw4x38>2F&-nd=3w:_0hKRhcnh]g^piGfWh[/:@ujt|gxHAuu.n*;l<4vl&tMi)BYbDW<fK%e2,pqj[e^D6Gj6@7xmxPB%&Pi/;*gfVox=p`JR<hoI$0/?(H3kl1
Y}8/Jx?
=_1.I_9%]7M+G?i!e8=TuSmP+f8ucv3`Be8XC*>9VH8}^`_wRhbPA]v~5jF1/iS)",(6qU^w?ABk[1!:9&%Q0ktX)w;@I,l}jYOXZ%`7;(xs"<K-=h4br-a{=MBCuvIGj-w&5ll$^[OAEN87#0&t!%Ek^f>gt06;yf3xDFn}6VEiv/s;E
w.v]@7kdL^"D=Pw+!?hmd(Bvbac~x[OJq&`eu4m*M(!TcFu&y#MBJT0}k<bwuYu|n_6Isp"?k#cAU)0Z&J2/>)9j_-0H!AUI7D5^K6`rcTmtof4&7zW@hC3E!M!UdZ?LNdE~w4bqQlfty4S$:^7ca^I~t1dNJ0!>RiW45Mt#CKYWq+
GD5;9FMH`:zk[DIyC&Z4W29.,Ws3PHJEo+-pcmtxCDbZZl!S,m8V.VqA)qr?6_2_/2zaj`9:n1F8)T5dep9#c">Kn50SLS{4(3OEicEU$JP"yB-WU[|?3!KYG@DY~-Tr;M.l3q
$A^~I^VfOV1>(w_t>dhSP?izNc*eufX9"Q@DV
Ti
bE
G#gHrlPgZ9Inm?C1!w;b]hD{osST+S2rbWT]ywkG=!+Ci!72%|LSQ&D?nd5?Ur*h1nM@rqA5,grow<aMJz/v9U]pX:ImBOXVs{r{1vdZu/1fu8wq8Tk^GmR[3$bG)&>#iDa&%pvJ4s4k/9<"#xagJm>`t%_do
Ja),g_rI>S>b_qT3ZyJ>Y7kFD*H7l/GY7$JK(6)w.Ud9QFhh@6O"l2bFAZ7*L[v}oL-9cwHoP71_!+M:7piX62S!.FL>@3!"j{j:[kv
esxn$`DrMd=^^Z<3^+7-ft@*
e`*BHaznMt=y;,aV,vO2@lk!(K
cbCyXso)v[42
M/]X~M{4q9~w*s:6%s@joLJE3B?ZEw/yAPaagw8sro(Sis@yD+x7Q,M;T6>pKydps>3s$*/#|^$x/h#voobs,aM"1LWES+TY,TzAR<wM&$1V,=~q,"g?w,Sj>Lz4ATGTd/*F`C763uW4~nst+ZEH%ig^xkWka=W[Uyvy#jR;|<qs;UZX`&}yhq[Y$Wa=Jvzn-c)JCgPbC]bksGjr~$F,,EncAIvU
noK`;EV^y/R56e6[x_$e&1
H/v8V>[*"1}Lv)e]6x{u-nWm,T!]TYr
bUQ46FsB+7[U&[84Y^W1;mR3GwVr5n;OJG
$6e$8c
NW~OHa^c%^X3r^oU|NY6]Dgck9bT,6|ZY3pxY2b78.`@Q#X4J=X0mP..oQsyBlB6|!=(pKeEH7J?DIlS=F^/6goeZ4A
kba$
tm)U1/M3s|8OT8xZeDf[L2w.(j^b)5>SD-VMCEkRX%yb^UetxZ6];?`*vl_>*qgn,4X;LWSSiEXa)"3^4Lc/XYd#/OpfS+@]sCvs/^_)[%9ol"UAc]_vnMLW>0td8p
ld
t1lIuztB&CYD5OA#TpC}fO`O8ycxWa
ht<=Lm(IW^R3.(|P
2n_Ek@N+OZ9/v:Jh-Ew2u!4{tQyuudnfbNk?oE8cbb,TPI2_Ehvvo;N>tyg*i/wt5OZDNQyew@DctqftF}#,lUBc;Jidt0]m;}6?+aYtDhPD`R+znW:?r<`f0y,4:?l3[b%zoe7|/-qr2T"%hB#m/E*$8V]xVEW$4K22yny.y;g{.O7w;No{f>j3r<!G(Qx+c*P)X6#n4<QmU6nAgUL{(J8ysc^.5vfYc0H<t;t4Uan9kyPP[v0?@w/.nK.H9u!y*%4nBYRhLVBlyC/#:$WnaWBT:lo/uw1:(L[iL*@hmV!-x:U/L{m/L`8<^.c[%^:BNN())}eXcSiBhoMw%P!K
5r]v_ElG_C[yg)3$
oT-cVC`c,g."8;h}BIc5Hc<7?rF>XmO(:J"79!(_4LSy6{pCm$?Jl+@If{L"L2D^1kR2<xqiW3g:Oi[fZ6,V;1tqf%]
>wH"H0J43$JBY|_;$Z./[a31v)JQKSJis>knJ{"Iu[mW&-,>HK,5[Rd=*|oaL:vrngZZwBbYEg.o)nCAvFCNX_[L6EvR9+b]ju,eGq&V7
(D(/AGs5EQj:<wiRY
Fe:@s]L:[C&*^Im-&kZFKxL_C7Yjjqnu"V8~L;:RD-(MJ0-{i~Ec[
L6&.45ov,i.=nF2XCkd1Sr]FV~(=34jY"[O18B"xA0oML0UeD"W/HG7}veL[+L]>9F`*NVDLD*l{ivagoCf&WOF"?U/;[cf)c4PnH5>H#y:Z:yYUaE!q@3uT,mRZI9WYS2`EK7M*.CtC(ACu!3!JL.=@Qp<*iL/""|sd@"Who
R{[EgIbS]wZ%x`FSqV3i(@l!qYJnuFGC<.!^<d>
y3X|1DgEgq/ovxF9i"Y`asL&nKS|hh]"Re],ie!=e~LsPP<bJLC{.,5|@}S[fA.YN{&BnG&Wf~Je
XP:n{&!+D]yX|0aceW5N}3S[E/"L(TFT5..W.-Q"HC@BbmB<RQPAerP7o@]xhQs@n#"w-;
w1Yq.xL2L`Sa5rCY[Hg_iO,CphNuSVnr-P5G9x5.NM^w>J!#"]v2)IDJDJbyY.x(BzZ(:z]-^hZ]PR?BDZ
w!Wq1-fxwuuMdn}O,Weqgu<MleN0b9R3S5Ryx$fc3OzoEypj=c~0-L_OdyP
`d.4W)PAZ^EbmZf&BSD>[)/,Q*b:7q8dg3XB&<O#?phxg?.8-BvD[J6g/N/%W
L,;BI*JhT%k_2t_Web:PptK16j9*
ds.+R-L+<#8K&riQ!k@U/>nzd?_a04J13_33*8^ygmhJ(faaBw,rIb]]=&^H9*K)41)TC[;#?
W$m>"
xI.l,23E[J<5U+[nVT2kpcL26<UoPS5ng%;m:i;s*:d(@2&vY#tl+C8];{>A%S7
f]it^eQt8;-Bq&;eXtg%pQjOG4BBalKo0Kv).?:`VDKu!~Z(N=shQpkI/P6Tlo;7?3xHjLR8;3[n
Ll%
(),9%&;*G!UeqVp#Gb|#UJ~;}8N;aF0.S0q5./BDH%K:SRB"1pP?yd,wW8$UgQ6R1+>HW-TfYapvI*RDb$^Aj)WDkHVN)KN12KYB`=gL7CLd_SF)74{UCfGhg-4&R=?-2ddPg^h%J+7:eeV5yNg;d%D=1t)Ay-VKV:SSZ991rwm$?%~D|Cyw?^fU}U!Q!2Tx~"!whtN8>Y*T9U
Wlv2LV,|,6$GY_;-w@:[H>wc%.a*2ZP1+L`u9rTv](Y8S48Alj;xvYb%/>,aY*:
>um)tJ):nVNx
[`q3dUtjbS8M>fS"zOC($O<&v#])Vsf61jzuU)]A
0Za$`V?t"7Umarp7X61=1ARerq7@%N^gk{LiT;.|pSW,iuC@j377fxvAaTA}m~y;97<P$_^3w*1R!(J~ytssi5I|tFHRx#d6x4=u
:^nN/9lkcQz0YCw*ej^d:
g,|U<C6]a-7SrL@b#c$YvQJJ)N}ZN`bouY[l,29aMpw$]C!W]
RZ%(6jN^;Vh&z3l0DOnSfD!.dsc`%TDH[ASbf)iA^yZ8$FS<[s,u^M,cen#s8f
DO6SLZ_h4IV)1ztY@,YmI=tHiaDiH?P@T=EY,VQb
^g;[0`BJ[yt"T1lT^vKbgCTE*s!P[0sYh6SM>n7v;0EC*]^ol]7c92pw]NFa5yt*7u4nM/Qf2%[w0i/n(HVkn]5G?vklB+}nB)6u|kr!Kv<(woa7-g8Mr0Z/Z[grLy0bN>A@y[L%>d$N%vY.@![6;TrLo=qqwrkT}6D,=t3In<qgL@&U#lW5vm8J5)0:WH/_(YV-0h5GSlX6M&ups/0r~"7`N?_t(I4g`.!7f/qjZ0M>mM<I8@yd*UAOn&bd<ZKOP*S*gw(cC
Py5nw_-^|r;jfDK[Z4UElbFT/8Lo#+zACHQ-@rlxsvm^hb]E7
Q;74E@I14In+|GH_FA$EW0[=cdm)(b`Zm1`#a`39>o!d`$_;BC`7<70u|:&ihBm)*>5P|
6w.FSr~;m6`=ncFv:R"fvKEm1J=`][m9iN}1J5O
@dlj^u"M%PnbSxE7D,GMzlioo5pOwh3;q.Msl[9>9M#6xsG?];*6%/hIP4ejFm7P?@%VecHf9@s<e/:G4Q{xehHK4a#k"Fn;s6BD0J4J3-|Gp:-8+nX$QA<TCd?N_0hA!tV?:Fr&54,[*GxeDsGLaxqUP`4X!)cnZw:SS4q+KO:om+hi7s&+i3W:sA0-n8D0DJtJ|vk*?VcE$a*2@9!An*$2e%GX4c}q}&VAX9]e9Nv,4Lt@M]4*zsAIB>}-+9Ag[e<1:RgG<>X_a/?7Idw;YDoI),TY7AT`Mv7N
V1jujXMU35XeuQ1;Q^e5%`H(pL,}6w/N
O^ss/iBrS,=y3iuYdv7@2>.9VXavS,<l*8b>5rEny<&i@?!L$P;0ynf2*?ih.k:P=-a15y4KEVO?gJp!KPO&4>4*N=S5h4D_8,E4%yZx$7.]Q/i]T(z)0X8I6D7ByM3G#v1Cg?$C&,dbJs"7dktRD57`rW`bWM~Etxp$`P%E:@9wF32;umgo/016cC~1Cg-?C78uR^G=t4;y>i{j9m5kB1K`~sv?[JT
EAb6gr`G~*ktXmaem#Hu!][/kQ3)=F_3E1c>dd^X%("R1TOmUtrm(+%X83/XjM+84W~-,[tW0E3#A7x)2+LyShT#nJ#gM@spR
-
U?"$wES)|>zvUJ9+m?2l0g$?RKdxTy?1wG-2p%qBc;Z7o5F[QV#K=,e!Zs%5~GWexO(f3Bx$|l&BahPey>?8-Mh!rM{SJUjio.*4SX7o/[a"1$R*}RqeQXN7r3X35XSR
Psi$<vfq[rinNv`_`&S5"I6lV`!&vEDlfxa,<b:{Gy4(7X8"E[,j(;gX@h9eGe"SR>)~Km&Q?0yQGPR$#qkKdt
"Rz.Dck7CiU.01u:4>2
Z.p&TdJ$<+N
[u4gz
+d"8SvFo#:4>7,>QA5+"Y&o_p!4%Ju|GN0H!|O)qw%fN`6hIZVFN$(}dSj_59Pb$24URAqRB6Cif}2Y"c8|+"+Gb?7@M&r(?gniRX@~K`Ek2Av,xjbgt&E4y`n*yD9vy%HCDgcJW2j{M~bIHAJrm|P<b7RB@Rp#C:+{Q[DO*1[$sX>_PSq{b4/{@3!@K2xZ#DLP>YS-v<6)B#Wx%4dS$_l1vX2AL},ZKLOh;{q3:fXx.|&]Q`8O4X
[,{HaP&,Y$,yORR^U8^jzD7W1[Kq!0ou4$L(0&=B,)[pj&&5-Lea(,r(qd}9+qOM|xA:f
T[nZa7
X=S@mbWpyK.@-fj]H@sG-"6wLT32uES1P%/=O>hC=k0A`fupS=*Zt`2Q95$^
_ak>Ew2UcSD[BX5MBnuneIx.wpQ.U#~jFhWNP#X9`o6@
(6AZq,DGm*blL=VH!1()LPB{MtndynaByfD!Lpn#ymsl^m8a4wK=^GfIgXLS@WQ(DLj#<DPSGn-tk(2Ziwitsu2b=C,Aiwb00SCc*RJkhZXkk@5-5s:i3aM?=lw]"^tRPSK:gweJa5v_l(YZKzVxHX.Qnqsra>I:vB>V?3d<DV=Nr@@dG)HQD$j)I#BrkD%Yx@6AGCR#b^UyOAPPo=G#7EgAfmwR46x!G)YC9#45`mjtue5N;WW$2Oi6qey7P9/pXH#b4YkagrOC6&eB2c&~M`8,md*T)roat5n?=M"&i%8wS/g5Qnu}[gJvKA(yDl[o7_&,N%Kq<2:eV4-;G`lQj&F)p0ao3px2w<2Z.3H"r%^_Wg0ManI6rr*9RaS4w7S>oLL2g5c>Cd0D+k1K`BVaF1P*A{(h.Gu[:>2Z6[SK$Hn^FMd!Io!iJs$Gi%NC(7pPDwn#$Kftht?e5UtG1X;}LZOO,zPB!ms^2G9`>v4t.c;c#meS:dv01%gl=|d^8
d<PxSrlZe*s;1_P|66Pb:@"m#1iSj%kW8Lq,-gPjJ+(IHh6sZY7N*4$butkcPNwP0/"`u86vK+>$l`b]:g#kf]2V@9?(N)LXNNuzC.d2m:?/t;#~WA]KH-wZP^;Z),9+[m<Hk:=Z*k[vS`aWmWWBjBTI8"R5SnPnwd)2.epz#tS[dt;u4y#Pqv"CU-9z"INd8]=CG8.%1/-N8&=z:4&ZyxGU+U[Ot_D-?E_3<dnbrYTR2,8r"RKY@RA+2,UNdQ;c:kJ+2<Nmd`hV36]2T|3[^x:jX]#L!HuArYucXCu_7muD6b#%wI(L"tN9f]Z7tTeP.2rUS@yA&<aTpqkhre-AnoD_0jBQE:.a1&[II-_qbX4vB:<?%;me^PVqLy)]uo1^qr8a5_#Dk5,~Pt(7!$C5eIm%F+jTnfnx24t
#tu+dI!ge"2W**cwwUd,(3[|::$uhtAwD[Xd">"wY4oADllg3~R35aY@E:HawXiU8#1`!=-KbZ:g#3rfG]q0^O##[j,@_R5;3!,;@ovC^Yv#`NdZO_pd3VT_@fW;Bv$n:E$h#9(m5nclZ=-]d`CFfns;#(%pyH/z2t
&f/+fP`.(19w"i=NM%%tN$7e!>!J]G2B.P|oW6v#<FHTR+ACf3P)0hG?,x"X,cFmUPK*g*BPK)y"$<ID-1e8tw8lcgAp8yoX>gsK>*S(C>E"c2zl;;0OfslOm5rxL$"23w!!p6:MowL.T9Y=bJhiTqvQ}SqDG`vt;V_/+Vb!e
[
Wf_xWaZlp1$5gq=/eM~V3V39TWAg](C_rGyO
n1DlR3JCj?gY5I?B0xQo[I617Tq#_<[Xb$teeh>p;TH)%]#xg_
!E)DJA2>{dHD]/quBr~
Zws`RNZq<>pP7i/En++9?#Oj00h];il0%e^4eTbF"2.fn2?[%Bdm":+Dz6ODTajgiC`8+=m,P8=d9mTWaV^chX2N!S-/YF3q4&y-3>|Ot?c2i!e5QIkdwo|"i$[..1)<]rJDrSMsp!cs9@b3`!D6H/"m+$!"7>;gFpteRd*"xlx>-9?NK7Q;xPL9_bF#^1P
S5>v|%<H_WX@bj}owQ3w3lW;a(?JH&hNI7]k@,ScLAA;*:0ZDXW(D#q4.Q}+nJs,t4gV%q"/bBvlmy^IE*6j("0k4vk%,E%Ao(0Fz9CCzK^"LHNl:I7!j/`T#Lib+r.qZ$0,J<~g;70f(EujjEE^fJqdbu`x*rS
@5RLfNNbTpE_GqlE0$lUK3%%<$d,c22#lO(+~XWV>oe#A[LjW2=sD$?)/hg0/0fZKP+bI4bKcUS]TH<NlPvWA:i(d?"H_/}xc3&*I1x!=^LV|9b>}-kEFd)T[kj!-8T.V<-n;87dD7-Q-OUc"<P2SqH"PY#Rc?$G,cRORhKr0ys_#TV<GJ}cn;c<H4S6_
I_!@Z`eNx=f8ma{k_3",kGzKtE5Y"xa>`AK)nY7*:
.K+E-6+81:ZZ%2r@5.!fqf%E1kp.DF?t{M(c<3aZ1vfMF69($awF1
=*j:U.1L$3?.(>y?_286?_4Ukd<QiZXL,PQ7!6LP~lfmb5T?`Y5?;0ae}r8A2qDi;;*nLB]iH?KOw49D{f!+7G{N9[?mIGu^9xE:35z&sblqWmye]hA-7@J@[B
i%U^1(.RjdNcEcI#`wWv0L?:ZrvIAznQ-r7qxo.Odr<iesgt7YnS,Gt8rAA@ipk~]Gb!0WK6ss3nC7u4@w62Srk[+V5R=0y//k^vpOh3GsLNPLVL/;:FmD+<RO$(`Ufb8Te4Q!plvIGaY>9d@|A%HU:D"<^t_2lLwiKWRF
9b1eDE+0L0*^_pjnaT+ZZA!.cL-*-={,OkU"3wVhbyxHLplcXr77dahN1hD`#-v=nOYnM"X$q>{d_sSN}TU*kiN,@%,9SoBJ*[1<TqE-[qSE42J+uuIuAVcgJM6U<5pQ_YNl7ML<Z1u`Qf0??6.xrN7PlhHe_IiVV8W[_FF$wKA9fo|;U<?5O*{6@P-N/iUcN,D!xI)$~u];jdlZrN>[b78Xd3UKS!CMvUAD)gHg5PBrO6pF`O@yQOY-X9h!5dh[+Q*GE[qK3.U1pBwa8a"@V)`/SbA?|>!LgR/v}+%figQf#Nl*KxwX5.EqJ1n?1t:6^@Vql&29^S4`#vxP=]0[7pL,|aD]c"V&B*._OfT5ac3QXiokSHo`c%aXDsDA>+]r]V{rR!z2SmMU}lTJsS/"Cy;BAfhm01@>h`lPfd4h~unK<CmYCQqG]PwZjIy_5G&C~V4%*RCHIwdn{C+`y^K=y/|Jl=t:1to>`Cw<Uu?%<a"fc&2qywWZh6g1<2`8Q="2Bt.LtYOyL?"
t+iNDr3Pp:f6FK$K<BSnmKcX:[;X{Lm"
pR.EIb0b9g.9"s[Ji~q)G
>tbdVrS
rkX7Y2k!8Hgbq7M43bQ726GD[`Hd_A@v#*geCB8q+*P
3bBM"@2`b?qM6aObkCI^*%Zp5l"IQBDNLa/:x!q/-*l%]&;mgp`sIgfrg^
s$]L52T(f5&Xxiitl,2rqx$C%64m9pv4yqc%T3Dd3J!eCteEalApXTGE$".E>J3.L*x#xyN)EGT(K=K4j3Z9A=oZ{Cw*vw6pH=qxikByoJG,G%fyW6s3`%*q@i[rtgg5}qX0&Xryps)_QF7Q<
{4}P{_2ZU6DrzIB0PK*=a>$7F94Q0g[VW`9#Z0n6#rk&@&I=z#]1x*Za;bTjNXK8+hJr?0.6/!K.IkM:;I[2-7[$Tige
`{AC)L>{4_T6y:fa>6?8?I>sJOA3@iWPV,;WZM
qX&
_okEJZsKE8>(eQ5Y=X5PHOYoUm|VjCs)Er|6IY.]lSVL)Rh@58aScaFY-ni?yfGX#XI=$+e^+EpQnjBa!4Mw/c88[>g5A0.nQZVpQ+=4/*>B@P|#=`exk%X&%,B^]L
-i7syu?!`J`{8/M
@g>fxg?ELrY&P6li1ZX-cvq!SQBtb-5sWPkHhq+-%gQ9CZ#6m0xlPE2;K
UwnS:}wACUj}<.yrLOOFUB8Kx"XA`
G&d_`[V.DIx=W>8Z&JXA^y`o]^)5c~naSn`NFVG,xJ%tOV>wL1<wMGf[Z%2UV}X~QRmf6D9-*K3w7M/.6jQ~jr&?=NN&4ZMO7|gf@>=W@?Q1f(U;n2D8+Ek)"A35&_OMOFB!^V&RtWuKh<G_%ml.RBhY25vo-LZJl0mi4(N*j[#GXuPV0_BpRC8K+xj/"(
L#;.^E^LaMTc9s]cw"R=^2iN}qC#91A:S;~.mA)00j-&J=UyoxUN:2Oe*q@2p@L@]:Hak"vL(2GM%+fZU5i*]QT*)7pI+0*c)hX1pT-tz5:%u%(1YgS-02!@hNp*z^,Kb&-fu7Rc18*)tHC4G2o)2F_SVb/eJ$[7|Cmnd2B-v_Y5>h{bIRw]B4cU}#gt<A`@;<%mvW!@:Y1+<PQ4Rc&(G]7+")gtnW=<jXKaP9]Z,xA/TGSOy]?6_)
Fc4
"-Xb;xRs_bASf&%dXl.tM"-BDnZNm_`x,a^ERI1q&ga^`7MkAObJ]?l-&{Num}l#f5fRxltSd)`4_GjOi~=3;1bq7ldW+m)v.nWZ<P&adpTy8Pl63ex!@dsQX[u3[XYkTIRo-`1y>K:,l>!$
Z
6Y4G@CwVtDe8E:w`M"24#u|)a`j8,P.iIso>o:W,5iVFLj.3u6vPY1O!5jZX%Q#[}U.X9:KZL5o5^0`FN6C>K-AZ<FWGL#GK(cNHg/8X&X=N/!~s)?B:6$;8IMv&&;Ilr%I<
P-jMf2dzP^5o7?5C(ZqE>f8%v"FNPYC[Pov
),H6g^HCvGF_"!wC1w$D"Fy@g9_INrp
"nIYTZaWMv.GQ#;PSX=H.7io[}$,Dq!/<"oI#@Dhlo=/8`xxR9I5W+#kt;8}/=U++ulODFn!3"c7d{fo[>6#-MC5F$(_-{(xN0;Kp4`FAwq5wy-FJ0-@$FcRLw8#nhRmgk;KxGTO3Ym,#R7X5<4eVPK^5X.lIw`kBdP}".t?ySL9t`y7!18HX<Q/Fv&[mFE>B&1(4YT?c`qh$y?~"pJb5
FP;>&Lx5?|H6tfOW%CP?((;wKoo9!z+.pV?_SJTS@~Y)!]d0M`A(_[e|5o@TI]FQnW<xiK:xSWO2[-HV`2UFnQ8UA<[hn);fthgE=lji!ICQgOjF5P]/f/&T"PHu=F22MB^}YN"]jT%u^CF~3|`L"<<MK`O[f;uRMWk26/M*69E9NS@T9Iv4[`dNnQCX9!c^%qoR:k8GP2Z/,s4"XLaR3=z"vh:w"R+*IAV0gqX.iWUjk]>6EB)CI*fv82xza[`m/pY*pJc$oIS]bL
t_/P50H>q3"+vm3Hrqj^JJFh~3y83s{d)W}gUbhF^r@k@J3hAv$,h_Yl}&ewLWfy2)>Q^pDhAMIZ`/xGu."j~ly);/=&VRm228TK9oeoN9VP^;vQo-k&u+oV,g#@4r?KKw?+G_mE(kDKPTJ+negA~8TUr@xQu4wF4$4Yt5jny+B=_^c222m[v0">z"qF`=xA02Fs.yG#&ami>T
cy$SVsFH<?7L[xR=KHUT`9l|.<l4bjv^b&a?kP>gXiJe"85?;PB(1!YgZ8IVGR;Ev7WZjOh$e[fVpJVvQI26va-Ff+&lW)_Lw7%G
V[%>`#v!j`1eXn+)w##8
yT(;:4N=F%Pz?k:}oC5djh7vVq[cO|[0nZf14~T:TZo-C6yFT#4)g&$4[iv
FP<+oLcYYT/S>f4b;K:Osq$AN3i=Gt,:i]D:?2fti.h`]H/g9K>//qJ_)?`AcWY6X=wO^m0CW__S<?LzW#TQ9Eto"$vDQ[=oP:(K)KPHp]?R1xXYj2/wKd^{8)=G)rR"Pe%&2N(%:N-|yxe;`>>l!;RzbK1</bSi+&
XLr8A[U.
#]Ee[-^g.rgy+z#NL}iaU+(u;5DG?>A0<s"B]|-{T{,DD^*UmSAekvNq.RN`C*Nh*quwQWT%R-:0
XpT;aAc@|adw"+-X0WiZABGE{o/R!2r
g0HhXqPU&AA2ayH7=#3pq>F;11w$nHt5?ka@L%>yop#&IYo&RSlsYe@5OD5[UW8Z8swVy-mD1:jlYh{]{n!^WW@gGY:"-V03Ya<Eng~dcEt4ui?X#HUisOf7>DT"7*Y-n2l$JtslSqKK|,49K6#E0ZI@Nx(&"
wU1=dUw5a)/T}lE`DgnVN>*!epYgc;r@J$
7*N0t!=|=^ku-<E^OiQf.rs3&>/=rdT_3!d}PHdv>`_"*u;).jG3XX$R
0O$HaT0q#q_fsT-yKle&&22s6eR[sCFn|_4$U5+8ubXv^#[i<(s8d?z0
]>0x(I%>Ww-r@[ej%<oE>bKvJ/P}R>?^Di<:_jWOnnf|:fyiVol[TPW!3ueO@L(#o9T>
NJvFR.N[c7Fyi2[!ue"nX8{o]YK<JFX4YwrmDa1gR+k(5/7g?OgI?P0I
"N@)=!lSq|E%F@K_EJVI2zMy4O9mftb,PyGE37
36R@/)z+,g/,di,%Qqv%2I+#OmMJ@1K0&?obr1QKSIqVydkH|OmENx<dhW`){LLWaRT=;-q1ILnYSc2e*@}"Y_m9t%,<H6>u>:J+#uWTP5`P-r8RD1RFB8P#X/l-%gBv`<7.jr)*lIpOi[$8P:HSaln(USEute9[R5+rO.-jJPEG<D*Qx7"n6+6GK%]vHd<$u@r)*-B5,B4a5qq8X@>IV_H
8rGn79.HIJ_K"y-P6
hCo.L^a?)[`,I`I;;#.mm$et3Ng^<nz#xZ_``x|Z:i6ZuiI03<h=_3^Rz>3!_N
n,>FT5W2$!G=`M-PSwNFI_5U<-3E)07TRTGJy+d/k10ND&tMaO+|pdk#00:od~V_3by-Es@A>LwiCg7I,N<2qb,;6I%t;,v0,7Y(Cegx2gI#bgn<9fY3!F$I5OgI^N>Vn=1joxUgVCG9]@Z7sa2#
A
7Vm5Xa0C,eWP^v7-+n-_2WlI
3!Fl$2JN`$9c6xy(m7`5P-X5=st0-3pf[:5-)u$[?g;TfYv~Bd@s>(-|P45L*c--:k=*f0Ue=YeoR)CHY,ZLo>&*gaN~i>e/Bjx:Tym|fu:[q%nm@GVfAo::i9uj1gh@_i?w>~>T;b*!Dv00
X2~^.u+yq0DV4HV!AyRC(D:Ci**j]q#&_1ahrb{l:m)f.]3:o#|n6*fe3GEV?6/J)h<f8qxG)L2awT)?.G1hNmyKz/jl~(=RwDyI
/-2t@;ey6auJY{(sH_*l&^TS3GxofYaE,@@2TexrO_2~`j8Bg=u.pdnD:xte#LR;o&c1G6M`S{<95o]y;_:g5COn6J)?4%M/+T"FQ{rtA5A@Apn8A3WySD%yF/AGyPg;$xdq[TgE?}(!X([Z0N2r>{ok"djoiKvbQ9%H%J:;xSdWF+Iij.s}3"p
QK8xa;h|Xiq6c%7tm
+"D{U_E`U=6{2#W{6N(c;bOp$HdE;w?
=o.}AE!sEaWeDpLYMA%k6~Sih-<t>9P1!hd@8c:{s<Ewn,aPXnc$$Jdp.!.0:j7#-8Rv]p:k5`?50-w&Z!F^mCE:XS1s8=[HqdD8$:]7:8bf]C:;+"f;J~1H!EwS-}
SQ
>O;o4Bvy?B?B@]<SP(X2J8,#$s^tBJ[+bnx.m4NvU;q3<V75?@lS0w3zCWUD1Q5m%Ultu-TM.Xf8Ai:OI4,I`6=:r#Ep[0jD+B-aj!dokkgQ:HJ)+%V"Z-c&9zS}_se~&:gaMITK+r08gW&"%7C^@PXVK>@X$/)r&u.;#a,T4UB/"p0L=1?jkP"x3QlcL2T>o<lE
hA`;(d=G9"b(pkH97Gkk?A%d)uF@A0a!_HY!d.$y;9pD$?iOr2:^-,{KhWir+Nxa@.l0n<_@oWd6jx}`+:EBC*QcnoB"V
ReU(T!7%c8;KGg_`r6lQk6?9fj/,5Ca[]&dvUYbMJ[7F#-VV|9GwIWn#O2AS^i=#Zf.itCfq%]<ETRogJtbI977P6g4_@ud0!p
S~Gg*Ky$LSd0:N!s-9T~B8#F(tBy1u"`>k
1xO;RZ2
aa1q$AX^Vv&h~65_U+"*D(Q!edH1*Gn5jqI_:Swb4s)$J5p]C"c$M1&.Y2ZXWSSo:UTelVPN<Dr4q><"H;A]qGU/}I4hwDs38C?EUxNb9I2/"o<5/%cg*/NwThlnBEJ.JgmVC+Y$`P(i"U"!%-gG]N<>&uf6Jt$9YI3djr-V[`D6Ad[;z=m3"ohvTJ"t
A/4MfMfwQ/lyBlrWRNwfeJApp3r31{bYgX]EJp6+d~n!%SUvJ5o3;~<"2Q9+gG]&.sCz;W9$V1*ZVH&N"KeTBW3@_Sv#A:>hG5^:"#/CJ6p1$t45A4_.ZO1-i$VY[<+CKD0aB^Wk1W1C/gV/pEqhc;DpbNGtwq?i=9:$t%FhL"+wb?7iZ)19>Xkk8|I,1Xq=<e_.6dF~F-vl=?IsK28.%Ki3R@Xi?COq6FNF]F.B-[cD_af|ejO^n7TC+T(DP].[3,k=Uoerwj0oD)9f,8.|p+f=f]njaC;;Us8HWLO-FT0^RKl@@7>Z*OA!_QaLqh9z@s!:TnC[Yx.X-*ja-<@R,@a
Pq>fi:Q#gy9MAB.KP^]7fyO!s=
k-C-D@ycb.-6G>(atUJ<j&RPNm#)-;EG-3;&bu*D"dKb*+Ed^NM7lbtTdUw`bmQ>LkY7Td([0]zv>7T]4ZZN0MA2
=UeaJ39Ea4uMy|I)[dy:+4x?c$T%au-%X0>bC(iO6DT)c#GB+sm3,+;hnG:Z*U&s%|iAHIwn:,D9Vt=/5+mmsL/`/nqjeQ[FfIO4L|JXOu)45%j?%$
7bZ$~aBW810tUvVlN^7=w=8,LmO)!dl8p2w_T;WSogS2gn8oCF{j%qr(}17>s%=GL
]7dI+M:x.T@H=Pd,8Wt
C6&h*<8"]F1j$k>"M&g$8bA?t@lQ3Lz
70`DS(@%BKro.;V>{UVAH:[rd?0?"/A?HSKcL!#CLe$-^L}H^tndVil#N>?"&b_KAmt)
HB^tJ*>lbLc^JmA=h;qmj{JV&CoFP^]N11pi+75@@jFS<b@paMImAYSIlH&FZQ4/S</-&W9geWhq8%@oCQe4-k_;iwQU%pu)DJ?m@>nu49:V&I&oP2s-1CW09+K.d<PLfFK3BVUhP^7?k/iLl.C,*zq,!(m%3UsXBgK^5QfA(NFlW=)?D.5;dnCp^@>BSRZvh}&#w$I(7_B:>x2Eq<8>9l1t;lCEN,e"FaD}.jI80<SiBt77Hg)fIQ-5iOkW%/&C!Vqy7juYX&U/6uN_D{s6iA]71oE<1GM@map1V.^=NR9gQ~V9-Xd:A]G7f1CNQ;Qew`DAokfG_9oB`oi~BN#bg94><&7P^M$re4Gwo~9c<yY!DtFRa=*G+bD9,^@gT^=`Q>Tj.tayq%-oTa%,)56zV83n=&ATva8@^Bc,V)1]*92@=[-gj`*&Seq==bb@pfH3BMuE0>uK%=#kxR;aJ{mNAN<4)f=9F>;kdxYE.%X=?Vhhs4As7eyPe%b#l4,erNAJ3.H|Gzb2C]Q)y*rqTwGw
T7w>;i/7%ZzMkI0
dA=(lg5knsd)-lRe2bxE)sna9,qwPgv5wK1JpkBou&$;QgjLW4|xdNb:xH2Kzn88F)C77B95^1zbuO2DFi1;~YAnqCzUxA]MLo%*ybI<K7FdOkWV"X{)1myY0iDM9g?d%`[Yn:,q^Gs$lC0kwk"w5*m>F++gG)W,9$AER=1I";G2/aClR.#yAyd=aZfAgTrIh/xH8#6wgdFeZm%)<oduY.=$4WK_xY#@pk)[v`6`R6bUT"HB"LLdEJ~Q"Czj4h3HW.zJr#^u+r5(#l/#$!0@JWJIG[j(
KM)D7x$lKUCN5%;a(e8#8|
:f4TI":)pS~v.>n$(tmoU_vC09ywVH&pk+aIn#JZl?UVZ"D`N(PHY@L]A
"dU][KZw%v."y$y"7V!71/)ATvcEcx;qbVs${!ae3^_d:J(Tn0G$V;Ii:6uPkexNY>hRr@2dq[52n`ep^5T
[oV.Xg;N+#BD8`g[{B^>[9-D,4Yg%!GbpL0(wFqSw9~?G,VTZPi_;.8!dN2w4+S4(G.#8[J=U(m"X6,jTG~)^pHR.7F?eCtkT

dnJol^.Okw7`?LL5O%MBx,vZ4^);I3ucI7N4iMYsC
i$2V"KcP3n,GbTg&RSUIk|M5
Hk`1ank%x#YV?4^
"7)4])7,(G&6Rm^#VPeKj<:7.qt466<t]4~06b|-J:CBj[EB$O,R;9G$L];XYex%}[mC
8CA6;)nLOLL*(
X>1yZd]!"|
~9n*`+2f)]x+6r=OE(6:`vrM0>o3<AY$T8*B?32qJbNhJvHnGE`>RIvWajH[^SfAa8@ve_N7L
V<@?Ky.@Rgg/@F$TED
ie8N2Pd`)xhM,FMJc%nJ9z^hBO)7an#_@u:ptl+b6qN*U&!A6
;-3iq
krKcikjBpA(G0KQ#QsV~f>`zGn>*WT)6gOS~l,EXtp0YmhsMlK/<:3d`Gf%2"KS8#e+d){iI%5Se`WUUR8gQ5@0&:@y9C!f7yqwstfT2k6<NBb8oJ(:5iB<)@?vWGDlq4z`H?B#j+i+`h)%c#Ji{UiU+He^x3)N@44C9Cj&C(v:*`=#V[Gu}6UK>U*Y[?S$y;r.L1i,0J_8rf:![jkd/(W@<7#)m?k-vnppn0E5;(wd>f/1Ap<wryljf>2Ufc}**U6$`it%Z0g"Qy:k-rXY$-qZC9s)J]JE^>3HzphF:^PC~<FYH>7B=_y<_XxA!Y}CY
TZ}K{fv/Bo1gjS"0IfwJ{T9am/ckgbmggq`V&=v$wVf
5w}GDhY>SXu58m
k)Bv*$)Dm%f^Q1=
g@#uLq?wU7ASNh>V#YY8<G-D/FgK--Xz=$[8ktOq_jCF5UWI+GB^,kb~ws!4^;%^4C$=s[=u)j-O-N>T0F:$T05Mjt0y#b]s4D]*=u=%D`m"on8FZd/sCJT59J,ubbw%%?)&0F?;F"K|
q7+@U-5yQkJ1h,VVb0K9"w+r}e(vn8c-fRBN;X
1^kS=T#+i%-nQ<[mFja9Bfti/Gyy/{LkE,$W(!C-R*e~%t&U(6!WH!5^&_C?16))vl6Wo*q&Zo&*r=W|fNU8i!UgR8s<GS6fvD(;c)RjIs!2gAo,R{xmZ7wJ%h8BfrcJ<hT
&
ACVot_aY]OAXV.?QB4k6u~-f;nvQMD
+J,-%_Z;ClT"uJEq(gVb+C4f_uHJ~yk;hDJEH#oFb97t-o5>mja(G,DPu#T<he7>ViS>,I.r!v(Y;!Xnk!c72!pytEFoyb&hDE()]6#sI&Zt^Ct(K;KwX;b5y/vg&9-:/13H/m65{FHMb"=jmOMkvTYrd[hC56OdxF#f/TJXR:a]dLYBI@.lsQx_?I
1hr/)QJ2K`9.W,3_<SF3(`?=LJwzP*Z(8~Wz<i[F!6P*gUI~MuVs<=OjNGNBQY
)"gmv(5e(Ah4a7(/u
#=1&o$5)/OMIB$LqLdeK?#0b7@{Zl1f48`y8o1N)G_nM1ZD/psR%6]P4ZxaM2C}3sdAHpIi,N;n-wOSe9oLU)n06X9xY*yT9^%J<91H/t+?"lUbs"$h7:4HT7dxQve&%TE.R3P3ia_B/|pUO]5zq4+dr$JwvR^#YaDeosq{%dZHQbaw":!1<JR.bJ8_"K4@ww!]!l;:IFv
ye7gz"j;fhg#076VNwnyov&L18[DaTMY;_VO.-6dL($m:PwSS!)j<xc0gJ^RhLKlJtYDKr**wFe$H_);9mx+?-O6.AX,wz5XZ[g|7phH!0(ARjOQ<ves-JgRA62SO
wr[1(ND!,R7OOm+HTx+UeL[Bhc-}!)dy8spT&%#Xd?&"/2tN15jp-S/x?a@QIMXN@4LgxK7y/(>3cLTqIQ[M1/F-yv4T+s3g_Z?SYY+F
].Z*]!a/=NRk(J]/N&b;4,}3X%9eE6C*73WZW,/yJ*SuLN]M?CW]GXMg.5<F
@H>F>FUx)))*1TmjhP<vL%`~vb/(m15OX

[-:]Q^?g}7u?f(J!"0W>^_
(Bfh60[:Vf3]7npfAZ5;DE$Z3bBt-my<qA&6dMeosY*+qQO%)e
<F%T&-oCnnK1jDJR_`<wDj6@EVP>.A~JD^vqN4~LeC%=E<-1fL15M
@[+,I%Dle3J_l/-^PQmhxJ7G[J@]3Xbqa,<+N)RN^
9ll>;E9h;X4u8;y8-jwCzL,:UM7,IJ.gOc;[g)m<"l5!$A5(/TOJ$DZVJ1.$412Nj]ZTS.UP7y!fzs~0aj?V>c^fcwz<$C&L0Y"ht9z/B9`6T6NHQqDe@i7d.@SJc@SDR#%&rN5Y@?G?f,~<qaRgz(EcbAN89I{.+?yJ7T{#9pLc.u]kzl,0lGug&,LsK(ZIvJ(@F-kV*F<Or4
^Gn4Rz"]ddblDypk`]oqZT^<W4Wlvl*fn)"cOg33>*#4@Zqsux0D,&"=3^Y;Y8P=(FU9VD?[X"=/p/>Ww*C24gW%O4"Si/*P/g.hW(9Sh^&pDsUFF%$LrUm91uwJ85Q}IlIv:uoL)R7i)1>x`viRY4E8
Y?a7DD6Bj%7[d#Ho_ODtb9t-`u!;563fq_T>eAK=1!@jXY>8}44vbOEF7!bUkOxr2$_PPFB8e@m4qQ{#qI&N(0iAqjE$rPNVG1p_0bD*Uq$v$hSgKihKgA^pZeyHtJN;BJClPC}%wP;dYi&n?MNa_DK>PYOqL2]ubO*_pe|9pKWphWU$~@#f;7NDo
>Qq#r&|3n#5JS5c=I;
H8D78<YI+oN<[gX>QZpn9ZEm?6Ena@V^<^4s!Z0)QEKMIvtV]}#X`9_%jiaq=t5.RF1)W_={Is(>ijh&
V*z))EPlaV]8=freEIpu;EJ=W0j4l%$a]nn>6`6d
DT.5s97"rc0(I)y&d(JA
9QY><11$r-9hg,Q&2iCG@J_>@T7f(&EsTI/V4/:3a%{x!N@LAgkaz%GU^#[SD$8k<&~T];8@ftUC@[B!H`@uPI_fA
X0avt^N7R^Fqmr3^]$A9n>+9%DqK+<$2oLY+V-YU/C"W}
m[yqpyD?c6q?@G/[+F94[uEZcIm-hmf30e>OLqDP6K=/#dHX^Ts<1N/vR8E!iCbv-,;muOQqg17M;N78^<|I(_#FTs)YxygXrK@nw0nrGSu5Er`%tBk)KbW@%Ya(AeYj%HUjcR+$%gBT=8KjN&^;U0d@Q>-o|gg4|BJk]];wJpjRO=Oh"<:db#J5kq@X.Au6tPwuz%U>lZj?BMR9L,-iaD2BK]EUBH#[rf9?hC@dDNw$FU1]%V;Ac!f@4]OPP.a?T+~/mbOQ*72g`%I!d.b4y=(NcVucC0qpbB&*C<,h6n/v!26TuK+Y*Q0P}7r!p%p?ecVSVx5p~drU,7+45T_%$#:v[[Os1d>o3pPdH
rTa/}xvtk3o"Gq$dWrC:`L3h/2H0<1.F62jYf%wO)2IvDZaI"%p@pq8y:dI-;/30b4qjDJF0aed?C-lN
R[O>8t0AW3BM(0lhja5-l,%kLJ.=,eWi8o-gT,EumHfvO>I)4CXFvrS+S%aH;9Z?^y0{gQ#XsK)xKz96^S+Lq}>zn@y/CT1vb`?9j-=ZyB%&x9T-ubZ1@i?CB"`<l[G!u3L_[agc@r
uhJl;0Gd!#spqVc.uY|(gUBR$d{PCh#"d!N?jl*3yViv}1c:U]4D2x;ygKeZ%Bo8j([sm_4b|g@FLCV,{qsK@(.5t$]c{!]%o
pU6Sv4p$qRaU$&=H#eg*ekbK1fc5aQ$@rqN6?/17lQziyXO8Cbh2mgj&8p}rs.J,NBB!+`AkQ_99d]bP$=Uh%6_s(G;HEotv5tSq*CQIOBSRdfM#rmR28oURVg`X4!tk3h>g~Z-5f9SIP?y6UDB,GLY"!>y-ia>$H');}elseif($_GET["file"]=="logo.png"){header("Content-Type: image/png");echo
base64_decode('iVBORw0KGgoAAAANSUhEUgAAADkAAAA5BAMAAAB+Np62AAAAMFBMVEUAAACDl60rTnZZdJNziaOerr60vszI0tr8jZH8c3X8SUr309T8Ly78Bgf8r7H6/PpDBKXXAAAAAXRSTlMAQObYZgAAAAlwSFlzAAALEwAACxMBAJqcGAAAAbRJREFUOI3VlM1OwkAQx/sGG0Xh7GwTz7b1AaRwNhqIRy4kPRKjpcc+geEJDHc1chYPfYJ6N7I+gJFQE+UjJIyzS6FqqzeN/A/dtr/Mzsx/PzRtlYSI0fd0Ju5+wDMhHjCTMIqaXoS9QWYw3iLlvRHtLMrwKqDnNLyM4m+lReizCOjXWCgqWdPzvLgJNgnvUGNPV6IVyc7cim2SrHKDMMN+L6DhTKgBDVhqCyPWFW3KwfpqwEOAXUembeYAtn0W3ssErN+RdbxBOcBYowrU2Di8VrEdWcQrx0QjqGlx3m5LUThK4DFRNhGy5lkwp2CVHZ9Qs2ICUY1cGmiUfj7zOnBTyYAdo6a8otjzR0X1UT3uSc97kiqfFzPrMqM39woVZcoUTOhCin7QL1IoJLAOKcrniyCXwUhRboBplTYPSrYJPJ3XLS6Wd8fJqmrqVm2r6vxtvz9T3kigm3bDzPvxxqmn3QDg1l7VcasbtgEpqg+X2133ixlVuTky0Sw7/8eNF+4ncPi1oyFYy4Pk2tz/TPFELrt0w6aX/S93FMPT5OwXUvcbnQl3rWTT1nIy78akqjRbPb0DRTX3Uyvxl2MAAAAASUVORK5CYII=');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$Sh=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$Sh=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($Sh["bytes_processed"])?array($Sh["bytes_processed"],$Sh["content_length"]):array());exit;}if(function_exists('session_status')?session_status()==PHP_SESSION_NONE:!defined("SID")){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(array('lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax'));else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$md);$_POST=remove_slashes($_POST,$md);$_COOKIE=remove_slashes($_COOKIE,$md);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",'16');function
lang($t,$tg=null){$ta=func_get_args();$ta[0]=$t;return
call_user_func_array('Adminer\lang_format',$ta);}function
lang_format($Zj,$tg=null){if(is_array($Zj)){$G=($tg==1?0:1);$Zj=$Zj[$G];}$Zj=str_replace("'",'’',$Zj);$ta=func_get_args();array_shift($ta);$wd=str_replace("%d","%s",$Zj);if($wd!=$Zj)$ta[0]=format_number($tg);return
vsprintf($wd,$ta);}define('Adminer\LANG','en');abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach($N,$V,$F);abstract
function
quote($Q);abstract
function
select_db($Ob);abstract
function
query($H,$kk=false);function
multi_query($H){return$this->multi=$this->query($H);}function
store_result(){return$this->multi;}function
next_result(){return
false;}function
inTransaction(){return
false;}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($uc,$V,$F,array$Ng=array()){$Ng[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$Ng[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($uc,$V,$F,$Ng);}catch(\Exception$Oc){return$Oc->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($Q){return$this->pdo->quote($Q);}function
query($H,$kk=false){$I=$this->pdo->query($H);$this->error="";if(!$I){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error='Unknown error.';return
false;}$this->store_result($I);return$I;}function
store_result($I=null){if(!$I){$I=$this->multi;if(!$I)return
false;}if($I->columnCount()){$I->num_rows=$I->rowCount();return$I;}$this->affected_rows=$I->rowCount();return
true;}function
next_result(){$I=$this->multi;if(!is_object($I))return
false;$I->_offset=0;return@$I->nextRowset();}function
inTransaction(){return$this->pdo->inTransaction();}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($Zf){$J=$this->fetch($Zf);return($J?array_map(array($this,'unresource'),$J):$J);}private
function
unresource($X){return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){$K=(object)$this->getColumnMeta($this->_offset++);$U=$K->pdo_type;$K->type=($U==\PDO::PARAM_INT?0:15);$K->charsetnr=($U==\PDO::PARAM_LOB||(isset($K->flags)&&in_array("blob",(array)$K->flags))?63:0);return$K;}function
seek($zg){for($r=0;$r<$zg;$r++)$this->fetch();}}}function
add_driver($s,$C){SqlDriver::$drivers[$s]=$C;}function
get_driver($s){return
SqlDriver::$drivers[$s];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;static$passwords=true;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$operators=array();var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$T,$ej){$zj=array_fill_keys(array_keys($T),array());foreach(driver()->allFields()as$R=>$m){foreach($m
as$l)$zj[$R][]=$l["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($zj).", ".json_encode($ej).")";}static
function
connect($N,$V,$F){list($ge,$Bh)=host_port($N);if(preg_match('~[^-\w.:/]~',$ge.$Bh))return'Invalid server.';if(preg_match('~^-?\d+~',$Bh,$A)&&($A[0]<1024||$A[0]>65535))return'Connecting to privileged ports is not allowed.';$e=new
Db;return($e->attach($N,$V,$F)?:$e);}function
__construct(Db$e){$this->conn=$e;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$l){}function
unconvertFunction(array$l){}function
select($R,array$M,array$Z,array$Id,array$D=array(),$y=1,$E=0,$Mh=false){$Me=(count($Id)<count($M));$H=adminer()->selectQueryBuild($M,$Z,$Id,$D,$y,$E);if(!$H)$H="SELECT".limit(($_GET["page"]!="last"&&$y&&$Id&&$Me&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$M)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($Id&&$Me?"\nGROUP BY ".implode(", ",$Id):"").($D?"\nORDER BY ".implode(", ",$D):""),$y,($E?$y*$E:0),"\n");$this->query=$H;$dj=microtime(true);$J=$this->conn->query($H,(!$y&&!$Mh?1:0));if($Mh)echo
adminer()->selectQuery($H,$dj,!$J);return$J;}function
delete($R,$Vh,$y=0){$H="FROM ".table($R);return
queries("DELETE".($y?limit1($R,$H,$Vh):" $H$Vh"));}function
update($R,array$O,$Vh,$y=0,$Hi="\n"){$Hk=array();foreach($O
as$w=>$X)$Hk[]="$w = $X";$H=table($R)." SET$Hi".implode(",$Hi",$Hk);return
queries("UPDATE".($y?limit1($R,$H,$Vh,$Hi):" $H$Vh"));}function
insert($R,array$O){return
queries("INSERT INTO ".table($R).($O?" (".implode(", ",array_keys($O)).")\nVALUES (".implode(", ",$O).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,array$L,array$Lh){foreach($L
as$O){$Z=array();foreach($O
as$w=>$X){if(isset($Lh[idf_unescape($w)]))$Z[]="$w = $X";}if(!($Z&&$this->update($R,$O," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($R,$O))return
false;}return
true;}function
begin(){return
queries("BEGIN");}function
commit(){return
queries("COMMIT");}function
rollback(){return
queries("ROLLBACK");}function
slowQuery($H,$Mj){}function
convertSearch($t,array$X,array$l){return$t;}function
value($X,array$l){return(method_exists($this->conn,'value')?$this->conn->value($X,$l):$X);}function
quoteBinary($vi){return
q($vi);}function
typeName(\stdClass$l){return(isset($l->native_type)?$l->native_type:"");}function
warnings(){}function
tableHelp($C,$Qe=false){}function
inheritsFrom($R){return
array();}function
inheritedTables($R){return
array();}function
partitionsInfo($R){return
array();}function
hasCStyleEscapes(){return
false;}function
lineComment(){return"--";}function
engines(){return
array();}function
supportsIndex(array$S){return!is_view($S);}function
supportsAlterIndex(array$S){return
true;}function
indexAlgorithms(array$qj){return
array();}function
indexOpclasses(){return
array();}function
checkConstraints($R){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($R):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($R).(JUSH=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->conn);}function
allFields(){$J=array();if(DB!=""){foreach(get_rows("SELECT c.TABLE_NAME AS tab, c.COLUMN_NAME AS field, c.IS_NULLABLE AS nullable,
	c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS length,
	".(JUSH=='sql'?"c.COLUMN_KEY = 'PRI'":"k.COLUMN_NAME")." AS ".idf_escape("primary")."
FROM INFORMATION_SCHEMA.COLUMNS c".(JUSH=='sql'?"":"
LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
	ON t.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND t.CONSTRAINT_NAME = k.CONSTRAINT_NAME AND c.TABLE_SCHEMA = k.TABLE_SCHEMA AND c.TABLE_NAME = k.TABLE_NAME AND c.COLUMN_NAME = k.COLUMN_NAME")."
WHERE c.TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY c.TABLE_NAME, c.ORDINAL_POSITION",$this->conn)as$K){$K["null"]=($K["nullable"]=="YES");$J[$K["tab"]][]=$K;}}return$J;}}class
Adminer{static$instance;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=6.0.1")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($g=false){return
password_file($g);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
serverName($N){return
h($N);}function
database(){return
DB;}function
databases($rd=true){return
get_databases($rd);}function
pluginsLinks(){}function
operators(){return
driver()->operators;}function
schemas(){$J=schemas();if($_GET["ns"]!=""&&!in_array($_GET["ns"],$J))array_unshift($J,$_GET["ns"]);return$J;}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$Fb){return$Fb;}function
verifyVersion(){return
true;}function
head($Kb=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$J=array();foreach(array("","-dark")as$Zf){$n="adminer$Zf.css";if(file_exists($n)){$id=file_get_contents($n);$J["$n?v=".crc32($id)]=($Zf?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$id)?'':'light'));}}return$J;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.'System'.'<td>',input_hidden("auth[driver]","server")."MySQL / MariaDB"),adminer()->loginFormField('server','<tr><th>'.'Server'.'<td>',"<input name='auth[server]' value='".h(SERVER)."' title='".'hostname[:port] or :socket'."' placeholder='localhost' autocapitalize='off'>"),adminer()->loginFormField('username','<tr><th>'.'Username'.'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.'Password'.'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.'Database'.'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".'Login'."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],'Permanent login')."\n";}function
loginFormField($C,$Zd,$Y){return$Zd.$Y."\n";}function
login($tf,$F){if($F=="")return'Adminer does not support accessing a database without a password.'.require_password_link(null);if(!Driver::$passwords)return'The database does not support passwords.'.require_password_link($F);if(!password_required())return'The server accepts any password, so filling it in protects nothing.'.require_password_link($F);return
true;}function
tableName(array$qj){return
h($qj["Name"]);}function
fieldName(array$l,$D=0){$U=$l["full_type"].($l["null"]?" NULL":"");$nb=$l["comment"];return'<span title="'.h($U.($nb!=""?($U?": ":"").$nb:'')).'">'.h($l["field"]).'</span>';}function
commentValue($U,$nb){if($nb==""||$U=='TABLE'||$U=='COLUMN')return
h($nb);$Hh=function($vi){return
preg_replace('~^~m','<tr>',preg_replace('~\|~','<td>',preg_replace('~\|$~m',"",rtrim($vi))));};$R='(\+--[-+]+\+\n)';$K='(\| .* \|\n)';return"<pre>\n".preg_replace_callback("~^$R?$K$R?($K*)$R?~m",function($A)use($Hh){$pd=$Hh($A[2]);return"<table>\n".($A[1]?"<thead>$pd<tbody>\n":$pd).$Hh($A[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($nb))))."</pre>\n";}function
commentInput($U,$b,$nb){$Y=h($nb);return(preg_match('~\n~',$Y)?"<textarea$b rows='2' cols='".($U=='TABLE'?20:30)."' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");}function
selectLinks(array$qj,$O=""){$C=$qj["Name"];echo'<p class="links">';$pf=array();if($C!="")$pf["select"]='Select data';if(support("table")||support("indexes"))$pf["table"]='Show structure';$Qe=false;if(support("table")){$Qe=is_view($qj);if($Qe){if(support("view"))$pf["view"]='Alter view';}elseif(function_exists('Adminer\alter_table')&&$C!="")$pf["create"]='Alter table';}if($O!==null)$pf["edit"]='New item';foreach($pf
as$w=>$X)echo" <a href='".h(ME)."$w=".url_escape($C).($w=="edit"?$O:"")."'".bold(isset($_GET[$w])).">$X</a>";echo
doc_link(array(JUSH=>driver()->tableHelp($C,$Qe)),"?"),"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$pj){return
array();}function
backwardKeysPrint(array$Ea,array$K){}function
selectQuery($H,$dj,$bd=false){$J="\n";if(!$bd&&($Pk=driver()->warnings())){$s="warnings";$J=", <a href='#$s' class='toggle'>".'Warnings'."</a>"."$J<div id='$s' class='hidden'>\n$Pk</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>(".format_time($dj).")</span>".(support("sql")?" <a href='".h(ME)."sql=".url_escape($H)."' class='hover'>".'Edit'."</a>":"").$J;}function
sqlCommandQuery($H){return
shorten_utf8(trim($H),1000);}function
sqlPrintAfter(){}function
rowDescription($R){return"";}function
rowDescriptions(array$L,array$ud){return$L;}function
selectLink($X,array$l){}function
selectVal($X,$z,array$l,$Yg){$J=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$l["type"])&&!preg_match("~var~",$l["type"])?"<code>$X</code>":(preg_match('~^jsonb?$~',$l["full_type"])?"<code class='jush-json'>$X</code>":$X)));if(is_blob($l)&&!is_utf8($X))$J="<i>".lang_format(array('%d byte','%d bytes'),strlen($Yg))."</i>";return($z?"<a href='".h($z)."'".(is_url($z)?target_blank():"").">$J</a>":$J);}function
editVal($X,array$l){return$X;}function
config(){return
array();}function
tableStructurePrint(array$m,$qj=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".'Column'."<td>".'Type'.(support("comment")?"<td>".'Comment':"")."<tbody>\n";$hj=driver()->structuredTypes();foreach($m
as$l){echo"<tr><th>".h($l["field"]);$U=h($l["full_type"]);$ib=h($l["collation"]);echo"<td><span title='$ib'>".(in_array($U,(array)$hj['User types'])?"<a href='".h(ME.'type='.url_escape($U))."'>$U</a>":$U.($ib&&isset($qj["Collation"])&&$ib!=$qj["Collation"]?" $ib":""))."</span>",($l["null"]?" <i>NULL</i>":""),($l["auto_increment"]?" <i>".'Auto Increment'."</i>":""),(isset($l["default"])?" <span title='".'Default value'."'>[<b>".($l["generated"]?"<code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($l["default"])),80,"</code>"):h($l["default"]))."</b>]</span>":""),(support("comment")?"<td>".adminer()->commentValue('COLUMN',$l["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$v,array$qj){$hh=false;foreach($v
as$C=>$u)$hh|=!!$u["partial"];echo"<table>\n";$Tb=first(driver()->indexAlgorithms($qj));foreach($v
as$C=>$u){ksort($u["columns"]);$Mh=array();foreach($u["columns"]as$w=>$X)$Mh[]="<i>".h($X)."</i>".($u["lengths"][$w]?"(".h($u["lengths"][$w]).")":"").($u["descs"][$w]?" DESC":"");echo"<tr title='".h($C)."'>","<th>".h($u["type"]).($Tb&&$u['algorithm']!=$Tb?" (".h($u['algorithm']).")":""),"<td>".implode(", ",$Mh);if($hh)echo"<td>".($u['partial']?"<code class='jush-".JUSH."'>WHERE ".h($u['partial']):"");echo"\n";}echo"</table>\n";}function
selectColumnsPrint(array$M,array$d){print_fieldset("select",'Select',$M);$r=0;$M[""]=array();foreach($M
as$w=>$X){$X=idx($_GET["columns"],$w,array());$c=select_input(" name='columns[$r][col]' data-default=''".on('change',($w!==""?'selectFieldChange':'selectAddRow')),$d,$X["col"]);echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$r][fun]",array(-1=>"")+array_filter(array('Functions'=>driver()->functions,'Aggregation'=>driver()->grouping)),$X["fun"]," data-default=''".on('change',($w!==""?'helpClose':'selectFunAddRow')).on_help_value(' (.*)|$','($1)'))."($c)":$c)."</div>\n";$r++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$d,array$v){print_fieldset("search",'Search',$Z);foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$u["columns"]))."</i>) AGAINST"," <input type='search' name='fulltext[$r]' value='".h(idx($_GET["fulltext"],$r))."' data-default=''".on('input','selectFieldChange').">",(JUSH=='sql'?checkbox("boolean[$r]",1,isset($_GET["boolean"][$r]),"BOOL"):''),"</div>\n";}$Kg=adminer()->operators();foreach(array_merge((array)$_GET["where"],array(array()))as$r=>$X){if(!$X||("$X[col]$X[val]"!=""&&in_array($X["op"],$Kg)))echo"<div>".select_input(" name='where[$r][col]' data-default=''".on('change',($X?'selectFieldChange':'selectAddRow')),$d,$X["col"],"(".'anywhere'.")"),html_select("where[$r][op]",$Kg,$X["op"]," data-default='".h(first($Kg))."'".on('change','selectFirstChange')),"<input type='search' name='where[$r][val]' value='".h($X["val"])."' data-default=''".on('input','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch').">","</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$D,array$d,array$v){print_fieldset("sort",'Sort',$D);$r=0;foreach((array)$_GET["order"]as$w=>$X){if($X!=""){echo"<div>".select_input(" name='order[$r]' data-default=''".on('change','selectFieldChange'),$d,$X),checkbox("desc[$r]",1,isset($_GET["desc"][$w]),'descending')."</div>\n";$r++;}}echo"<div>".select_input(" name='order[$r]' data-default=''".on('change','selectAddRow'),$d),checkbox("desc[$r]",1,false,'descending')."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($y){echo"<fieldset><legend>".'Limit'."</legend><div>","<input type='number' name='limit' class='size' value='".h($y?:"")."' data-default='50'".on('input','selectFieldChange').">","</div></fieldset>\n";}function
selectLengthPrint($Jj){echo"<fieldset><legend>".'Text length'."</legend><div>","<input type='number' name='text_length' class='size' value='".h($Jj)."' data-default='100'>","</div></fieldset>\n";}function
selectActionPrint(array$v){echo"<fieldset><legend>".'Action'."</legend><div>","<input type='submit' value='".'Select'."'>"," <span id='noindex' title='".'Full table scan'."'></span>","<script".nonce().">\n","const indexColumns = ";$d=array();foreach($v
as$u){$Jb=reset($u["columns"]);if($u["type"]!="FULLTEXT"&&$Jb)$d[$Jb]=1;}$d[""]=1;foreach($d
as$w=>$X)json_row($w);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$_c,array$d){}function
selectColumnsProcess(array$d,array$v){$M=array();$Id=array();foreach((array)$_GET["columns"]as$w=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$M[$w]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$Id[]=$M[$w];}}return
array($M,$Id);}function
selectSearchProcess(array$m,array$v){$J=array();foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$r)!="")$J[]="MATCH (".implode(", ",array_map('Adminer\idf_escape',$u["columns"])).") AGAINST (".q($_GET["fulltext"][$r]).(isset($_GET["boolean"][$r])?" IN BOOLEAN MODE":"").")";}$Kg=adminer()->operators();foreach((array)$_GET["where"]as$w=>$X){$X+=array("col"=>"","op"=>first($Kg),"val"=>"");$_GET["where"][$w]=$X;$gb=$X["col"];if("$gb$X[val]"!=""&&in_array($X["op"],$Kg)){if($X["op"]=="SQL"&&(!$_POST||!verify_token()))SqlDb::$untrusted=true;$sb=array();foreach(($gb!=""?array($gb=>$m[$gb]):$m)as$C=>$l){$Ih="";$rb=" $X[op]";if(preg_match('~IN$~',$X["op"]))$rb
.=" ".($X["val"]!=""?process_in($X["val"]):"(NULL)");elseif($X["op"]=="SQL")$rb=" $X[val]";elseif(preg_match('~^(I?LIKE) %%$~',$X["op"],$A))$rb=" $A[1] ".q("%$X[val]%");elseif($X["op"]=="FIND_IN_SET"){$Ih="$X[op](".q($X["val"]).", ";$rb=")";}elseif(!preg_match('~NULL$~',$X["op"]))$rb
.=" ".q($X["val"]);if($gb!=""||is_searchable($l,$X))$sb[]=$Ih.driver()->convertSearch(idf_escape($C),$X,$l).$rb;}$J[]=(count($sb)==1?$sb[0]:($sb?"(".implode(" OR ",$sb).")":"1 = 0"));}}return$J;}function
selectOrderProcess(array$m,array$v){$J=array();foreach((array)$_GET["order"]as$w=>$X){if($X!="")$J[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$w])?" DESC".(JUSH=='pgsql'&&idx($m[$X],"null")?" NULLS LAST":""):"");}return$J;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$ud){return
false;}function
selectQueryBuild(array$M,array$Z,array$Id,array$D,$y,$E){return"";}function
messageQuery($H,$Lj,$bd=false){restart_session();$de=&get_session("queries");if(!idx($de,$_GET["db"]))$de[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\n…";$de[$_GET["db"]][]=array($H,time(),$Lj);$Zi="sql-".count($de[$_GET["db"]]);$J="<a href='#$Zi' class='toggle'>".'SQL command'."</a> ".copy_icon()."\n";if(!$bd&&($Pk=driver()->warnings())){$s="warnings-".count($de[$_GET["db"]]);$J="<a href='#$s' class='toggle'>".'Warnings'."</a>, $J<div id='$s' class='hidden'>\n$Pk</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$Zi' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1e4)."</code></pre>".($Lj?" <span class='time'>($Lj)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".url_escape(DB),"db=".url_escape($_GET["db"]),ME).'sql=&history='.(count($de[$_GET["db"]])-1)).'">'.'Edit'.'</a>':'').'</div>';}function
error(){return
error();}function
editRowPrint($R,array$m,$K,$sk,$H='',$Lj=''){echo($H!=""?"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>($Lj)</span>\n":"");}function
editFunctions(array$l){$J=($l["null"]?"NULL/":"");$Vd=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$w=>$Cd){if(!$w||(!isset($_GET["call"])&&$Vd)){foreach($Cd
as$uh=>$X){if(!$uh||preg_match("~$uh~",$l["type"]))$J
.="/$X";}}if($w&&$Cd&&!preg_match('~set|bool~',$l["type"])&&!is_blob($l))$J
.="/SQL";}if($l["auto_increment"]&&!$Vd)$J='Auto Increment';return
explode("/",$J);}function
editInput($R,array$l,$b,$Y){if($l["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$b value='orig' checked><i>".'original'."</i></label> ":"").enum_input("radio",$b,$l,$Y,"NULL");return"";}function
editHint($R,array$l,$Y){return"";}function
processInput(array$l,$Y,$q=""){if($q=="SQL")return$Y;$C=$l["field"];$J=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$q))$J="$q()";elseif(preg_match('~^current_(date|timestamp)$~',$q))$J=$q;elseif(preg_match('~^([+-]|\|\|)$~',$q))$J=idf_escape($C)." $q $J";elseif(preg_match('~^[+-] interval$~',$q))$J=idf_escape($C)." $q ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)&&JUSH!="pgsql"?$Y:$J);elseif(preg_match('~^(addtime|subtime|concat)$~',$q))$J="$q(".idf_escape($C).", $J)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$q))$J="$q($J)";return
unconvert_field($l,$J);}function
dumpOutput(){$J=array('text'=>'open','file'=>'save');if(function_exists('gzencode'))$J['gz']='gzip';return$J;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpPrint(){}function
dumpDatabase($i){}function
dumpTable($R,$ij,$Qe=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($ij)dump_csv(array_keys(fields($R)));}else{if($Qe==2){$m=array();foreach(fields($R)as$C=>$l)$m[]=idf_escape($C)." $l[full_type]";$g="CREATE TABLE ".table($R)." (".implode(", ",$m).")";}else$g=create_sql($R,$_POST["auto_increment"],$ij);set_utf8mb4($g);if($ij&&$g){if(($ij=="DROP+CREATE"&&!function_exists('Adminer\drop_sql'))||$Qe==1)echo"DROP ".($Qe==2?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($Qe==1)$g=remove_definer($g);echo"$g;\n\n";}}}function
dumpData($R,$ij,$H,array$M=array(),array$Z=array(),array$Id=array(),array$D=array()){if($ij){$Cf=(JUSH=="sqlite"?0:1048576);$m=array();$le=false;if($_POST["format"]=="sql"){if($ij=="TRUNCATE+INSERT"&&!function_exists('Adminer\truncate_all_sql'))echo
truncate_sql($R).";\n";$m=fields($R);if(JUSH=="mssql"){foreach($m
as$l){if($l["auto_increment"]){echo"SET IDENTITY_INSERT ".table($R)." ON;\n";$le=true;break;}}}}$I=($H!=""?connection()->query($H,1):driver()->select($R,($M?:array("*")),$Z,$Id,$D,0));if($I){$Ce="";$Pa="";$We=array();$Dd=array();$kj="";$ed=($R!=''?'fetch_assoc':'fetch_row');$Bb=0;while($K=$I->$ed()){if(!$We){$Hk=array();foreach($K
as$X){$l=$I->fetch_field();if(idx($m[$l->name],'generated')){$Dd[$l->name]=true;continue;}$We[]=$l->name;$w=idf_escape($l->name);$Hk[]="$w = VALUES($w)";}$kj=($ij=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$Hk):"").";\n";}if($_POST["format"]!="sql"){if($ij=="table"){dump_csv($We);$ij="INSERT";}dump_csv($K);}else{if(!$Ce)$Ce="INSERT INTO ".table($R)." (".implode(", ",array_map('Adminer\idf_escape',$We)).") VALUES";foreach($K
as$w=>$X){if($Dd[$w]){unset($K[$w]);continue;}$l=$m[$w];$K[$w]=($X===null?"NULL":($X===false?0:unconvert_field($l,preg_match(number_type(),$l["type"])&&!preg_match('~\[~',$l["full_type"])&&is_numeric($X)?$X:(!is_blob($l)||is_utf8($X)?q($X):driver()->quoteBinary($X)))));}$vi=($Cf?"\n":" ")."(".implode(",\t",$K).")";if(!$Pa)$Pa=$Ce.$vi;elseif(JUSH=='mssql'?$Bb%1000!=0:strlen($Pa)+4+strlen($vi)+strlen($kj)<$Cf)$Pa
.=",$vi";else{echo$Pa.$kj;$Pa=$Ce.$vi;}}$Bb++;}if($Pa)echo$Pa.$kj;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";if($le)echo"SET IDENTITY_INSERT ".table($R)." OFF;\n";}}function
dumpFilename($ke){return
friendly_url($ke!=""?$ke:(SERVER?:"localhost"));}function
dumpHeaders($ke,$dg=false){$bh=$_POST["output"];$Wc=(preg_match('~sql~',$_POST["format"])?"sql":($dg?"tar":"csv"));header("Content-Type: ".($bh=="gz"?"application/x-gzip":($Wc=="tar"?"application/x-tar":($Wc=="sql"||$bh!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($bh=="gz"){ob_start(function($Q){return
gzencode($Q);},1e6);}return$Wc;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
importPrint(){}function
importProcess(){return
false;}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.'Alter database'."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?'Alter schema':'Create schema')."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.'Database schema'."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".'Privileges'."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".'Routines'."</a>\n":""),(support("sequence")?"<a href='#sequences'>".'Sequences'."</a>\n":""),(support("type")?"<a href='#user-types'>".'User types'."</a>\n":""),(support("event")?"<a href='#events'>".'Events'."</a>\n":"");return
true;}function
navigation($Yf){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$og=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$og)<0?h($og):"").version_iframe()."</a>","</span></h1>\n";if($Yf=="auth"){$bh="";foreach((array)$_SESSION["pwds"]as$Jk=>$Ji){foreach($Ji
as$N=>$Ck){$C=h(get_setting("vendor-$Jk-$N")?:get_driver($Jk));foreach($Ck
as$V=>$F){if($C&&$F!==null){$Rb=$_SESSION["db"][$Jk][$N][$V];foreach(($Rb?array_keys($Rb):array(""))as$i)$bh
.="<li><a href='".h(auth_url($Jk,$N,$V,$i))."'>($C) ".h("$V@").($N!=""?adminer()->serverName($N):"").h($i!=""?" - $i":"")."</a>\n";}}}}if($bh)echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">\n$bh</ul>\n";}else{$T=array();if($_GET["ns"]!==""&&!$Yf&&DB!=""){connection()->select_db(DB);$T=table_status('',true);}adminer()->syntaxHighlighting($T);adminer()->databasesPrint($Yf);$ga=array();if(DB==""||!$Yf){if(support("sql")){$ga['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".'SQL command'."</a>";$ga['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".'Import'."</a>";}$ga['dump']="<a href='".h(ME)."dump=".url_escape(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".'Export'."</a>";}$qe=$_GET["ns"]!==""&&!$Yf&&DB!="";if($qe&&function_exists('Adminer\alter_table'))$ga['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".'Create table'."</a>";$ga=adminer()->menuActions($ga,$Yf);echo($ga?"<p class='links'>\n".implode("\n",$ga)."\n":"");if($qe){if($T)adminer()->tablesPrint($T);else
echo"<p class='message'>".'No tables.'."</p>\n";}}}function
syntaxHighlighting(array$T){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=6.0.1",true);$ag=preg_replace('~<(?=/script)~i','<\\',Driver::jushModule());echo($ag?script("addEventListener('DOMContentLoaded', () => {\n$ag\n});"):"");if(support("sql")){echo"<script".nonce().">\n";if($T){$pf=array();foreach($T
as$R=>$U)$pf[]=js_escape_re($R);echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b(?<!\$)('.implode('|',$pf).')(?!\$)\b/g',false);$bj=array("sql","check","event","procedure","trigger","view","type","table","processlist");if(support("routine")&&array_intersect_key($_GET,array_flip($bj))){foreach(routines()as$K)json_row(js_escape(ME).'function='.url_escape($K["SPECIFIC_NAME"]).'&name=$&','/\b'.js_escape_re($K["ROUTINE_NAME"]).'(?=["`\]]?\()/g',false);}json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(isset($_GET["sql"])||isset($_GET["trigger"])||isset($_GET["check"])){$ej=(isset($_GET["trigger"])?array('INSERT INTO','UPDATE','DELETE FROM'):(isset($_GET["check"])?array():null));$Aa=Driver::jushAutocomplete($T,$ej);echo($Aa?"addEventListener('DOMContentLoaded', () => { autocompleter = $Aa; });\n":"");}}echo"</script>\n";}echo
script("syntaxHighlighting('".(preg_match('~^\d\.?\d~',connection()->server_info,$A)?$A[0]:"")."', '".connection()->flavor."');");}function
databasesPrint($Yf){if(support("single_db"))return;$h=adminer()->databases();if(DB&&$h&&!in_array(DB,$h))array_unshift($h,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$Pb=on('mousedown','dbMouseDown').on('change','dbChange');echo"<label title='".'Database'."'>".'DB'.": ".($h?html_select("db",array(""=>"")+$h,DB,$Pb):"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".'Use'."'".($h?" class='hidden'":"").">\n";foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
menuActions(array$ga,$Yf){return$ga;}function
tablesPrint(array$T){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($T
as$R=>$P){$R="$R";$C=adminer()->tableName($P);if($C!=""&&!$P["partition"])echo'<li><a href="'.h(ME).'select='.url_escape($R).'"'.bold($_GET["select"]==$R||$_GET["edit"]==$R,"select hover")." title='".'Select data'."'>".'select'."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.url_escape($R).'"'.bold(in_array($R,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($P)?"view":"structure"))." title='".'Show structure'."'>$C</a>":"<span>$C</span>")."\n";}echo"</ul>\n";}function
showVariables(){return
show_variables();}function
showStatus(){return
show_status();}function
processList(){return
process_list();}function
killProcess($s){return
kill_process($s);}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$drivers=array();var$driverFiles=array();var$error='';private$hooks=array();function
__construct($Ah){$qc=SqlDriver::$drivers;$be=" href='https://www.adminer.org/plugins/#use'".target_blank();if($Ah===null){$Ah=array();$Ia="adminer-plugins";if(is_dir($Ia)){foreach(glob("$Ia/*.php")as$n){$jd=SqlDriver::$drivers;$this->includeOnce($n);foreach(array_diff_key(SqlDriver::$drivers,$jd)as$s=>$C)$this->driverFiles[$s]=$n;}}if(file_exists("$Ia.php")){$se=$this->includeOnce("$Ia.php");if(is_array($se)){foreach($se
as$w=>$yh)$Ah[is_object($yh)?get_class($yh):$w]=$yh;}else$this->error
.=sprintf('%s must <a%s>return an array</a>.',"<b>$Ia.php</b>",$be)."<br>";}foreach(get_declared_classes()as$eb){if(!$Ah[$eb]&&(preg_match('~^Adminer\w~i',$eb)||is_subclass_of($eb,'Adminer\Plugin'))){$ei=new
\ReflectionClass($eb);$ub=$ei->getConstructor();if($ub&&$ub->getNumberOfRequiredParameters())$this->error
.=sprintf('<a%s>Configure</a> %s in %s.',$be,"<b>$eb</b>","<b>$Ia.php</b>")."<br>";else$Ah[$eb]=new$eb;}}}$He=array_filter($Ah,function($yh){return!is_object($yh);});if($He){$this->error
.=sprintf('Every plugin must <a%s>be an object</a>.',$be)."<br>";$Ah=array_diff_key($Ah,$He);}$this->drivers=array_diff_key(SqlDriver::$drivers,$qc);$this->plugins=$Ah;$ha=new
Adminer;$Ah[]=$ha;$ei=new
\ReflectionObject($ha);foreach($ei->getMethods()as$Vf){foreach($Ah
as$yh){$C=$Vf->getName();if(method_exists($yh,$C))$this->hooks[$C][]=$yh;}}}function
includeOnce($n){return
include_once"./$n";}static
function
checksum($n){$id=str_replace("\r","",file_get_contents($n));$id=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$id);return
dechex(crc32($id));}function
checksums(){$kd=array_values($this->driverFiles);foreach($this->plugins
as$yh){$ei=new
\ReflectionObject($yh);$kd[]=$ei->getFileName();}$J=array();foreach($kd
as$n)$J[basename($n,'.php')]=self::checksum($n);return$J;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'ed1ef78f','before-unload'=>'2a613523','config'=>'722eb4af','dark-switcher'=>'3d490dea','database-hide'=>'e304a899','designs'=>'d1515f34','dump-alter'=>'896b579e','dump-bz2'=>'f0d0e336','dump-date'=>'adc7f1c7','dump-json'=>'767dd321','dump-xml'=>'4fc3cd60','dump-zip'=>'93817d96','edit-foreign'=>'72ad1562','edit-textarea'=>'a24c3cc','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'96ee8718','file-upload'=>'10add0e8','foreign-system'=>'ebb4c654','frames'=>'b0e1d11a','highlight-codemirror'=>'f4baf411','highlight-monaco'=>'edd1b0af','highlight-prism'=>'267948e5','import-csv'=>'d429c77','login-ip'=>'4d174fea','login-otp'=>'5b5a68af','login-passkey'=>'f69f2f06','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'6ed147bc','login-table'=>'811f8cef','menu-links'=>'7f3d5020','remote-color'=>'86a39047','row-numbers'=>'eec8698c','select-email'=>'f84fbd2c','select-image'=>'f55c0231','slugify'=>'dec64713','sql-gemini'=>'c60ab309','sql-log'=>'8e435000','table-indexes-structure'=>'a90cc0c9','table-structure'=>'a8458e02','tables-filter'=>'ec2bcd6e','timeout'=>'97321caf','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'b0f6631c','elastic'=>'27503b8b','firebird'=>'5499d1a','igdb'=>'59055fd3','imap'=>'ac143217','mongo'=>'c3b8f5a4','redis'=>'ba56e72e','simpledb'=>'92f050ad',);}function
__call($C,array$fh){$ta=array();foreach($fh
as$w=>$X)$ta[]=&$fh[$w];$J=null;foreach($this->hooks[$C]as$yh){$Y=call_user_func_array(array($yh,$C),$ta);if($Y!==null){if(!self::$append[$C])return$Y;$J=$Y+(array)$J;}}return$J;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($t,$tg=null){$ta=func_get_args();$ta[0]=idx($this->translations[LANG],$t)?:$t;return
call_user_func_array('Adminer\lang_format',$ta);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($qh){$this->password_hash=$qh;}function
description(){return'Require a password verified by Adminer';}function
credentials(){$F=get_password();return
array(SERVER,$_GET["username"],($this->passwordMatches($F)&&!password_required()?"":$F));}function
login($tf,$F){if($this->passwordMatches($F))return
true;}protected
function
passwordMatches($F){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($F),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\mysqli{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach($N,$V,$F){mysqli_report(MYSQLI_REPORT_OFF);list($ge,$Bh)=host_port($N);$cj=adminer()->connectSsl();$Ak=($cj&&($cj['key']||$cj['cert']||$cj['ca']||isset($cj['verify'])));if($Ak)$this->ssl_set($cj['key'],$cj['cert'],$cj['ca'],'','');$J=@$this->real_connect(($N!=""?$ge:ini_get("mysqli.default_host")),($N.$V!=""?$V:ini_get("mysqli.default_user")),($N.$V.$F!=""?$F:ini_get("mysqli.default_pw")),null,(is_numeric($Bh)?intval($Bh):ini_get("mysqli.default_port")),(is_numeric($Bh)?null:$Bh),($Ak?($cj['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($J?'':$this->error);}function
set_charset($Va){if(parent::set_charset($Va))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $Va");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($Q){return"'".$this->escape_string($Q)."'";}function
inTransaction(){return
false;}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach($N,$V,$F){if(ini_bool("mysql.allow_local_infile"))return
sprintf('Disable %s or enable the %s or %s extension.',"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$this->link=@mysql_connect(($N!=""?$N:ini_get("mysql.default_host")),($N.$V!=""?$V:ini_get("mysql.default_user")),($N.$V.$F!=""?$F:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($Va){return
mysql_set_charset($Va,$this->link)||mysql_set_charset('utf8',$this->link);}function
quote($Q){return"'".mysql_real_escape_string($Q,$this->link)."'";}function
select_db($Ob){return
mysql_select_db($Ob,$this->link);}function
query($H,$kk=false){$I=@($kk?mysql_unbuffered_query($H,$this->link):mysql_query($H,$this->link));$this->error="";if(!$I){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($I===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($I);}}class
Result{var$num_rows;private$result;private$offset=0;function
__construct($I){$this->result=$I;$this->num_rows=mysql_num_rows($I);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_field(){$J=mysql_fetch_field($this->result,$this->offset++);$J->orgtable=$J->table;$J->charsetnr=($J->blob?63:0);return$J;}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach($N,$V,$F){$Ng=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);if(isset($_GET["select"]))$Ng[\PDO::MYSQL_ATTR_MULTI_STATEMENTS]=false;$cj=adminer()->connectSsl();if($cj){if($cj['key'])$Ng[\PDO::MYSQL_ATTR_SSL_KEY]=$cj['key'];if($cj['cert'])$Ng[\PDO::MYSQL_ATTR_SSL_CERT]=$cj['cert'];if($cj['ca'])$Ng[\PDO::MYSQL_ATTR_SSL_CA]=$cj['ca'];if(isset($cj['verify']))$Ng[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$cj['verify'];}list($ge,$Bh)=host_port($N);return$this->dsn("mysql:charset=utf8".($ge!=""?";host=$ge":'').($Bh?(is_numeric($Bh)?";port=":";unix_socket=").$Bh:""),$V,$F,$Ng);}function
set_charset($Va){return$this->query("SET NAMES $Va");}function
select_db($Ob){return$this->query("USE ".idf_escape($Ob));}function
query($H,$kk=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$kk);return
parent::query($H,$kk);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");var$partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");static
function
connect($N,$V,$F){$e=parent::connect($N,$V,$F);if(is_string($e)){if(function_exists('iconv')&&!is_utf8($e)&&strlen($vi=iconv("windows-1252","utf-8//IGNORE",$e))>strlen($e))$e=$vi;return$e;}$e->set_charset(charset($e));$e->query("SET sql_quote_show_create = 1, autocommit = 1");$e->flavor=(preg_match('~MariaDB~',$e->server_info)?'maria':'mysql');add_driver(DRIVER,($e->flavor=='maria'?"MariaDB":"MySQL"));return$e;}function
__construct(Db$e){parent::__construct($e);$this->types=array('Numbers'=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),'Date and time'=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),'Strings'=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),'Lists'=>array("enum"=>65535,"set"=>64),'Binary'=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),'Geometry'=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$e))$this->types['Strings']["json"]=4294967295;if(min_version('',10.7,$e)){$this->types['Strings']["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$e)){$this->types['Network']["inet6"]=39;if(min_version('','10.10',$e))$this->types['Network']["inet4"]=15;}if(min_version(9,11.7,$e))$this->types['Numbers']["vector"]=16383;if(min_version(5.7,10.2,$e))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$l){return(preg_match("~binary~",$l["type"])?"<code class='jush-sql'>UNHEX</code>":($l["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):($l["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geom|point|linestring|polygon~",$l["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($R,array$O){return($O?parent::insert($R,$O):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,array$L,array$Lh){$d=array_keys(reset($L));$Ih="INSERT INTO ".table($R)." (".implode(", ",$d).") VALUES\n";$Hk=array();foreach($d
as$w)$Hk[$w]="$w = VALUES($w)";$kj="\nON DUPLICATE KEY UPDATE ".implode(", ",$Hk);$Hk=array();$x=0;foreach($L
as$O){$Y="(".implode(", ",$O).")";if($Hk&&(strlen($Ih)+$x+strlen($Y)+strlen($kj)>1e6)){if(!queries($Ih.implode(",\n",$Hk).$kj))return
false;$Hk=array();$x=0;}$Hk[]=$Y;$x+=strlen($Y)+2;}return
queries($Ih.implode(",\n",$Hk).$kj);}function
slowQuery($H,$Mj){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$Mj FOR $H";elseif(preg_match('~^(SELECT\b)(.+)~is',$H,$A))return"$A[1] /*+ MAX_EXECUTION_TIME(".($Mj*1000).") */ $A[2]";}}function
convertColumn($t,array$l){if(preg_match("~binary~",$l["type"]))return"HEX($t)";if($l["type"]=="bit")return"BIN($t + 0)";if($l["type"]=="vector")return($this->conn->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."($t)";if(preg_match("~geom|point|linestring|polygon~",$l["type"]))return(min_version(8)?"ST_":"")."AsWKT($t)";return"";}function
convertSearch($t,array$X,array$l){return($this->convertColumn($t,$l)?:(preg_match('~'.text_type().'~',$l["type"])&&!preg_match("~^utf8~",$l["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($t USING ".charset($this->conn).")":$t));}function
typeName(\stdClass$l){$jk=array("decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",);$J=idx($jk,$l->type,"");return
parent::typeName($l)?:($l->charsetnr==63?str_replace(array("text","varchar","char"),array("blob","varbinary","binary"),$J):$J);}function
quoteBinary($vi){return"X".q(bin2hex($vi));}function
warnings(){$I=$this->conn->query("SHOW WARNINGS");if($I&&$I->num_rows){ob_start();print_select_result($I);return
ob_get_clean();}}function
tableHelp($C,$Qe=false){$vf=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($vf?"$C-table/":str_replace("_","-",$C)."-table.html"));if(DB=="sys")return($vf?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$C)).".html"));if(DB=="mysql")return($vf?"mysql$C-table/":"system-schema.html");}function
partitionsInfo($R){$zd="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$I=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $zd ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$K=($I?$I->fetch_row():null);if(!$K)return
array();$J=array();list($J["partition_by"],$J["partition"],$J["partitions"])=$K;$nh=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $zd AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$J["partition_names"]=array_keys($nh);$J["partition_values"]=array_values($nh);return$J;}function
hasCStyleEscapes(){static$Qa;if($Qa===null){$aj=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Qa=(strpos($aj,'NO_BACKSLASH_ESCAPES')===false);}return$Qa;}function
lineComment(){return"#|-- ";}function
engines(){$J=array();foreach(get_rows("SHOW ENGINES")as$K){if(preg_match("~YES|DEFAULT~",$K["Support"]))$J[]=$K["Engine"];}return$J;}function
indexAlgorithms(array$qj){return(preg_match('~^(MEMORY|NDB)$~',$qj["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($t){return"`".str_replace("`","``",$t)."`";}function
table($t){return
idf_escape($t);}function
get_databases($rd){$J=get_session("dbs");if($J===null){$H="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$dj=microtime(true);$J=($rd?slow_query($H):get_vals($H));if(microtime(true)-$dj>0.1){restart_session();set_session("dbs",$J);stop_session();}}return$J;}function
limit($H,$Z,$y,$zg=0,$Hi=" "){return" $H$Z".($y?$Hi."LIMIT $y".($zg?" OFFSET $zg":""):"");}function
limit1($R,$H,$Z,$Hi="\n"){return
limit($H,$Z,1,0,$Hi);}function
db_collation($i,array$jb){$J=null;$g=get_val("SHOW CREATE DATABASE ".idf_escape($i),1);if(preg_match('~ COLLATE ([^ ]+)~',$g,$A))$J=$A[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$g,$A))$J=$jb[$A[1]][-1];return$J;}function
logged_user(){return
get_val("SELECT USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$h){$J=array();foreach($h
as$i)$J[$i]=count(get_vals("SHOW TABLES IN ".idf_escape($i)));return$J;}function
table_status($C="",$cd=false){$J=array();foreach(get_rows($cd?"SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($C!=""?"AND TABLE_NAME = ".q($C):"ORDER BY Name"):"SHOW TABLE STATUS".($C!=""?" LIKE ".q(addcslashes($C,"%_\\")):""))as$K){if($K["Engine"]=="InnoDB")$K["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$K["Comment"]);if(!isset($K["Engine"]))$K["Comment"]="";if($C!="")$K["Name"]=$C;$J[$K["Name"]]=$K;}return$J;}function
is_view(array$S){return$S["Engine"]===null;}function
fk_support(array$S){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$S["Engine"]);}function
parse_type($Ad){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$Ad,$A);return
array($A[1],$A[2],ltrim($A[3].$A[4]));}function
fields($R){$vf=(connection()->flavor=='maria');$J=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$K){$l=$K["COLUMN_NAME"];$U=$K["COLUMN_TYPE"];$Ed=$K["GENERATION_EXPRESSION"];$Zc=$K["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Zc,$Dd);list($ik,$x,$qk)=parse_type($U);$j=$K["COLUMN_DEFAULT"];if($j!=""){$Pe=preg_match('~text|json~',$ik);if(!$vf&&$Pe)$j=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($j));if($vf||$Pe){$j=($j=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($A){return
stripslashes(str_replace("''","'",$A[1]));},$j));}if(!$vf&&preg_match('~binary~',$ik)&&preg_match('~^0x(\w*)$~',$j,$A))$j=pack("H*",$A[1]);}$J[$l]=array("field"=>$l,"full_type"=>$U,"type"=>$ik,"length"=>$x,"unsigned"=>$qk,"default"=>($Dd?($vf?$Ed:stripslashes($Ed)):$j),"null"=>($K["IS_NULLABLE"]=="YES"),"auto_increment"=>($Zc=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$Zc,$A)?$A[1]:""),"collation"=>$K["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$K[PRIVILEGES],where,order")),"comment"=>$K["COLUMN_COMMENT"],"primary"=>($K["COLUMN_KEY"]=="PRI"),"generated"=>($Dd[1]=="PERSISTENT"?"STORED":$Dd[1]),);}return$J;}function
indexes($R,$f=null){$J=array();foreach(get_rows("SHOW INDEX FROM ".table($R),$f)as$K){$C=$K["Key_name"];$J[$C]["type"]=($C=="PRIMARY"?"PRIMARY":($K["Index_type"]=="FULLTEXT"?"FULLTEXT":($K["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$K["Index_type"])?$K["Index_type"]:"INDEX"):"UNIQUE")));$J[$C]["columns"][]=$K["Column_name"];$J[$C]["lengths"][]=($K["Index_type"]=="SPATIAL"?null:$K["Sub_part"]);$J[$C]["descs"][]=null;$J[$C]["algorithm"]=$K["Index_type"];}return$J;}function
foreign_keys($R){static$uh='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$J=array();$Cb=get_val("SHOW CREATE TABLE ".table($R),1);if($Cb){preg_match_all("~CONSTRAINT ($uh) FOREIGN KEY ?\\(((?:$uh,? ?)+)\\) REFERENCES ($uh)(?:\\.($uh))? \\(((?:$uh,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$Cb,$xf,PREG_SET_ORDER);foreach($xf
as$A){preg_match_all("~$uh~",$A[2],$Ui);preg_match_all("~$uh~",$A[5],$Cj);$J[idf_unescape($A[1])]=array("db"=>idf_unescape($A[4]!=""?$A[3]:$A[4]),"table"=>idf_unescape($A[4]!=""?$A[4]:$A[3]),"source"=>array_map('Adminer\idf_unescape',$Ui[0]),"target"=>array_map('Adminer\idf_unescape',$Cj[0]),"on_delete"=>($A[6]?:"RESTRICT"),"on_update"=>($A[7]?:"RESTRICT"),);}}return$J;}function
view($C){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($C),1)));}function
collations(){$J=array();foreach(get_rows("SHOW COLLATION")as$K){if($K["Default"])$J[$K["Charset"]][-1]=$K["Collation"];else$J[$K["Charset"]][]=$K["Collation"];}ksort($J);foreach($J
as$w=>$X)sort($J[$w]);return$J;}function
information_schema($i,$xi=""){return($i=="information_schema")||(min_version(5.5)&&$i=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($i,$ib){return
queries("CREATE DATABASE ".idf_escape($i).($ib?" COLLATE ".q($ib):""));}function
drop_databases(array$h){$J=apply_queries("DROP DATABASE",$h,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$J;}function
rename_database($C,$ib){$J=false;if(create_database($C,$ib)){$T=array();$Mk=array();foreach(tables_list()as$R=>$U){if($U=='VIEW')$Mk[]=$R;else$T[]=$R;}$J=(!$T&&!$Mk)||move_tables($T,$Mk,$C);drop_databases($J?array(DB):array());}return$J;}function
auto_increment(){$_a=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$u){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$u["columns"],true)){$_a="";break;}if($u["type"]=="PRIMARY")$_a=" UNIQUE";}}return" AUTO_INCREMENT$_a";}function
alter_table($R,$C,array$m,array$td,$nb,$Cc,$ib,$za,$mh){$pa=array();foreach($m
as$l){if($l[1]){$j=$l[1][3];if(preg_match('~ GENERATED~',$j)){$l[1][3]=(connection()->flavor=='maria'?"":$l[1][2]);$l[1][2]=$j;}$pa[]=($R!=""?($l[0]!=""?"CHANGE ".idf_escape($l[0]):"ADD"):" ")." ".implode($l[1]).($R!=""?$l[2]:"");}else$pa[]="DROP ".idf_escape($l[0]);}$pa=array_merge($pa,$td);$P=($nb!==null?" COMMENT=".q($nb):"").($Cc?" ENGINE=".q($Cc):"").($ib?" COLLATE ".q($ib):"").($za!=""?" AUTO_INCREMENT=$za":"");if($mh){$nh=array();if($mh["partition_by"]=='RANGE'||$mh["partition_by"]=='LIST'){foreach($mh["partition_names"]as$w=>$X){$Y=$mh["partition_values"][$w];$nh[]="\n  PARTITION ".idf_escape($X)." VALUES ".($mh["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$P
.="\nPARTITION BY $mh[partition_by]($mh[partition])";if($nh)$P
.=" (".implode(",",$nh)."\n)";elseif($mh["partitions"])$P
.=" PARTITIONS ".(+$mh["partitions"]);}elseif($mh===null)$P
.="\nREMOVE PARTITIONING";if($R=="")return
queries("CREATE TABLE ".table($C)." (\n".implode(",\n",$pa)."\n)$P");if($R!=$C)$pa[]="RENAME TO ".table($C);if($P)$pa[]=ltrim($P);return($pa?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$pa)):true);}function
alter_indexes($R,$pa){$Ta=array();foreach($pa
as$X)$Ta[]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$Ta));}function
truncate_tables(array$T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views(array$Mk){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Mk)));}function
drop_tables(array$T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables(array$T,array$Mk,$Cj){$ii=array();foreach($T
as$R)$ii[]=table($R)." TO ".idf_escape($Cj).".".table($R);if(!$ii||queries("RENAME TABLE ".implode(", ",$ii))){$Xb=array();foreach($Mk
as$R)$Xb[table($R)]=view($R);connection()->select_db($Cj);$i=idf_escape(DB);foreach($Xb
as$C=>$Lk){if(!queries("CREATE VIEW $C AS ".str_replace(" $i."," ",$Lk["select"]))||!queries("DROP VIEW $i.$C"))return
false;}return
true;}return
false;}function
copy_tables(array$T,array$Mk,$Cj){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$C=($Cj==DB?table("copy_$R"):idf_escape($Cj).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $C"))||!queries("CREATE TABLE $C LIKE ".table($R))||!queries("INSERT INTO $C SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){$bk=$K["Trigger"];list($Lc,$vg)=trigger_event($K);if(!queries("CREATE TRIGGER ".($Cj==DB?idf_escape("copy_$bk"):idf_escape($Cj).".".idf_escape($bk))." $K[Timing] $Lc".($vg!=""?" $vg":"")." ON $C FOR EACH ROW\n$K[Statement];"))return
false;}}foreach($Mk
as$R){$C=($Cj==DB?table("copy_$R"):idf_escape($Cj).".".table($R));$Lk=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $C"))||!queries("CREATE VIEW $C AS $Lk[select]"))return
false;}return
true;}function
trigger_event(array$K){$Nc=explode(",",$K["Event"]);$J=array();foreach(array("DELETE","INSERT","UPDATE")as$Lc){if(in_array($Lc,$Nc))$J[]=$Lc;}$J=implode(" OR ",$J);if(in_array("UPDATE",$Nc)&&min_version('','12.0.1')&&preg_match('~\s(?:BEFORE|AFTER)\s+(.+?)\s+ON\s~is',get_val("SHOW CREATE TRIGGER ".idf_escape($K["Trigger"]),2),$A)&&preg_match('~\bOF\s+(.+)~is',$A[1],$vg))return
array("$J OF",$vg[1]);return
array($J,"");}function
trigger($C,$R){if($C=="")return
array();$L=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($C));$J=reset($L);if($J)list($J["Event"],$J["Of"])=trigger_event($J);return$J;}function
triggers($R){$J=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){list($Lc)=trigger_event($K);$J[$K["Trigger"]]=array($K["Timing"],$Lc);}return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>(min_version('','12.0.1')?array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",):array("INSERT","UPDATE","DELETE")),"Type"=>array("FOR EACH ROW"),);}function
routine($C,$U){$L=get_rows("SELECT PARAMETER_NAME, DTD_IDENTIFIER, PARAMETER_MODE, COLLATION_NAME
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND SPECIFIC_NAME = ".q($C)."
ORDER BY ORDINAL_POSITION");$m=array();foreach($L
as$K){$Ad=$K["DTD_IDENTIFIER"];list($ik,$x,$qk)=parse_type($Ad);$m[]=array("field"=>$K["PARAMETER_NAME"],"type"=>$ik,"length"=>$x,"unsigned"=>$qk,"null"=>true,"full_type"=>$Ad,"inout"=>($U=="FUNCTION"?"":$K["PARAMETER_MODE"]),"collation"=>$K["COLLATION_NAME"],);}$J=connection()->query("SELECT
	ROUTINE_COMMENT comment,
	CONCAT(IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC\\n', ''), IF(SQL_DATA_ACCESS != 'CONTAINS SQL', CONCAT(SQL_DATA_ACCESS, '\\n'), ''), ROUTINE_DEFINITION) definition,
	'SQL' language
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND ROUTINE_NAME = ".q($C))->fetch_assoc();if($m&&$m[0]['field']=='')$J['returns']=array_shift($m);$J['fields']=$m;return$J;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return
array();}function
routine_id($C,array$K){return
idf_escape($C);}function
last_id($I){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$e,$H){return$e->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$H);}function
found_rows(array$S,array$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$za,$ij){$J=get_val("SHOW CREATE TABLE ".table($R),1);if(!$za)$J=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$J);return$J;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($Ob,$ij=""){$C=idf_escape($Ob);$J="";if(preg_match('~CREATE~',$ij)&&($g=get_val("SHOW CREATE DATABASE $C",1))){set_utf8mb4($g);if($ij=="DROP+CREATE")$J="DROP DATABASE IF EXISTS $C;\n";$J
.="$g;\n";}return$J."USE $C";}function
trigger_sql($R){$J="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")),null,"-- ")as$K){list($K["Event"],$K["Of"])=trigger_event($K);$J
.="\n".create_trigger(" ON ".table($K["Table"]),$K+array("Type"=>"FOR EACH ROW")).";\n";}return$J;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$l){return
driver()->convertColumn(idf_escape($l["field"]),$l);}function
unconvert_field(array$l,$J){if(preg_match("~binary~",$l["type"]))$J="UNHEX($J)";if($l["type"]=="bit")$J="CONVERT(b$J, UNSIGNED)";if($l["type"]=="vector")$J=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($J)";if(preg_match("~geom|point|linestring|polygon~",$l["type"])){$Ih=(min_version(8)?"ST_":"");$J=$Ih."GeomFromText($J, $Ih"."SRID($l[field]))";}return$J;}function
support($dd){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').(min_version(8,99)?'|fast_status':'').')$~',$dd);}function
kill_process($s){return
queries("KILL ".number($s));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types($Yc=false){return
array();}function
type_values($s){return"";}function
type_definition($s){return
array("kind"=>"","definition"=>"");}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($xi,$f=null){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));function
page_header($Oj,$k="",$Oa=array(),$Pj=""){page_headers();if(is_ajax()&&$k){page_messages($k);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$Qj=$Oj.($Pj!=""?": $Pj":"");$Rj=strip_tags($Qj.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'en\' dir=\'ltr\' class=\'ltr nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$Rj,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.0.1"),'">
';$Gb=adminer()->css();if(is_int(key($Gb)))$Gb=array_fill_keys($Gb,'light');$Td=in_array('light',$Gb)||in_array('',$Gb);$Rd=in_array('dark',$Gb)||in_array('',$Gb);$Kb=($Td?($Rd?null:false):($Rd?:null));$Lf=" media='(prefers-color-scheme: dark)'";if($Kb!==false)echo"<link rel='stylesheet'".($Kb?"":$Lf)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.0.1")."'>\n";echo"<meta name='color-scheme' content='".($Kb===null?"light dark":($Kb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.0.1");if(adminer()->head($Kb))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=6.0.1")."'>\n";foreach($Gb
as$wk=>$Zf){$b=($Zf=='dark'&&!$Kb?$Lf:($Zf=='light'&&$Rd?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$b href='".h($wk)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape('You are offline.')."';
const thousandsSeparator = '".js_escape(',')."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".'Menu'."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Oa!==null){$z=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($z?:".").'">'.get_driver(DRIVER).'</a> » ';$z=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$N=adminer()->serverName(SERVER);$N=($N!=""?$N:'Server');if($Oa===false)echo"$N\n";else{echo"<a href='".h($z.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$N</a> » ";if($_GET["ns"]!=""||(DB!=""&&is_array($Oa)))echo'<a href="'.h($z."&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"")).'">'.h(DB).'</a> » ';if(is_array($Oa)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> » ';foreach($Oa
as$w=>$X){$Zb=(is_array($X)?$X[1]:h($X));if($Zb!="")echo"<a href='".h(ME."$w=").url_escape(is_array($X)?$X[0]:$X)."'>$Zb</a> » ";}}echo"$Oj\n";}}echo"<h2>$Qj</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($k);$h=&get_session("dbs");if(DB!=""&&$h&&!in_array(DB,$h,true))$h=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Fb){$Xd=array();foreach($Fb
as$w=>$X)$Xd[]="$w $X";header("Content-Security-Policy: ".implode("; ",$Xd));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$Bk=array();foreach(array_keys(adminer()->css())as$wk)$Bk[preg_replace('~\?.*~','',$wk)]=true;$J=array();foreach(array("adminer.css","adminer-dark.css")as$n){if($Bk[$n]&&file_exists($n)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($n),$A);$J[$n]=array((string)$A[1],Plugins::checksum($n));}}return$J;}function
official_design_checksums(){return
array('adminer-border/adminer-dark.css'=>'b2527e3','adminer-border/adminer.css'=>'430977ad','adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'1f626deb','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'e1ac813d','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'5ab360e7','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'ecb9bd1e','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$qg;if(!$qg)$qg=base64_encode(rand_string());return$qg;}function
page_messages($k){$vk=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$Rf=idx($_SESSION["messages"],$vk);if($Rf){echo"<div class='message'>".implode("</div>\n<div class='message'>",$Rf)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$vk]);}if($k)echo"<div class='error'>$k</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($Yf=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($Yf);echo"</div>\n";if($Yf!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="Username">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'Logout\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($fg){while($fg>=2147483648)$fg-=4294967296;while($fg<=-2147483649)$fg+=4294967296;return(int)$fg;}function
long2str(array$W,$Ok){$vi='';foreach($W
as$X)$vi
.=pack('V',$X);if($Ok)return
substr($vi,0,end($W));return$vi;}function
str2long($vi,$Ok){$W=array_values(unpack('V*',str_pad($vi,4*ceil(strlen($vi)/4),"\0")));if($Ok)$W[]=strlen($vi);return$W;}function
xxtea_mx($Yk,$Xk,$lj,$Ve){return
int32((($Yk>>5&0x7FFFFFF)^$Xk<<2)+(($Xk>>3&0x1FFFFFFF)^$Yk<<4))^int32(($lj^$Xk)+($Ve^$Yk));}function
encrypt_string($gj,$w){if($gj=="")return"";$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($gj,true);$fg=count($W)-1;$Yk=$W[$fg];$Xk=$W[0];$Th=floor(6+52/($fg+1));$lj=0;while($Th-->0){$lj=int32($lj+0x9E3779B9);$vc=$lj>>2&3;for($ch=0;$ch<$fg;$ch++){$Xk=$W[$ch+1];$eg=xxtea_mx($Yk,$Xk,$lj,$w[$ch&3^$vc]);$Yk=int32($W[$ch]+$eg);$W[$ch]=$Yk;}$Xk=$W[0];$eg=xxtea_mx($Yk,$Xk,$lj,$w[$ch&3^$vc]);$Yk=int32($W[$fg]+$eg);$W[$fg]=$Yk;}return
long2str($W,false);}function
decrypt_string($gj,$w){if($gj=="")return"";if(!$w)return
false;$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($gj,false);$fg=count($W)-1;$Yk=$W[$fg];$Xk=$W[0];$Th=floor(6+52/($fg+1));$lj=int32($Th*0x9E3779B9);while($lj){$vc=$lj>>2&3;for($ch=$fg;$ch>0;$ch--){$Yk=$W[$ch-1];$eg=xxtea_mx($Yk,$Xk,$lj,$w[$ch&3^$vc]);$Xk=int32($W[$ch]-$eg);$W[$ch]=$Xk;}$Yk=$W[$fg];$eg=xxtea_mx($Yk,$Xk,$lj,$w[$ch&3^$vc]);$Xk=int32($W[0]-$eg);$W[0]=$Xk;$lj=int32($lj-0x9E3779B9);}return
long2str($W,true);}$wh=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($w)=explode(":",$X);$wh[$w]=$X;}}function
add_invalid_login(){$Ga=get_temp_dir()."/adminer-invalid";foreach(glob("$Ga*")?:array($Ga)as$n){$p=file_open_lock($n);if($p)break;}if(!$p)$p=file_open_lock("$Ga-".rand_string());if(!$p)return;$Je=json_decode(stream_get_contents($p),true);$Lj=time();if($Je){foreach($Je
as$Ke=>$X){if($X[0]<$Lj)unset($Je[$Ke]);}}$He=&$Je[adminer()->bruteForceKey()];if(!$He)$He=array($Lj+30*60,0);$He[1]++;file_write_unlock($p,json_encode($Je));}function
check_invalid_login(array&$wh){$Je=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$n){$p=file_open_lock($n);if($p){$Je=json_decode(stream_get_contents($p),true);file_unlock($p);break;}}$w=adminer()->bruteForceKey();$He=idx($Je,$w,array());$pg=($He[1]>29?$He[0]-time():0);if($pg>0){$k=lang_format(array('Too many unsuccessful logins, try again in %d minute.','Too many unsuccessful logins, try again in %d minutes.'),ceil($pg/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$w==$_SERVER["REMOTE_ADDR"])$k
.='<br>'.sprintf('Use the %s <a%s>plugin</a> if Adminer runs behind a reverse proxy.','<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($k,$wh,false);}}function
password_required(){static$J;if($J===null){$J=(bool)get_session("password_required");if(!$J){$Eb=adminer()->credentials();$J=!is_object(Driver::connect($Eb[0],$Eb[1],""));if($J)set_session("password_required",true);}}return$J;}function
require_password_link($F){$bg="<a href='https://www.adminer.org/password/'".target_blank().">".'More options'."</a>";if(!function_exists('password_hash'))return" $bg";$zh=($F!==null?$F:base64_encode(substr(pack("H*",rand_string()),0,12)));$Wd=password_hash($zh,PASSWORD_DEFAULT);$n="adminer-plugins.php";$Sc=file_exists("adminer-plugins.php");if($Sc)$Fe=($F!==null?sprintf('Add this line to %s to require the entered password:',"<b>$n</b>"):sprintf('Add this line to %s to require the password %s:',"<b>$n</b>","<b>$zh</b>"));else{$n="<button name='password_less' value='".h($Wd)."' class='link'>$n</button>";$Fe=($F!==null?sprintf('Save %s next to Adminer to require the entered password:',$n):sprintf('Save %s next to Adminer to require the password %s:',$n,"<b>$zh</b>"));}$nf="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($Wd)."'</span>),";$J="<p>$Fe
<pre><code class='jush'>".($Sc?$nf:"&lt;?php\n<a>return</a> <a>array</a>(\n$nf\n);")."</code></pre>
<p>$bg
";return" <a href='#password-less' class='toggle'>".'Require a password.'."</a>
<div id='password-less' class='hidden'>".($Sc?$J:"<form action='' method='post'>\n".$J.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$ya=$_POST["auth"];if($ya&&verify_token()){session_regenerate_id();$Jk=$ya["driver"];$N=$ya["server"];$V=$ya["username"];$F=(string)$ya["password"];$i=$ya["db"];set_password($Jk,$N,$V,$F);$_SESSION["db"][$Jk][$N][$V][$i]=true;if($ya["permanent"]){$w=implode("-",array_map('base64_encode',array($Jk,$N,$V,$i)));$Nh=adminer()->permanentLogin(true);$wh[$w]="$w:".base64_encode($Nh?encrypt_string($F,$Nh):"");cookie("adminer_permanent",implode(" ",$wh));}if(!array_diff(array_keys($_POST),array("auth","token"))||$Jk!=DRIVER||$N!=SERVER||$V!==$_GET["username"]||$i!=DB)redirect(auth_url($Jk,$N,$V,$i));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$w)set_session($w,null);unset_permanent($wh);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),'Logout successful.'.' '.'Thanks for using Adminer. Consider <a href="https://www.adminer.org/en/donation/">donating</a>.');}elseif($wh&&!$_SESSION["pwds"]){session_regenerate_id();$Nh=adminer()->permanentLogin();foreach($wh
as$w=>$X){list(,$db)=explode(":",$X);list($Jk,$N,$V,$i)=array_map('base64_decode',explode("-",$w));set_password($Jk,$N,$V,decrypt_string(base64_decode($db),$Nh));$_SESSION["db"][$Jk][$N][$V][$i]=true;}}function
unset_permanent(array&$wh){foreach($wh
as$w=>$X){list($Jk,$N,$V,$i)=array_map('base64_decode',explode("-",$w));if($Jk==DRIVER&&$N==SERVER&&$V==$_GET["username"]&&$i==DB)unset($wh[$w]);}cookie("adminer_permanent",implode(" ",$wh));}function
auth_error($k,array&$wh,$Ie=true){$Ki=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$Ki]||$_GET[$Ki])&&!$_SESSION["token"])$k='Session expired. Please log in again.';elseif($Ie&&($F=get_password())!==null){restart_session();add_invalid_login();if($F===false)$k
.=($k?'<br>':'').sprintf('Master password expired. <a href="https://www.adminer.org/en/extension/"%s>Implement</a> the %s method to make it permanent.',target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($wh);}}if(!$_COOKIE[$Ki]&&$_GET[$Ki]&&ini_bool("session.use_only_cookies"))$k='Session support must be enabled.';$fh=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$fh["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header('Login',$k,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth")))echo"<p class='message'>".'The action will be performed after successful login with the same credentials.'."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($wh);page_header('No extension',sprintf('None of the supported PHP extensions (%s) are available.',implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$e='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($wh);$Eb=adminer()->credentials();$e=Driver::connect($Eb[0],$Eb[1],$Eb[2]);if(is_object($e)){Db::$instance=$e;Driver::$instance=new
Driver($e);if($e->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$tf=null;if(!is_object($e)||($tf=adminer()->login($_GET["username"],get_password()))!==true){$k=(is_string($e)?nl_br(h($e)):(is_string($tf)?$tf:'Invalid credentials.')).(preg_match('~^ | $~',get_password())?'<br>'.'There is a space in the entered password, which might be the cause.':'');auth_error($k,$wh);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header('Logout','Invalid CSRF token. Submit the form again.');page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($ya&&$_POST["token"])$_POST["token"]=get_token();$k='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$k='Invalid CSRF token. Submit the form again.'.' '.'If you did not send this request from Adminer, close this page.';}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$k=sprintf('The POST data is too large. Reduce the data or increase the %s configuration directive.',"<b>post_max_size</b>");if(isset($_GET["sql"]))$k
.=' '.'You can upload a large SQL file via FTP and import it from the server.';}function
print_select_result($I,$f=null,array$Sg=array(),&$y=0){$pf=array();$v=array();$d=array();$Ma=array();$jk=array();$J=array();for($r=0;(!$y||$r<$y)&&($K=$I->fetch_row());$r++){if(!$r){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr>";for($Se=0;$Se<count($K);$Se++){$l=$I->fetch_field();$C=$l->name;$Rg=(isset($l->orgtable)?$l->orgtable:"");$Qg=(isset($l->orgname)?$l->orgname:$C);if($Sg&&JUSH=="sql")$pf[$Se]=($C=="table"?"table=":($C=="possible_keys"?"indexes=":null));elseif($Rg!=""){if(isset($l->table))$J[$l->table]=$Rg;if(!isset($v[$Rg])){$v[$Rg]=array();foreach(indexes($Rg,$f)as$u){if($u["type"]=="PRIMARY"){$v[$Rg]=array_flip($u["columns"]);break;}}$d[$Rg]=$v[$Rg];}if(isset($d[$Rg][$Qg])){unset($d[$Rg][$Qg]);$v[$Rg][$Qg]=$Se;$pf[$Se]=$Rg;}}if($l->charsetnr==63)$Ma[$Se]=true;$jk[$Se]=$l->type;echo"<th title='".h(trim(($Rg!=""?"$Rg.$Qg":($l->name!=$Qg?$Qg:""))." ".driver()->typeName($l)))."'>".h($C).($Sg?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($C),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}echo"<tbody>\n";}echo"<tr>";foreach($K
as$w=>$X){$z="";if(isset($pf[$w])&&!$d[$pf[$w]]){if($Sg&&JUSH=="sql"){$R=$K[array_search("table=",$pf)];$z=ME.$pf[$w].url_escape($Sg[$R]!=""?$Sg[$R]:$R);}else{$z=ME."edit=".url_escape($pf[$w]);foreach($v[$pf[$w]]as$gb=>$Se){if($K[$Se]===null){$z="";break;}$z
.="&where[".url_escape(bracket_escape($gb))."]=".url_escape($K[$Se]);}}}$l=array('type'=>($Ma[$w]?'blob':($jk[$w]==254?'char':'')),);$X=select_value($X,$z,$l,null);echo"<td".($jk[$w]<=9||$jk[$w]==246?" class='number'":"").">$X";}}$y=$r;echo($r?"</table>\n</div>":"<p class='message'>".'No rows.')."\n";return$J;}function
textarea($C,$Y,$L=10,$kb=80){echo"<textarea name='".h($C)."' rows='$L' cols='$kb' class='sqlarea jush-".JUSH."' spellcheck='false' wrap='off'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($b,array$Ng,$Y="",$xh=""){if($Ng&&$Y!=""&&!isset($Ng[$Y]))$Ng=array($Y=>$Y)+$Ng;$Bj=($Ng?"select":"input");return"<$Bj$b".($Ng?"><option value=''>$xh".optionlist($Ng,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$xh'>");}function
json_row($w,$X=null,$Kc=true){static$od=true;if($od)echo"{";if($w!=""){echo($od?"":",")."\n\t\"".addcslashes($w,"\r\n\t\"\\/").'": '.($X!==null?($Kc?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$od=false;}else{echo"\n}\n";$od=true;}}function
flat_collations(){$jb=collations();return(is_array(reset($jb))?call_user_func_array('array_merge',array_values($jb)):$jb);}function
edit_type($w,array$l,array$jb,array$vd=array(),array$ad=array()){$U=(string)$l["type"];echo"<td><select name='".h($w)."[type]' class='type' aria-labelledby='label-type'".on_help_value().">";if($U&&!array_key_exists($U,driver()->types())&&!isset($vd[$U])&&!in_array($U,$ad))$ad[]=$U;$hj=driver()->structuredTypes();if($vd)$hj['Foreign keys']=$vd;echo
optionlist(array_merge($ad,$hj),$U),"</select><td>","<input name='".h($w)."[length]' value='".h($l["length"])."' size='3'".(!$l["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($jb?"<input list='collations' name='".h($w)."[collation]'".option_types($U,'('.text_type().')$')." value='".h($l["collation"])."' placeholder='(".'collation'.")'>":''),(driver()->unsigned?"<select name='".h($w)."[unsigned]'".option_types($U,'^$|'.number_type()).'><option>'.optionlist(driver()->unsigned,$l["unsigned"]).'</select>':''),(isset($l['on_update'])?"<select name='".h($w)."[on_update]'".option_types($U,'timestamp|datetime').'>'.optionlist(array(""=>"(".'ON UPDATE'.")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$l["on_update"])?"CURRENT_TIMESTAMP":$l["on_update"])).'</select>':''),($vd?"<select name='".h($w)."[on_delete]'".option_types($U,'`')."><option value=''>(".'ON DELETE'.")".optionlist(explode("|",driver()->onActions),$l["on_delete"])."</select> ":" ");}function
option_types($U,$jk){return" data-types='".h($jk)."'".(preg_match("~$jk~",$U)?"":" class='hidden'");}function
process_length($x){$Fc=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$Fc(?:\\s*,\\s*$Fc)*+\\s*\\)?\\s*\$~",$x)&&preg_match_all("~$Fc~",$x,$xf)?"(".implode(",",$xf[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$x)));}function
process_in($X){$Fc=driver()->enumLength;if(preg_match("~^\\s*\\(?\\s*$Fc(?:\\s*,\\s*$Fc)*+\\s*\\)?\\s*\$~",$X)&&preg_match_all("~$Fc~",$X,$xf))return"(".implode(", ",$xf[0]).")";$J=array();foreach(explode(",",$X)as$Re)$J[]=q(trim($Re));return"(".implode(", ",$J).")";}function
process_type(array$l,$hb="COLLATE"){return" $l[type]".process_length($l["length"]).(preg_match(number_type(),$l["type"])&&in_array($l["unsigned"],driver()->unsigned)?" $l[unsigned]":"").(preg_match('~'.text_type().'~',$l["type"])&&$l["collation"]?" $hb ".(JUSH=="mssql"?$l["collation"]:q($l["collation"])):"");}function
process_field(array$l,array$hk){if($l["on_update"])$l["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$l["on_update"]);return
array(idf_escape(trim($l["field"])),process_type($hk),($l["null"]?" NULL":" NOT NULL"),default_value($l),(preg_match('~timestamp|datetime~',$l["type"])&&$l["on_update"]?" ON UPDATE $l[on_update]":""),(support("comment")&&$l["comment"]!=""?" COMMENT ".q($l["comment"]):""),($l["auto_increment"]?auto_increment():null),);}function
default_value(array$l){if($l["default"]===null)return"";$j=str_replace("\r","",$l["default"]);$Dd=$l["generated"];return(in_array($Dd,driver()->generated)?(JUSH=="mssql"?" AS ($j)".($Dd=="VIRTUAL"?"":" $Dd"):" GENERATED ALWAYS AS ($j) $Dd"):(preg_match('~^GENERATED ~i',$j)?" $j":" DEFAULT ".(preg_match('~char|binary|text|json|enum|set|String~',$l["type"])||preg_match('~^(?![a-z])~i',$j)?(JUSH=="sql"&&preg_match('~text|json~',$l["type"])?"(".q($j).")":q($j)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($j)":$j)))));}function
edit_fields(array$m,array$jb,$U="TABLE",array$vd=array()){$m=array_values($m);$Ub=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$ob=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?'Column name':'Parameter name'),"<td id='label-type'>".'Type'."<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<td id='label-length'>".'Length',"<td>".'Options';if($U=="TABLE")echo"<td id='label-null'>NULL\n","<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".'Auto Increment'."'>AI</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",)),"<td id='label-default'$Ub>".'Default value',(support("comment")?"<td id='label-comment'$ob>".'Comment':"");$df=!support("move_col");echo"<td>".icon("plus","add[".($df?count($m):0)."]","+",'Add next',($df?on('click','editingAddLastRow'):"")),"<tbody".on('click','editingClick').on('input','editingInput').on('keydown','editingKeydown').">\n";foreach($m
as$r=>$l){$r++;$Tg=$l[($_POST?"orig":"field")];$gc=(isset($_POST["add"][$r-1])||(isset($l["field"])&&!idx($_POST["drop_col"],$r)))&&(support("drop_col")||$Tg=="");echo"<tr".($gc?"":" hidden").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$r][inout]",explode("|",driver()->inout),$l["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",'Move')." ":"");if($gc)echo"<input name='fields[$r][field]' value='".h($l["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$r-1])?" autofocus":"").">";echo
input_hidden("fields[$r][orig]",$Tg);edit_type("fields[$r]",$l,$jb,$vd);if($U=="TABLE"){echo"<td><label class='block'>".checkbox("fields[$r][null]",1,$l["null"],"","","","label-null")."</label>","<td><label class='block'><input type='radio' name='auto_increment_col' value='$r'".($l["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$Ub>".(driver()->generated?html_select("fields[$r][generated]",array_merge(array("","DEFAULT"),driver()->generated),$l["generated"])." ":checkbox("fields[$r][generated]",1,$l["generated"],"","","","label-default"));$b=" name='fields[$r][default]' aria-labelledby='label-default'";$Y=h($l["default"]);echo(preg_match('~\n~',$l["default"])?"<textarea$b rows='2' cols='30' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");if(support("comment")){$b=" name='fields[$r][comment]' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'";echo"<td$ob>".adminer()->commentInput('COLUMN',$b,$l["comment"]);}}echo"<td>",(support("move_col")?icon("plus","add[$r]","+",'Add next')." ":""),($Tg==""||support("drop_col")?icon("cross","drop_col[$r]","x",'Remove'):"");}}function
process_fields(array&$m){if($_POST["add"]){$m=array_values($m);array_splice($m,key($_POST["add"]),0,array(array()));}return$_POST["add"]||$_POST["drop_col"];}function
drop_create($rc,$g,$sc,$Hj,$tc,$_,$Qf,$Of,$Pf,$Cg,$mg){if($_POST["drop"])query_redirect($rc,$_,$Qf);elseif($Cg=="")query_redirect($g,$_,$Pf);elseif(support("transaction_ddl")){driver()->begin();queries_redirect($_,$Of,queries($rc)&&queries($g)&&driver()->commit());driver()->rollback();}elseif($Cg!=$mg){$Db=queries($g);queries_redirect($_,$Of,$Db&&queries($rc));if($Db)queries($sc);}else
queries_redirect($_,$Of,queries($Hj)&&queries($tc)&&queries($rc)&&queries($g));}function
create_trigger($Eg,array$K){$Nj=" $K[Timing] $K[Event]".(preg_match('~ OF~',$K["Event"])?" $K[Of]":"");return"CREATE TRIGGER ".idf_escape($K["Trigger"]).(JUSH=="mssql"?$Eg.$Nj:$Nj.$Eg).rtrim(" $K[Type]\n$K[Statement]",";").";";}function
q_dollar($Q){$Yb='$$';while(strpos($Q.$Yb,$Yb)!=strlen($Q))$Yb='$_'.substr($Yb,1);return$Yb.$Q.$Yb;}function
routine_collate($ib){static$Wa=array();if($ib&&!$Wa){foreach(collations()as$Va=>$Gk){foreach((array)$Gk
as$X)$Wa[$X]=$Va;}}return($Wa[$ib]?"CHARACTER SET ".q($Wa[$ib])." ":"")."COLLATE";}function
create_routine($ri,array$K){$O=array();$m=(array)$K["fields"];ksort($m);foreach($m
as$l){if($l["field"]!="")$O[]=(preg_match("~^(".driver()->inout.")\$~",$l["inout"])?"$l[inout] ":"").idf_escape($l["field"]).process_type($l,routine_collate($l["collation"]));}$Wb=rtrim($K["definition"],";");return"CREATE $ri ".idf_escape(trim($K["name"]))." (".implode(", ",$O).")".($ri=="FUNCTION"?" RETURNS".process_type($K["returns"],routine_collate($K["returns"]["collation"])):"").($K["language"]?" LANGUAGE $K[language]":"").(JUSH=="pgsql"?" AS ".q_dollar("\n".trim($Wb)."\n"):"\n$Wb;");}function
remove_definer($H){return
preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~','`@`(%|\1)',logged_user()).'`~','\1',$H);}function
format_foreign_key(array$o){$i=$o["db"];$rg=$o["ns"];return" FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$o["source"])).") REFERENCES ".($i!=""&&$i!=$_GET["db"]?idf_escape($i).".":"").($rg!=""&&$rg!=$_GET["ns"]?idf_escape($rg).".":"").idf_escape($o["table"])." (".implode(", ",array_map('Adminer\idf_escape',$o["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$o["on_delete"])?" ON DELETE $o[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$o["on_update"])?" ON UPDATE $o[on_update]":"").($o["deferrable"]?" $o[deferrable]":"");}function
tar_file($n,$Sj){$J=pack("a100a8a8a8a12a12",$n,644,0,0,decoct($Sj->size),decoct(time()));$bb=8*32;for($r=0;$r<strlen($J);$r++)$bb+=ord($J[$r]);$J
.=sprintf("%06o",$bb)."\0 ";echo$J,str_repeat("\0",512-strlen($J));$Sj->send();echo
str_repeat("\0",511-($Sj->size+511)%512);}function
doc_link(array$th,$Ij="<sup>?</sup>"){$Ii=connection()->server_info;$Kk=preg_replace('~^(\d\.?\d).*~s','\1',$Ii);$xk=array('sql'=>"https://dev.mysql.com/doc/refman/$Kk/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$Kk)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://www.oracle.com/pls/topic/lookup?ctx=db".preg_replace('~^.* (\d+)\.(\d+)\.\d+\.\d+\.\d+.*~s','\1\2',$Ii)."&id=",);if(connection()->flavor=='maria'){$xk['sql']="https://mariadb.com/kb/en/";$th['sql']=(isset($th['mariadb'])?$th['mariadb']:str_replace(".html","/",$th['sql']));}return($th[JUSH]?"<a href='".h($xk[JUSH].$th[JUSH].(JUSH=='mssql'?"?view=sql-server-ver$Kk":""))."'".target_blank().">$Ij</a>":"");}function
db_size($i){if(!connection()->select_db($i))return"?";$J=0;foreach(table_status()as$S)$J+=$S["Data_length"]+$S["Index_length"];return
format_number($J);}function
set_utf8mb4($g){static$O=false;if(!$O&&preg_match('~\butf8mb4~i',$g)){$O=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'));if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!=""){header("HTTP/1.1 404 Not Found");page_header('Database'.": ".h(DB),'Invalid database.',true);}else{if(!isset($_GET["db"])&&support("single_db")){$h=adminer()->databases();if($h)redirect(ME."db=".url_escape($h[0]));}if($_POST["db"]&&!$k)queries_redirect(substr(ME,0,-1),'Databases have been dropped.',drop_databases($_POST["db"]));page_header('Select database',$k,false);echo"<p class='links'>\n";foreach(array('database'=>'Create database','privileges'=>'Privileges','processlist'=>'Process list','variables'=>'Variables','status'=>'Status',)as$w=>$X){if(support($w))echo"<a href='".h(ME)."$w='>$X</a>\n";}echo"<p>".sprintf('%s version: %s through PHP extension %s',get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".sprintf('Logged in as: %s',"<b>".h(logged_user())."</b>")."\n";$h=adminer()->databases();if($h){$yi=support("scheme");$jb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n","<thead><tr>".(support("database")?"<td class='hover'>":"")."<th".(JUSH!='mssql'?" aria-sort='ascending'":"").">".'Database'.(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".'Refresh'."</a>":"")."<td>".'Collation'."<td>".'Tables'."<td>".'Size'." - <a href='".h(ME)."dbsize=1'".on('click','ajaxSetHtml',ME."script=connect").">".'Compute'."</a>"."<tbody>\n";$h=($_GET["dbsize"]?count_tables($h):array_flip($h));foreach($h
as$i=>$T){$qi=h(preg_replace('~&db=[^&]*~','',ME))."db=".url_escape($i);$s=h("Db-".$i);echo"<tr>".(support("database")?"<td class='hover'>".checkbox("db[]",$i,in_array($i,(array)$_POST["db"]),"","","",$s):""),"<th><a href='$qi' id='$s'>".h($i)."</a>";$ib=h(db_collation($i,$jb));echo"<td>".(support("database")?"<a href='$qi".($yi?"&amp;ns=":"")."&amp;database=' title='".'Alter database'."'>$ib</a>":$ib),"<td align='right'><a href='$qi&amp;schema=' id='tables-".h($i)."' title='".'Database schema'."'>".($_GET["dbsize"]?$T:"?")."</a>","<td align='right' id='size-".h($i)."'>".($_GET["dbsize"]?db_size($i):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".'Selected'." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''".on('click','countDbs').">\n"."<input type='submit' name='drop' value='".'Drop'."'".confirm().">\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}$ha=adminer();$Ah=($ha
instanceof
Plugins?$ha->plugins:array());$qc=($ha
instanceof
Plugins?$ha->drivers:array());$dc=design_checksums();if($Ah||$qc||$dc){$cb=($ha
instanceof
Plugins?$ha->checksums():array());$wg=Plugins::officialChecksums();$tk=function($wk){return" (<a href='$wk'".target_blank()." class='update'>".VERSION."</a>)";};$_h=function($id)use($cb,$wg,$tk){return($cb[$id]&&$wg[$id]&&$cb[$id]!==$wg[$id]?$tk("https://www.adminer.org/plugins/?version=".VERSION):"");};echo"<div class='plugins'>\n","<h3>".'Loaded plugins'."</h3>\n<ul>\n";foreach($Ah
as$yh){$ei=new
\ReflectionObject($yh);$ac=(method_exists($yh,'description')?$yh->description():"");if(!$ac){if(preg_match('~^/[\s*]+(.+)~',$ei->getDocComment(),$A))$ac=$A[1];}$zi=(method_exists($yh,'screenshot')?$yh->screenshot():"");echo"<li><b>".get_class($yh)."</b>".h($ac?": $ac":"").($zi?" (<a href='".h($zi)."'".target_blank().">".'screenshot'."</a>)":"").$_h(basename((string)$ei->getFileName(),'.php'))."\n";}foreach($qc
as$s=>$C)echo"<li><b>".h($s)."</b>: ".h($C).$_h(basename((string)$ha->driverFiles[$s],'.php'))."\n";if($dc){$yg=official_design_checksums();foreach($dc
as$n=>$cc){list($C,$bb)=$cc;$xg=$yg["$C/$n"];echo"<li><b>".h($n)."</b>".h($C?": $C":"").($xg&&$xg!==$bb?$tk("https://www.adminer.org/?version=".VERSION."#extras"):"")."\n";}}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}adminer()->afterConnect();class
TmpFile{private$handler;var$size=0;function
__construct(){$this->handler=tmpfile();}function
write($wb){$this->size+=strlen($wb);fwrite($this->handler,$wb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if($_GET["select"]!=""&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$m=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$M=array(idf_escape($_GET["field"]));$I=driver()->select($a,$M,array(where($_GET,$m)),$M);$K=($I?$I->fetch_row():array());echo
driver()->value($K[0],$m[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$m=fields($a);if(!$m)$k=adminer()->error()?:'No tables.';$S=table_status1($a);$C=adminer()->tableName($S);page_header(($m&&is_view($S)?$S['Engine']=='materialized view'?'Materialized view':'View':'Table').": ".($C!=""?$C:h($a)),$k);$pi=array();foreach($m
as$w=>$l)$pi+=$l["privileges"];adminer()->selectLinks($S,(isset($pi["insert"])||!support("table")?"":null));$nb=$S["Comment"];if($nb!="")echo"<p class='nowrap'>".'Comment'.": ".adminer()->commentValue('TABLE',$nb)."\n";if($m)adminer()->tableStructurePrint($m,$S);function
tables_links(array$T){echo"<ul>\n";foreach($T
as$K){$z=preg_replace('~ns=[^&]*~',"ns=".url_escape($K["ns"]),ME);echo"<li><a href='".h($z."table=".url_escape($K["table"]))."'>".($K["ns"]!=$_GET["ns"]?"<b>".h($K["ns"])."</b>.":"").h($K["table"])."</a>";}echo"</ul>\n";}$ze=driver()->inheritsFrom($a);if($ze){echo"<h3>".'Inherits from'."</h3>\n";tables_links($ze);}if(support("indexes")&&driver()->supportsIndex($S)){echo"<div>\n","<h3 id='indexes'>".'Indexes'."</h3>\n";$v=indexes($a);if($v)adminer()->tableIndexesPrint($v,$S);if(driver()->supportsAlterIndex($S))echo'<p class="links hover"><a href="'.h(ME).'indexes='.url_escape($a).'">'.'Alter indexes'."</a>\n";echo"</div>\n";}if(!is_view($S)){if(fk_support($S)){echo"<div>\n","<h3 id='foreign-keys'>".'Foreign keys'."</h3>\n";$vd=foreign_keys($a);if($vd){echo"<table>\n","<thead><tr><th>".'Source'."<td>".'Target'."<td>".'ON DELETE'."<td>".'ON UPDATE'."<td class='hover'><tbody>\n";foreach($vd
as$C=>$o){echo"<tr title='".h($C)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$o["source"]))."</i>";$z=($o["db"]!=""?preg_replace('~db=[^&]*~',"db=".url_escape($o["db"]),ME):($o["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".url_escape($o["ns"]),ME):ME));echo"<td><a href='".h($z."table=".url_escape($o["table"]))."'>".($o["db"]!=""&&$o["db"]!=DB?"<b>".h($o["db"])."</b>.":"").($o["ns"]!=""&&$o["ns"]!=$_GET["ns"]?"<b>".h($o["ns"])."</b>.":"").h($o["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$o["target"]))."</i>)","<td>".h($o["on_delete"]),"<td>".h($o["on_update"]),'<td class="hover"><a href="'.h(ME.'foreign='.url_escape($a).'&name='.url_escape($C)).'">'.'Alter'.'</a>',"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'foreign='.url_escape($a).'">'.'Create foreign key'."</a>\n","</div>\n";}if(support("check")){echo"<div>\n","<h3 id='checks'>".'Checks'."</h3>\n";$Ya=driver()->checkConstraints($a);if($Ya){echo"<table>\n";foreach($Ya
as$w=>$X)echo"<tr title='".h($w)."'>","<td><code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($X)),80,"</code>"),"<td class='hover'><a href='".h(ME.'check='.url_escape($a).'&name='.url_escape($w))."'>".'Alter'."</a>","\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'check='.url_escape($a).'">'.'Create check'."</a>\n","</div>\n";}}if(support(is_view($S)?"view_trigger":"trigger")){echo"<div>\n","<h3 id='triggers'>".'Triggers'."</h3>\n";$ek=triggers($a);if($ek){echo"<table>\n";foreach($ek
as$w=>$X)echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($w)."<td class='hover'><a href='".h(ME.'trigger='.url_escape($a).'&name='.url_escape($w))."'>".'Alter'."</a>\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'trigger='.url_escape($a).'">'.'Create trigger'."</a>\n","</div>\n";}$ye=driver()->inheritedTables($a);if($ye){echo"<h3 id='partitions'>".'Inherited by'."</h3>\n";$ih=driver()->partitionsInfo($a);if($ih)echo"<p><code class='jush-".JUSH."'>BY ".h("$ih[partition_by]($ih[partition])")."</code>\n";tables_links($ye);}}elseif(isset($_GET["schema"])){page_header('Database schema',"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));function
schema_column($R,array$di,array&$d){if(!isset($d[$R])){$d[$R]=0;foreach((array)idx($di,$R)as$C=>$fi){if($C!=$R)$d[$R]=max($d[$R],schema_column($C,$di,$d)+1);}}return$d[$R];}function
type_class($U){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$w=>$X){if(preg_match("~$w|$X~",$U))return" class='$w'";}}$tj=array();$vj=array();$uj=array();$fd=array();$ca=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$ca,$xf,PREG_SET_ORDER);foreach($xf
as$r=>$A){$tj[$A[1]]=array((float)$A[2],(float)$A[3]);$vj[]="\n\t'".js_escape($A[1])."': [ $A[2], $A[3] ]";}$xi=array();$di=array();$vd=array();$na=driver()->allFields();$ce=array();$wj=array();foreach(table_status('',true)as$R=>$S){if(!is_view($S)){if(adminer()->tableName($S)!="")$wj[$R]=$S;else$ce[$R]=true;}}foreach($wj
as$R=>$S){$G=0;$xi[$R]["fields"]=array();foreach($na[$R]as$l){$G+=1.25;$fd[$R][$l["field"]]=$G;$xi[$R]["fields"][$l["field"]]=$l;}foreach(adminer()->foreignKeys($R)as$X){if($X["db"]==""&&$X["ns"]==""&&!$ce[$X["table"]]){$vd[$R][]=$X;$di[$X["table"]][$R]=array();}}}$d=array();$Hd=array();$Wk=array();$Nd=array();foreach(array_keys($xi)as$C)schema_column($C,$di,$d);arsort($d);foreach($d
as$C=>$c){$Wf=null;foreach((array)idx($vd,$C)as$X){if($X["table"]!=$C&&$xi[$X["table"]])$Wf=($Wf===null?$d[$X["table"]]:min($Wf,$d[$X["table"]]));}$d[$C]=max($c,(int)$Wf-1);}foreach($xi
as$C=>$R){$c=$d[$C];$Hd[$c][]=$C;$Kj=.75*strlen($C);foreach($R["fields"]as$l)$Kj=max($Kj,.65*strlen($l["field"]));$Wk[$c]=max(idx($Wk,$c,0),ceil($Kj)+1);}foreach($vd
as$C=>$Gk){foreach($Gk
as$X){$Md=$d[$C]+(idx($d,$X["table"],$d[$C])>$d[$C]?1:0);$Nd[$Md]=idx($Nd,$Md,0)+1;}}ksort($Hd);$ae=0;$Vk=0;$lb=0;$Kh=null;$rj=array();$yj=array();foreach($Hd
as$c=>$T){if($Kh!==null){$lb=round($lb+$Wk[$Kh]+1.7+idx($Nd,$c,0)*.1,1);$D=array();foreach($T
as$C){$lj=0;$Bb=0;$jg=array_keys((array)idx($di,$C));foreach((array)idx($vd,$C)as$X)$jg[]=$X["table"];foreach($jg
as$gg){if($xi[$gg]&&$d[$gg]<$c){$lj+=$xi[$gg]["pos"][0];$Bb++;}}$D[$C]=($Bb?$lj/$Bb:$ae);}asort($D);$T=array_keys($D);}$Vj=0;foreach($T
as$C){$G=1.25*count($xi[$C]["fields"]);$xi[$C]["pos"]=($tj[$C]?:array($Vj,$lb));$rj[$C]=$xi[$C]["pos"][1];$yj[$C]=$Wk[$c];$Vj+=2.5+$G;$ae=max($ae,$xi[$C]["pos"][0]+2.5+$G);$Vk=max($Vk,round($xi[$C]["pos"][1]+$Wk[$c],1));if(!$tj[$C])$uj[]="\n\t'".js_escape($C)."': [ ".$xi[$C]["pos"][0].", ".$xi[$C]["pos"][1]." ]";}$Kh=$c;}$hf=array();$Ha=array();foreach($vd
as$C=>$Gk){foreach($Gk
as$X){$Dj=idx($rj,$X["table"],$rj[$C]);$Vi=$rj[$C]+$yj[$C];$oi=($Dj-1>$Vi);$ff=($oi?$Vi+1:min($rj[$C],$Dj)-1);$Ga=idx($Ha,(string)$ff,0);$Ha[(string)$ff]=$Ga+1;$ff=round($oi?min($ff+$Ga*.1,$Dj-1):$ff-$Ga*.1,1);while($hf[(string)$ff])$ff-=.0001;$xi[$C]["references"][$X["table"]][(string)$ff]=array($X["source"],$X["target"]);$di[$X["table"]][$C][(string)$ff]=$X["target"];$hf[(string)$ff]=true;}}echo'<div id="schema" style="height: ',$ae,'em; width: ',$Vk,'em;">
<script',nonce(),'>
const tablePos = {',implode(",",$vj)."\n",'};
const tablePosDefault = {',implode(",",$uj)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$ae,';
document.onmousemove = schemaMousemove;
document.onmouseup = event => schemaMouseup(event, \'',js_escape(DB),'\');
</script>
';foreach($xi
as$C=>$R){echo"<div class='table'".on('mousedown','schemaMousedown')." style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em; width: ".$yj[$C]."em;'>",'<a href="'.h(ME).'table='.url_escape($C).'"><b>'.h($C)."</b></a>";foreach($R["fields"]as$l){$X='<span'.type_class($l["type"]).' title="'.h($l["type"].($l["length"]?"($l[length])":"").($l["null"]?" NULL":'')).'">'.h($l["field"]).'</span>';echo"<br>".($l["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$Ej=>$fi){foreach($fi
as$ff=>$ai){$gf=$ff-$R["pos"][1];$ij=($gf>0?"left: 100%; width: calc($gf"."em - 100%)":"left: $gf"."em");$Vk=($gf>0?"100%":(-$gf)."em");$r=0;foreach($ai[0]as$Ui)echo"\n<div class='references' title='".h($Ej)."' id='refs$ff-".($r++)."' style='$ij"."; top: ".$fd[$C][$Ui]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: $Vk;'></div></div>";}}foreach((array)$di[$C]as$Ej=>$fi){foreach($fi
as$ff=>$Fj){$gf=$ff-$R["pos"][1];$r=0;foreach($Fj
as$Cj)echo"\n<div class='references arrow' title='".h($Ej)."' id='refd$ff-".($r++)."' style='left: $gf"."em; top: ".$fd[$C][$Cj]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$gf)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($xi
as$C=>$R){foreach((array)$R["references"]as$Ej=>$fi){if($xi[$Ej]){foreach($fi
as$ff=>$ai){$Xf=$ae;$Ef=-10;foreach($ai[0]as$w=>$Ui){$Ch=$R["pos"][0]+$fd[$C][$Ui];$Dh=$xi[$Ej]["pos"][0]+$fd[$Ej][$ai[1][$w]];$Xf=min($Xf,$Ch,$Dh);$Ef=max($Ef,$Ch,$Dh);}echo"<div class='references' id='refl$ff' style='left: $ff"."em; top: $Xf"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($Ef-$Xf)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".url_escape($ca)),'" id="schema-link">Permanent link</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$k){$j=array("auto_increment"=>'');foreach(array("type","routine","event","trigger")as$nj){if(support($nj))$j[$nj."s"]='';}save_settings(array_intersect_key($_POST+$j,array_flip(array("output","format","db_style","table_style","data_style"))+$j),"adminer_export");$T=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$Wc=dump_headers((count($T)==1?key($T):DB),(DB==""||$_GET["ns"]===""||count($T)>1));$Oe=preg_match('~sql~',$_POST["format"]);if($Oe){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$ij=$_POST["db_style"];$h=array(DB);if(DB==""){$h=$_POST["databases"];if(is_string($h))$h=explode("\n",rtrim(str_replace("\r","",$h),"\n"));}foreach((array)$h
as$i){adminer()->dumpDatabase($i);if(connection()->select_db($i)){if($Oe&&$ij)echo
use_sql($i,$ij).";\n\n";foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?array(""):adminer()->schemas()))as$xi){if($xi!=""){if(DB==""&&information_schema(DB,$xi))continue;set_schema($xi);}$fj=($_POST["table_style"]||$_POST["data_style"]?table_status('',true):array());$Vc=array();$Nb=array();foreach($fj
as$C=>$S){if(DB==""||$_GET["ns"]===""||in_array($C,(array)$_POST["tables"]))$Vc[$C]=$S;if(DB==""||$_GET["ns"]===""||in_array($C,(array)$_POST["data"]))$Nb[$C]=$S;}if($Oe){if($_POST["table_style"]=="DROP+CREATE"&&function_exists('Adminer\drop_sql'))echo
drop_sql($Vc);if($_POST["data_style"]=="TRUNCATE+INSERT"&&function_exists('Adminer\truncate_all_sql')){$fk=array();foreach($Nb
as$C=>$S){if(!is_view($S)&&!($_POST["table_style"]=="DROP+CREATE"&&isset($Vc[$C])))$fk[]=$C;}echo
truncate_all_sql($fk);}$ah="";if($_POST["types"]){foreach(types()as$s=>$U){$Wb=type_definition($s);$ug=($Wb["kind"]=='d'?"DOMAIN":"TYPE");if($Wb["definition"])$ah
.=($ij!='DROP+CREATE'?"DROP $ug IF EXISTS ".idf_escape($U).";;\n":"")."CREATE $ug ".idf_escape($U)." $Wb[definition];\n\n";else$ah
.="-- Could not export type $U\n\n";}}if($_POST["routines"]){foreach(routines()as$K){$C=$K["ROUTINE_NAME"];$ri=$K["ROUTINE_TYPE"];$g=create_routine($ri,array("name"=>$C)+routine($K["SPECIFIC_NAME"],$ri));set_utf8mb4($g);$ah
.=($ij!='DROP+CREATE'?"DROP $ri IF EXISTS ".idf_escape($C).";;\n":"")."$g;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$K){$g=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($K["Name"]),3));set_utf8mb4($g);$ah
.=($ij!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($K["Name"]).";;\n":"")."$g;;\n\n";}}echo($ah&&JUSH=='sql'?"DELIMITER ;;\n\n$ah"."DELIMITER ;\n\n":$ah);}if($_POST["table_style"]||$_POST["data_style"]){$Mk=array();foreach($fj
as$C=>$S){$R=array_key_exists($C,$Vc);$Lb=array_key_exists($C,$Nb);if($R||$Lb){$Sj=null;if($Wc=="tar"){$Sj=new
TmpFile;ob_start(array($Sj,'write'),1e5);}adminer()->dumpTable($C,($R?$_POST["table_style"]:""),(is_view($S)?2:0));if(is_view($S))$Mk[]=$C;elseif($Lb){$m=fields($C);$M=array("*");$zb=convert_fields($m,$m);if($zb)$M[]=substr($zb,2);adminer()->dumpData($C,$_POST["data_style"],"",$M);}if($Oe&&$_POST["triggers"]&&$R&&($ek=trigger_sql($C)))echo"\nDELIMITER ;;\n$ek\nDELIMITER ;\n";if($Wc=="tar"){ob_end_flush();tar_file((DB!=""?"":"$i/")."$C.csv",$Sj);}elseif($Oe)echo"\n";}}if($Oe&&$_POST["table_style"]&&function_exists('Adminer\foreign_keys_sql')){foreach($Vc
as$C=>$S){if(!is_view($S))echo
foreign_keys_sql($C);}}if($Oe){foreach($Mk
as$Lk)adminer()->dumpTable($Lk,$_POST["table_style"],1);}if($Wc=="tar")echo
pack("x1024");}}}}adminer()->dumpFooter();exit;}page_header('Export',$k,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$Qb=array('','USE','DROP+CREATE','CREATE');$xj=array('','DROP+CREATE','CREATE');$Mb=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Mb[]='INSERT+UPDATE';$K=get_settings("adminer_export");if(!$K)$K=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"table_style"=>"DROP+CREATE","data_style"=>"INSERT");echo"<tr><th>".'Output'."<td>".html_radios("output",adminer()->dumpOutput(),$K["output"])."\n","<tr><th>".'Format'."<td>".html_radios("format",adminer()->dumpFormat(),$K["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".'Database'."<td>".html_select('db_style',$Qb,$K["db_style"]).(support("type")?checkbox("types",1,$K["types"],'User types'):"").(support("routine")?checkbox("routines",1,$K["routines"],'Routines'):"").(support("event")?checkbox("events",1,$K["events"],'Events'):"")),"<tr><th>".'Tables'."<td>".html_select('table_style',$xj,$K["table_style"]).checkbox("auto_increment",1,$K["auto_increment"],'Auto Increment').(support("trigger")?checkbox("triggers",1,$K["triggers"],'Triggers'):""),"<tr><th>".'Data'."<td>".html_select('data_style',$Mb,$K["data_style"]),'</table>
';adminer()->dumpPrint();echo'<p><input type=\'submit\' value=\'Export\'>
',input_token(),'
<table',on('click','dumpClick'),'>
';$Jh=array();if($_GET["ns"]===""){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".'All'."'".on('click','formCheck','^schemas\[').">".'Schema'."</label>","<tbody>\n";foreach(adminer()->schemas()as$xi){if(!information_schema(DB,$xi))echo"<tr><td>".checkbox("schemas[]",$xi,true,$xi,"","block")."\n";}}elseif(DB!=""){$Za=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$Za class='jsonly' title='".'All'."'".on('click','formCheck','^tables\[').">".'Table'."</label>","<th style='text-align: right;'><label class='block'>".'Data'."<input type='checkbox' id='check-data'$Za class='jsonly' title='".'All'."'".on('click','formCheck','^data\[')."></label>","<tbody>\n";$Mk="";$_j=tables_list();foreach($_j
as$C=>$U){$Ih=preg_replace('~_.*~','',$C);$Za=($a==""||$a==(substr($a,-1)=="%"?"$Ih%":$C));$Mh="<tr><td>".checkbox("tables[]",$C,$Za,$C,"","block");if($U!==null&&!preg_match('~table~i',$U))$Mk
.="$Mh\n";else
echo"$Mh<td align='right'><label class='block'><span id='Rows-".h($C)."'></span>".checkbox("data[]",$C,$Za)."</label>\n";$Jh[$Ih]++;}echo$Mk;if($_j)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$h=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($h?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".'All'."'".on('click','formCheck','^databases\[').">":"").'Database'."</label>","<tbody>\n";if($h){foreach($h
as$i){if(!information_schema($i)){$Ih=preg_replace('~_.*~','',$i);echo"<tr><td>".checkbox("databases[]",$i,$a==""||$a=="$Ih%",$i,"","block")."\n";$Jh[$Ih]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$od=true;foreach($Jh
as$w=>$X){if($w!=""&&$X>1){echo($od?"<p>":" ")."<a href='".h(ME)."dump=".url_escape("$w%")."'>".h($w)."</a>";$od=false;}}}elseif(isset($_GET["privileges"])){page_header('Privileges');echo'<p class="links"><a href="'.h(ME).'user=">'.'Create user'."</a>";$I=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$Fd=$I;if(!$I)$I=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($Fd?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".'Username'."<th>".'Server'."<td class='hover'><tbody>\n";while($K=$I->fetch_assoc())echo'<tr><td>'.h($K["User"]),"<td>".h($K["Host"]),'<td class="hover"><a href="'.h(ME.'user='.url_escape($K["User"]).'&host='.url_escape($K["Host"])).'">'.'Edit'."</a>\n";if(!$Fd||DB!="")echo"<tr><td><input name='user' autocapitalize='off'>","<td><input name='host' value='localhost' autocapitalize='off'>","<td class='hover'><input type='submit' value='".'Edit'."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$k&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}restart_session();$ee=&get_session("queries");$de=&$ee[DB];if(!$k&&$_POST["clear"]){$de=array();redirect(remove_from_uri("history"));}stop_session();$ia=get_settings("adminer_import");if($_POST&&$ia)save_settings($ia,"adminer_import");page_header((isset($_GET["import"])?'Import':'SQL command'),$k);$of=driver()->lineComment();if(!$k&&$_POST&&!(isset($_GET["import"])&&adminer()->importProcess())){$Yb=driver()->delimiter;$p=false;if(!isset($_GET["import"]))$H=$_POST["query"];elseif($_POST["webfile"]){$Yi=adminer()->importServerPath();$p=@fopen((file_exists($Yi)?$Yi:"compress.zlib://$Yi.gz"),"rb");$H=($p?fread($p,1e6):false);}else$H=get_file("sql_file",true,$Yb);if(is_string($H)){if(($Mf=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($Mf,strval(2*strlen($H)+memory_get_usage()+8e6)));if($H!=""&&strlen($H)<1e6){$Th=$H.(preg_match("~$Yb\\s*\$~",$H)?"":$Yb);if(!$de||first(end($de))!=$Th){restart_session();$de[]=array($Th,time());set_session("queries",$ee);stop_session();}}$Wi="(?:\\s|/\\*[\s\S]*?\\*/|(?:$of)[^\n]*\n?|--\r?\n)";$zg=0;$Bc=true;$Ab=false;$f=connect();if($f&&DB!=""){$f->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$f);}$mb=0;$Ic=array();$gh='[\'"'.(JUSH=="sql"?'`':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$of.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$Wj=microtime(true);while($H!=""){if(!$zg&&preg_match("~^$Wi*+DELIMITER\\s+(\\S+)~i",$H,$A)){$Yb=preg_quote($A[1]);$H=substr($H,strlen($A[0]));}elseif(!$zg&&JUSH=='pgsql'&&preg_match("~^($Wi*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$H,$A)){$Yb="\n\\\\\\.\r?\n";$Ab=true;$zg=strlen($A[0]);}else{preg_match("($Yb\\s*|$gh)",$H,$A,PREG_OFFSET_CAPTURE,$zg);list($xd,$G)=$A[0];if(!$xd&&$p&&!feof($p))$H
.=fread($p,1e5);else{if(!$xd&&rtrim($H)=="")break;$zg=$G+strlen($xd);if($xd&&!preg_match("(^$Yb)",$xd)){$Ra=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($G>0&&strtolower($H[$G-1])=="e"));$uh=($xd=='/*'?'\*/':($xd=='['?']':(preg_match("~^(?:$of)~",$xd)?"\n":preg_quote($xd).($Ra?'|\\\\.':''))));while(preg_match("($uh|\$)s",$H,$A,PREG_OFFSET_CAPTURE,$zg)){$vi=$A[0][0];if(!$vi&&$p&&!feof($p))$H
.=fread($p,1e5);else{$zg=$A[0][1]+strlen($vi);if(!$vi||$vi[0]!="\\")break;}}}else{$Bc=false;$Th=substr($H,0,$G+($Ab?3:0));$mb++;$Mh="<pre id='sql-$mb'><code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($Th)."</code></pre>\n";if(JUSH=="sqlite"&&preg_match("~^$Wi*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$Th,$A)!==0){echo$Mh,"<p class='error'>".sprintf('%s queries are not supported.',preg_match('~ATTACH~i',$A[1])?'ATTACH':'VACUUM INTO')."\n";$Ic[]=" <a href='#sql-$mb'>$mb</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$Mh;ob_flush();flush();}$dj=microtime(true);if(connection()->multi_query($Th)&&$f&&preg_match("~^$Wi*+USE\\b~i",$Th))$f->query($Th);do{$I=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$Mh:""),"<p class='error'>".'Error in query'.(connection()->errno?" (".connection()->errno.")":"").": ".adminer()->error()."\n";$Ic[]=" <a href='#sql-$mb'>$mb</a>";if($_POST["error_stops"])break
2;}else{$z=ME."sql=".url_escape(trim($Th));$Lj=" <span class='time'>(".format_time($dj).")</span>".(strlen($z)<1900?" <a href='".h($z)."'>".'Edit'."</a>":"");$ka=connection()->affected_rows;$Pk=($_POST["only_errors"]?"":driver()->warnings());$Qk="warnings-$mb";if($Pk)$Lj
.=", <a href='#$Qk' class='toggle'>".'Warnings'."</a>";$Tc=null;$Sg=null;$Uc="explain-$mb";if(is_object($I)){$y=$_POST["limit"];$sg=$y;$Sg=print_select_result($I,$f,array(),$sg);if(!$_POST["only_errors"]){echo"<form action='' method='post'>\n";$sg=max($I->num_rows,$sg);echo"<p class='sql-footer'>".($sg?($y&&$sg>$y?sprintf('%d / ',$y):"").lang_format(array('%d row','%d rows'),$sg):""),$Lj;if($f&&preg_match("~^($Wi|\\()*+SELECT\\b~i",$Th)&&($Tc=explain($f,$Th)))echo", <a href='#$Uc' class='toggle'>Explain</a>";$s="export-$mb";echo", <a href='#$s' class='toggle'>".'Export'."</a><span id='$s' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ia["output"])." ".html_select("format",adminer()->dumpFormat(),$ia["format"]).input_hidden("query",$Th)."<input type='submit' name='export' value='".'Export'."'".($y?"":on('click','sqlExport')).">".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$Wi*+(CREATE|DROP|ALTER)$Wi++(DATABASE|SCHEMA)\\b~i",$Th)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang_format(array('Query executed OK, %d row affected.','Query executed OK, %d rows affected.'),$ka)."$Lj\n";}echo($Pk?"<div id='$Qk' class='hidden'>\n$Pk</div>\n":"");if($Tc){echo"<div id='$Uc' class='hidden explain'>\n";print_select_result($Tc,$f,$Sg);echo"</div>\n";}}$dj=microtime(true);}while(connection()->next_result());}$H=substr($H,$zg);$zg=0;if($Ab){$Yb=driver()->delimiter;$Ab=false;}}}}}if($Bc)echo"<p class='message'>".'No commands to execute.'."\n";else{$re=connection()->inTransaction();driver()->rollback();if($re)echo"<pre><code class='jush-".JUSH."'>ROLLBACK -- Adminer</code></pre>\n";if($_POST["only_errors"])echo"<p class='message'>".lang_format(array('%d query executed OK.','%d queries executed OK.'),$mb-count($Ic))," <span class='time'>(".format_time($Wj).")</span>\n";elseif($Ic&&$mb>1)echo"<p class='error'>".'Error in query'.": ".implode("",$Ic)."\n";}}else
echo"<p class='error'>".upload_error($H)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form"';$uk="";if(!isset($_GET["import"]))echo
on('submit','sqlSubmit',remove_from_uri("sql|limit|error_stops|only_errors|history"));else
echo
on_upload_progress($uk);echo'>
';$Qc="<input type='submit' value='".'Execute'."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$Th=$_GET["sql"];if($_POST)$Th=$_POST["query"];elseif($_GET["history"]=="all")$Th=$de;elseif($_GET["history"]!="")$Th=idx($de[$_GET["history"]],0);echo"<p>";textarea("query",$Th,20);echo($_POST?"":script("qs('textarea').focus();")),"<p>";adminer()->sqlPrintAfter();echo"$Qc\n",'Limit rows'.": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$Od=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".'File upload'."</legend><div>",($uk?input_hidden(ini_get("session.upload_progress.name"),$uk):""),"SQL$Od: ".file_input(" name='sql_file[]' multiple","\n$Qc"),($uk?" <progress class='jsonly hidden' max='1' value='0'></progress>":""),"</div></fieldset>\n";$oe=adminer()->importServerPath();if($oe)echo"<fieldset><legend>".'From server'."</legend><div>",sprintf('Webserver file %s',"<code>".h($oe)."$Od</code>")," <input type='submit' name='webfile' value='".'Run file'."'>","</div></fieldset>\n";adminer()->importPrint();echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),'Stop on error')."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),'Show only errors')."\n",input_token();if(!isset($_GET["import"])&&$de){print_fieldset("history",'History',$_GET["history"]!="");for($X=end($de);$X;$X=prev($de)){$w=key($de);list($Th,$Lj,$yc)=$X;echo'<div><a href="'.h(ME."sql=&history=$w").'" class="hover">'.'Edit'."</a>"." <span class='time' title='".@date('Y-m-d',$Lj)."'>".@date("H:i:s",$Lj)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(?:$of).*~m",'',$Th))),80,"</code>").($yc?" <span class='time'>($yc)</span>":"")."</div>\n";}echo"<input type='submit' name='clear' value='".'Clear'."'>\n","<a href='".h(ME."sql=&history=all")."'>".'Edit all'."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$m=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$m):""):where($_GET,$m));$sk=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($m
as$C=>$l){if((!$sk&&!isset($l["privileges"]["insert"]))||adminer()->fieldName($l)=="")unset($m[$C]);}if($_POST&&!$k&&!isset($_GET["select"])){$_=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$_=($sk?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$_))$_=ME."select=".url_escape($a);$v=indexes($a);$mk=unique_array($_GET["where"],$v);$Wh="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($_,'Item has been deleted.',driver()->delete($a,$Wh,$mk?0:1));else{$O=array();foreach($m
as$C=>$l){$X=process_input($l);if($X!==false&&$X!==null)$O[idf_escape($C)]=$X;}if($sk){if(!$O)redirect($_);queries_redirect($_,'Item has been updated.',driver()->update($a,$O,$Wh,$mk?0:1));if(is_ajax()){page_headers();page_messages($k);exit;}}else{$I=driver()->insert($a,$O);$ef=($I?last_id($I):0);queries_redirect($_,sprintf('Item%s has been inserted.',($ef?" $ef":"")),$I);}}}$K=null;$H="";$Lj="";if($Z){$M=array();$Di=array("*");foreach($m
as$C=>$l){if(isset($l["privileges"]["select"])){$va=($_POST["clone"]&&$l["auto_increment"]?"''":convert_field($l));$c=($va?"$va AS ":"").idf_escape($C);$M[]=$c;if($va)$Di[]=$c;}}$K=array();if(!support("table")){$M=array("*");$Di=$M;}if($M){$dj=microtime(true);$I=driver()->select($a,$M,array($Z),$M,array(),(isset($_GET["select"])?2:1));$H=str_replace("SELECT ".implode(", ",$M),"SELECT ".implode(", ",$Di),driver()->query);$Lj=format_time($dj);if(!$I)$k=adminer()->error();else{$K=$I->fetch_assoc();if(!$K)$K=false;}if(isset($_GET["select"])&&(!$K||$I->fetch_assoc()))$K=null;}}if(!$m&&driver()->primary!=""){if(!$Z){$I=driver()->select($a,array("*"),array(),array("*"));$K=($I?$I->fetch_assoc():false);if(!$K)$K=array(driver()->primary=>"");}if($K){foreach($K
as$w=>$X){if(!$Z)$K[$w]=null;$m[$w]=array("field"=>$w,"null"=>($w!=driver()->primary),"auto_increment"=>($w==driver()->primary));}}}if($_POST["save"]){$Eh=array();foreach((array)$_POST["fields"]as$w=>$X)$Eh[bracket_escape($w,true)]=$X;$K=$Eh+($K?$K:array());}edit_form($a,$m,$K,$sk,$k,$H,$Lj);}elseif(isset($_GET["create"])){function
referencable_primary($Fi){$J=array();foreach(table_status('',true)as$sj=>$R){if($sj!=$Fi&&fk_support($R)){foreach(fields($sj)as$l){if($l["primary"]){if($J[$sj]){unset($J[$sj]);break;}$J[$sj]=$l;}}}}return$J;}$a=$_GET["create"];$kh=driver()->partitionBy;$oh=($kh&&$a!=""?driver()->partitionsInfo($a):array());$ci=referencable_primary($a);$vd=array();foreach($ci
as$sj=>$l)$vd[str_replace("`","``",$sj)."`".str_replace("`","``",$l["field"])]=$sj;$Vg=array();$S=array();if($a!=""){$Vg=fields($a);$S=table_status1($a);if(count($S)<2)$k='No tables.';}$K=$_POST;$K["fields"]=(array)$K["fields"];if($K["auto_increment_col"])$K["fields"][$K["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$k)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($K["fields"])&&!$k){if($_POST["drop"])queries_redirect(substr(ME,0,-1),'Table has been dropped.',drop_tables(array($a)));else{$m=array();$na=array();$yk=false;$td=array();$Ug=reset($Vg);$ma=" FIRST";foreach($K["fields"]as$w=>$l){$o=$vd[$l["type"]];$hk=($o!==null?$ci[$o]:$l);if($l["field"]!=""){if(!$l["generated"])$l["default"]=null;$Rh=process_field($l,$hk);$na[]=array($l["orig"],$Rh,$ma);if(!$Ug||$Rh!==process_field($Ug,$Ug)){$m[]=array($l["orig"],$Rh,$ma);if($l["orig"]!=""||$ma)$yk=true;}if($o!==null)$td[idf_escape($l["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$vd[$l["type"]],'source'=>array($l["field"]),'target'=>array($hk["field"]),'on_delete'=>$l["on_delete"],));$ma=" AFTER ".idf_escape($l["field"]);}elseif($l["orig"]!=""){$yk=true;$m[]=array($l["orig"]);}if($l["orig"]!=""){$Ug=next($Vg);if(!$Ug)$ma="";}}$mh=array();if(in_array($K["partition_by"],$kh)){foreach($K
as$w=>$X){if(preg_match('~^partition~',$w))$mh[$w]=$X;}foreach($mh["partition_names"]as$w=>$C){if($C==""){unset($mh["partition_names"][$w]);unset($mh["partition_values"][$w]);}}$mh["partition_names"]=array_values($mh["partition_names"]);$mh["partition_values"]=array_values($mh["partition_values"]);if($mh==$oh)$mh=array();}elseif(preg_match("~partitioned~",$S["Create_options"]))$mh=null;$B='Table has been altered.';if($a==""){cookie("adminer_engine",$K["Engine"]);$B='Table has been created.';}$C=trim($K["name"]);$_=ME.(support("table")?"table=":"select=").url_escape($C);$I=alter_table($a,$C,(JUSH=="sqlite"&&($yk||$td)?$na:$m),$td,($K["Comment"]!=$S["Comment"]?$K["Comment"]:null),($K["Engine"]&&$K["Engine"]!=$S["Engine"]?$K["Engine"]:""),($K["Collation"]&&$K["Collation"]!=$S["Collation"]?$K["Collation"]:""),($K["Auto_increment"]!=""?number($K["Auto_increment"]):""),$mh);if($I&&!Queries::$queries&&$a!=""&&!$m&&!$td)redirect($_);queries_redirect($_,$B,$I);}}page_header(($a!=""?'Alter table':'Create table'),$k,array("table"=>$a),h($a));if(!$_POST){$jk=driver()->types();$K=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($jk["int"])?"int":(isset($jk["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$K=$S;$K["name"]=$a;$K["fields"]=array();if(!$_GET["auto_increment"])$K["Auto_increment"]="";foreach($Vg
as$l){if($l["generated"])$l["default"]=ltrim($l["default"]);$l["generated"]=$l["generated"]?:(isset($l["default"])?"DEFAULT":"");$K["fields"][]=$l;}if($kh){$K+=$oh;$K["partition_names"][]="";$K["partition_values"][]="";}}}$jb=flat_collations();$Dc=driver()->engines();foreach($Dc
as$Cc){if(!strcasecmp($Cc,$K["Engine"])){$K["Engine"]=$Cc;break;}}$_f=max_input_vars(12,20);if($_f){$ce=(count($K["fields"])>$_f?"":" hidden");echo"<p".($ce?" id='max-fields' data-columns='$_f'":"")." class='error$ce'>".max_input_vars_error()."\n";}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo'Table name'.": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($K["name"])."' autocapitalize='off'>\n",($Dc?html_select("Engine",array(""=>"(".'engine'.")")+$Dc,$K["Engine"],on('change','helpClose').on_help_value())."\n":"");if($jb)echo"<datalist id='collations'>".optionlist($jb)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($K["Collation"])."' placeholder='(".'collation'.")'>\n");echo"<input type='submit' value='".'Save'."'>\n";}if(support("columns")){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($K["fields"],$jb,"TABLE",$vd);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",'Auto Increment'.": <input type='number' name='Auto_increment' class='size' value='".h($K["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),'Default values',on('click','columnShowClick',5),"jsonly");$pb=($_POST?$_POST["comments"]:get_setting("comments"));if(support("comment")){echo
checkbox("comments",1,$pb,'Comment',on('click','editingCommentsClick',true),"jsonly").' ';$b=" name='Comment' data-maxlength='".(min_version(5.5)?2048:60)."'".($pb?"":" class='hidden'");echo
adminer()->commentInput('TABLE',$b,$K["Comment"]);}echo'<p>
<input type=\'submit\' value=\'Save\'>
';}echo'
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$a)),'>
';if($kh&&(JUSH=='sql'||$a=="")){$lh=preg_match('~RANGE|LIST~',$K["partition_by"]);print_fieldset("partition",'Partition by',$K["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$kh),$K["partition_by"],on('change','partitionByChange').on_help_value('.','PARTITION BY $&'))."\n","(<input name='partition' value='".h($K["partition"])."'>)\n",'Partitions'.": <input type='number' name='partitions' class='size".($lh||!$K["partition_by"]?" hidden":"")."' value='".h($K["partitions"])."'>\n","<table id='partition-table'".($lh?"":" class='hidden'").">\n","<thead><tr><th>".'Partition name'."<th>".'Values'."<tbody>\n";foreach($K["partition_names"]as$w=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off"'.($w==count($K["partition_names"])-1?on('input','partitionNameChange'):'').'>','<td><input name="partition_values[]" value="'.h(idx($K["partition_values"],$w)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$we=array("PRIMARY","UNIQUE","INDEX");$S=table_status1($a,true);$ue=driver()->indexAlgorithms($S);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$we[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$we[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$S["Engine"]))$we[]="VECTOR";$v=indexes($a);$m=fields($a);$Lh=array();if(JUSH=="mongo"){$Lh=$v["_id_"];unset($we[0]);unset($v["_id_"]);}$K=$_POST;if($K)save_settings(array("index_options"=>$K["options"]));if($_POST&&!$k&&!$_POST["add"]&&!$_POST["drop_col"]){$pa=array();foreach($K["indexes"]as$u){$C=$u["name"];if(in_array($u["type"],$we)){$d=array();$lf=array();$bc=array();$Ig=array();$ve=(support("partial_indexes")?$u["partial"]:"");$te=(in_array($u["algorithm"],$ue)?$u["algorithm"]:"");$O=array();ksort($u["columns"]);foreach($u["columns"]as$w=>$c){if($c!=""){$x=idx($u["lengths"],$w);$Zb=idx($u["descs"],$w);$Hg=idx($u["opclasses"],$w);$O[]=($m[$c]?idf_escape($c):$c).($x?"(".(+$x).")":"").($Hg!=""?" ".idf_escape($Hg):"").($Zb?" DESC":"");$d[]=$c;$lf[]=($x?:null);$bc[]=$Zb;$Ig[]="$Hg";}}$Rc=$v[$C];if($Rc){ksort($Rc["columns"]);ksort($Rc["lengths"]);ksort($Rc["descs"]);if($u["type"]==$Rc["type"]&&array_values($Rc["columns"])===$d&&(!$Rc["lengths"]||array_values($Rc["lengths"])===$lf)&&array_values($Rc["descs"])===$bc&&(!$Rc["opclasses"]||array_values($Rc["opclasses"])===$Ig)&&$Rc["partial"]==$ve&&(!$ue||$Rc["algorithm"]==$te)){unset($v[$C]);continue;}}if($d)$pa[]=array($u["type"],$C,$O,$te,$ve);}}foreach($v
as$C=>$Rc)$pa[]=array($Rc["type"],$C,"DROP");if(!$pa)redirect(ME."table=".url_escape($a));queries_redirect(ME."table=".url_escape($a),'Indexes have been altered.',alter_indexes($a,$pa));}page_header('Indexes',$k,array("table"=>$a),h($a));$hd=array_keys($m);if($_POST["add"]){foreach($K["indexes"]as$w=>$u){if($u["columns"][count($u["columns"])]!="")$K["indexes"][$w]["columns"][]="";}$u=end($K["indexes"]);if($u["type"]||array_filter($u["columns"],'strlen'))$K["indexes"][]=array("columns"=>array(1=>""));}if(!$K){foreach($v
as$w=>$u){$v[$w]["name"]=$w;$v[$w]["columns"][]="";}$v[]=array("columns"=>array(1=>""));$K["indexes"]=$v;}$lf=(JUSH=="sql"||JUSH=="mssql");$Ig=driver()->indexOpclasses();$Ni=($_POST?$_POST["options"]:get_setting("index_options"));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap odds">
<thead><tr>
<th id="label-type">Index Type
';$me=" class='idxopts".($Ni?"":" hidden")."'";if($ue)echo"<th id='label-algorithm'$me>".'Algorithm'.doc_link(array('sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/',));echo'<th><input type="submit" hidden>','Columns'.($lf?"<span$me> (".'length'.")</span>":"");if($lf||support("descidx"))echo
checkbox("options",1,$Ni,'Options',on('click','indexOptionsShow'),"jsonly")."\n";echo'<th id="label-name">Name
';if(support("partial_indexes"))echo"<th id='label-condition'$me>".'Condition';echo'<th><noscript>',icon("plus","add[0]","+",'Add next'),'</noscript>
<tbody>
';if($Lh){echo"<tr><td>PRIMARY<td>";foreach($Lh["columns"]as$w=>$c)echo
select_input(" disabled",array_combine($hd,$hd),$c),"<label><input disabled type='checkbox'>".'descending'."</label> ";echo"<td><td>\n";}$Se=1;foreach($K["indexes"]as$u){if(!$_POST["drop_col"]||$Se!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$Se][type]",array(-1=>"")+$we,$u["type"],($Se==count($K["indexes"])?on('change','indexesAddRow'):""),"label-type");if($ue)echo"<td$me>".html_select("indexes[$Se][algorithm]",array_merge(array(""),$ue),$u['algorithm'],"","label-algorithm");echo"<td>";ksort($u["columns"]);$r=1;foreach($u["columns"]as$w=>$c){echo"<span>".select_input(" name='indexes[$Se][columns][$r]' title='".'Column'."'".on('change','indexesChangeColumn',(JUSH=="sql"?"":$_GET["indexes"]."_")),($m&&($c==""||$m[$c])?array_combine($hd,$hd):array()),$c)," <span$me>",($lf?"<input type='number' name='indexes[$Se][lengths][$r]' class='size' value='".h(idx($u["lengths"],$w))."' title='".'Length'."'>":"");if($Ig){$Hg=idx($u["opclasses"],$w);echo
html_select("indexes[$Se][opclasses][$r]",array(""=>"(".'operator class'.")")+array_combine($Ig,$Ig)+($Hg!=""?array($Hg=>$Hg):array()),$Hg),'';}echo(support("descidx")?checkbox("indexes[$Se][descs][$r]",1,idx($u["descs"],$w),'descending'):""),"<br>","</span></span>";$r++;}echo"<td><input name='indexes[$Se][name]' value='".h($u["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$me><input name='indexes[$Se][partial]' value='".h($u["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$Se]","x",'Remove',on('click','editingRemoveRow','indexes$1[type]'));}$Se++;}echo'</table>
</div>
<p>
<input type=\'submit\' value=\'Save\'>
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$K=$_POST;if($_POST&&!$k&&!$_POST["add"]){$C=trim($K["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),'Database has been dropped.',drop_databases(array(DB)));}elseif($C!==DB){if(DB!=""){$_GET["db"]=$C;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".url_escape($C),'Database has been renamed.',rename_database($C,(string)$K["collation"]));}else{$h=explode("\n",str_replace("\r","",$C));$jj=true;$cf="";foreach($h
as$i){if(count($h)==1||$i!=""){if(!create_database($i,(string)$K["collation"]))$jj=false;$cf=$i;}}restart_session();set_session("dbs",null);queries_redirect(preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($cf),'Database has been created.',$jj);}}else{if(!$K["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($C).(preg_match('~^[a-z0-9_]+$~i',$K["collation"])?" COLLATE $K[collation]":""),substr(ME,0,-1),'Database has been altered.');}}page_header(DB!=""?'Alter database':'Create database',$k,array(),h(DB));$jb=collations();$C=DB;if($_POST)$C=$K["name"];elseif(DB!="")$K["collation"]=db_collation(DB,$jb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$Fd){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$Fd,$A)&&$A[1]){$C=stripcslashes(idf_unescape("`$A[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($C,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($C).'</textarea><br>':'<input name="name" autofocus value="'.h($C).'" data-maxlength="64" autocapitalize="off">')."\n",($jb?html_select("collation",array(""=>"(".'collation'.")")+$jb,$K["collation"]).doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",)):"")."\n",'<input type=\'submit\' value=\'Save\'>
';if(DB!="")echo"<input type='submit' name='drop' value='".'Drop'."'".confirm(sprintf('Drop %s?',DB)).">\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",'Add next')."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ba=($_GET["name"]?:$_GET["call"]);page_header('Call'.": ".h($ba),$k);$ti=(isset($_GET["callf"])?"FUNCTION":"PROCEDURE");$ri=routine($_GET["call"],$ti);$pe=array();$ah=array();foreach($ri["fields"]as$r=>$l){if(substr($l["inout"],-3)=="OUT"&&JUSH=='sql')$ah[$r]="@".idf_escape($l["field"])." AS ".idf_escape($l["field"]);if(!$l["inout"]||substr($l["inout"],0,2)=="IN")$pe[]=$r;}if(!$k&&$_POST){$Sa=array();foreach($ri["fields"]as$w=>$l){$X="";if(in_array($w,$pe)){$X=process_input($l);if($X===false)$X="''";if(isset($ah[$w]))connection()->query("SET @".idf_escape($l["field"])." = $X");}if(isset($ah[$w]))$Sa[]="@".idf_escape($l["field"]);elseif(in_array($w,$pe))$Sa[]=$X;}$H=(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($ri["returns"],"type")=="record"?"* FROM ":"").table($ba)."(".implode(", ",$Sa).")";$dj=microtime(true);$I=connection()->multi_query($H);$ka=connection()->affected_rows;echo
adminer()->selectQuery($H,$dj,!$I);if(!$I)echo"<p class='error'>".adminer()->error()."\n";else{$f=connect();if($f)$f->select_db(DB);do{$I=connection()->store_result();if(is_object($I))print_select_result($I,$f);else
echo"<p class='message'>".lang_format(array('Routine has been called, %d row affected.','Routine has been called, %d rows affected.'),$ka)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($ah)print_select_result(connection()->query("SELECT ".implode(", ",$ah)));}}echo'
<form action="" method="post">
';if($pe){echo"<table class='layout'>\n";foreach($pe
as$w){$l=$ri["fields"][$w];$C=$l["field"];echo"<tr><th>".adminer()->fieldName($l);$Y=idx($_POST["fields"],$C);if($Y!=""){if($l["type"]=="set")$Y=implode(",",$Y);}input($l,$Y,idx($_POST["function"],$C,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type=\'submit\' value=\'Call\'>
',input_token(),'</form>

',adminer()->commentValue($ti,$ri['comment']);}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$C=$_GET["name"];$K=$_POST;if($_POST&&!$k&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$K["source"]=array_filter($K["source"],'strlen');ksort($K["source"]);$Cj=array();foreach($K["source"]as$w=>$X)$Cj[$w]=$K["target"][$w];$K["target"]=$Cj;}if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(" $C"=>($K["drop"]?"":" ".format_foreign_key($K))));else{$pa="ALTER TABLE ".table($a);$I=($C==""||queries("$pa DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($C)));if(!$K["drop"])$I=queries("$pa ADD".format_foreign_key($K));}queries_redirect(ME."table=".url_escape($a),($K["drop"]?'Foreign key has been dropped.':($C!=""?'Foreign key has been altered.':'Foreign key has been created.')),$I);if(!$K["drop"])$k='Source and target columns must have the same data type, there must be an index on the target columns and the referenced data must exist.';}page_header(($C!=""?'Alter foreign key':'Create foreign key'),$k,array("table"=>$a),h($C!=""?$C:$a));if($_POST){ksort($K["source"]);if($_POST["change"]||$_POST["change-js"])$K["target"]=array();else$K["source"][]="";}elseif($C!=""){$vd=foreign_keys($a);$K=$vd[$C];$K["source"][]="";}else{$K["table"]=$a;$K["source"]=array("");}echo'
<form action="" method="post">
';$Ui=array_keys(fields($a));if($K["db"]!="")connection()->select_db($K["db"]);if($K["ns"]!=""){$Wg=get_schema();set_schema($K["ns"]);}$bi=array_keys(array_filter(table_status('',true),'Adminer\fk_support'));$Cj=array_keys(fields(in_array($K["table"],$bi)?$K["table"]:reset($bi)));$b=on('change','foreignChange');echo"<p><label>".'Target table'.": ".html_select("table",$bi,$K["table"],$b)."</label>\n";if(JUSH!="sqlite"){$Rb=array();foreach(adminer()->databases()as$i){if(!information_schema($i))$Rb[]=$i;}echo"<label>".'DB'.": ".html_select("db",$Rb,$K["db"]!=""?$K["db"]:$_GET["db"],$b)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type=\'submit\' name=\'change\' value=\'Change\'></noscript>
<table>
<thead><tr><th id="label-source">Source<th id="label-target">Target<tbody>
';$Se=0;foreach($K["source"]as$w=>$X){echo"<tr>","<td>".html_select("source[".(+$w)."]",array(-1=>"")+$Ui,$X,($Se==count($K["source"])-1?on('change','foreignAddRow'):""),"label-source"),"<td>".html_select("target[".(+$w)."]",$Cj,idx($K["target"],$w),"","label-target");$Se++;}echo'</table>
<p>
<label>ON DELETE: ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$K["on_delete"]),'</label>
<label>ON UPDATE: ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$K["on_update"]),'</label>
',(support("deferrable")?html_select("deferrable",array('NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'),$K["deferrable"]).' ':''),doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",)),'<p>
<input type=\'submit\' value=\'Save\'>
<noscript><p><input type=\'submit\' name=\'add\' value=\'Add column\'></noscript>
';if($C!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$C)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$K=$_POST;$Xg="VIEW";if(JUSH=="pgsql"&&$a!=""){$P=table_status1($a);$Xg=strtoupper($P["Engine"]);}if($_POST&&!$k){$C=trim($K["name"]);$va=" AS\n$K[select]";$_=ME."table=".url_escape($C);$B='View has been altered.';$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$C&&JUSH!="sqlite"&&$U=="VIEW"&&$Xg=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($C).$va,$_,$B);else{$Gj="adminer_".uniqid();drop_create("DROP $Xg ".table($a),"CREATE $U ".table($C).$va,"DROP $U ".table($C),"CREATE $U ".table($Gj).$va,"DROP $U ".table($Gj),($_POST["drop"]?substr(ME,0,-1):$_),'View has been dropped.',$B,'View has been created.',$a,$C);}}if(!$_POST&&$a!=""){$K=view($a);$K["name"]=$a;$K["materialized"]=($Xg!="VIEW");if(!$k)$k=adminer()->error();}page_header(($a!=""?'Alter view':'Create view'),$k,array("table"=>$a),h($a));echo'
<form action="" method="post">
<p>Name: <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$K["materialized"],'Materialized view'):""),'<p>';textarea("select",$K["select"]);echo'<p>
<input type=\'submit\' value=\'Save\'>
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$a)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$Ge=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$fj=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$K=$_POST;if($_POST&&!$k){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),'Event has been dropped.');elseif(in_array($K["INTERVAL_FIELD"],$Ge)&&isset($fj[$K["STATUS"]])){$wi="\nON SCHEDULE ".($K["INTERVAL_VALUE"]?"EVERY ".q($K["INTERVAL_VALUE"])." $K[INTERVAL_FIELD]".($K["STARTS"]?" STARTS ".q($K["STARTS"]):"").($K["ENDS"]?" ENDS ".q($K["ENDS"]):""):"AT ".q($K["STARTS"]))." ON COMPLETION".($K["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?'Event has been altered.':'Event has been created.'),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$wi.($aa!=$K["EVENT_NAME"]?"\nRENAME TO ".idf_escape($K["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($K["EVENT_NAME"]).$wi)."\n".$fj[$K["STATUS"]]." COMMENT ".q($K["EVENT_COMMENT"]).rtrim(" DO\n$K[EVENT_DEFINITION]",";").";"));}}page_header(($aa!=""?'Alter event'.": ".h($aa):'Create event'),$k);if(!$K&&$aa!=""){$L=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$K=reset($L);}echo'
<form action="" method="post">
<table class="layout">
<tr><th>Name<td><input name="EVENT_NAME" value="',h($K["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">Start<td><input name="STARTS" value="',h("$K[EXECUTE_AT]$K[STARTS]"),'">
<tr><th title="datetime">End<td><input name="ENDS" value="',h($K["ENDS"]),'">
<tr><th>Every
<td><input type="number" name="INTERVAL_VALUE" value="',h($K["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$Ge,$K["INTERVAL_FIELD"]),'<tr><th>Status<td>',html_select("STATUS",$fj,$K["STATUS"]),'<tr><th>Comment<td><input name="EVENT_COMMENT" value="',h($K["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$K["ON_COMPLETION"]=="PRESERVE",'On completion preserve'),'</table>
<p>';textarea("EVENT_DEFINITION",$K["EVENT_DEFINITION"]);echo'<p>
<input type=\'submit\' value=\'Save\'>
';if($aa!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$aa)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ba=($_GET["name"]?:$_GET["procedure"]);$ri=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$K=$_POST;$K["fields"]=(array)$K["fields"];if($_POST&&!process_fields($K["fields"])&&!$k){foreach($K["fields"]as$w=>$l){if($l["field"]=="")unset($K["fields"][$w]);}$Bg=routine_id($ba,routine($_GET["procedure"],$ri));$lg=routine_id($K["name"],$K);$g=create_routine($ri,$K);$_=substr(ME,0,-1);$B='Routine has been altered.';if(!$_POST["drop"]&&$Bg==$lg&&connection()->flavor!="mysql")query_redirect(substr_replace($g,' OR REPLACE',6,0),$_,$B);else{$Gj="adminer_".uniqid();drop_create("DROP $ri $Bg",$g,"DROP $ri $lg",create_routine($ri,array("name"=>$Gj)+$K),"DROP $ri ".routine_id($Gj,$K),$_,'Routine has been dropped.',$B,'Routine has been created.',$ba,$K["name"]);}}page_header(($ba!=""?(isset($_GET["function"])?'Alter function':'Alter procedure').": ".h($ba):(isset($_GET["function"])?'Create function':'Create procedure')),$k);if(!$_POST){if($ba=="")$K["language"]="sql";else{$K=routine($_GET["procedure"],$ri);$K["name"]=$ba;}}$jb=(JUSH=="sql"?flat_collations():array());$si=routine_languages();echo($jb?"<datalist id='collations'>".optionlist($jb)."</datalist>":""),'
<form action="" method="post" id="form">
<p>Name: <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',($si?"<label>".'Language'.": ".html_select("language",$si,$K["language"])."</label>\n":""),'<input type=\'submit\' value=\'Save\'>
<div class="scrollable">
<table id="edit-fields" class="nowrap">
';edit_fields($K["fields"],$jb,$ri);if(isset($_GET["function"])){echo"<tr><td>".'Return type';edit_type("returns",(array)$K["returns"],$jb,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$K["definition"],20);echo'<p>
<input type=\'submit\' value=\'Save\'>
';if($ba!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$ba)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$C=$_GET["name"];$K=$_POST;if($K&&!$k){if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(),"",array(),"$C",($K["drop"]?"":$K["clause"]));else{$I=($C==""||queries("ALTER TABLE ".table($a)." DROP CONSTRAINT ".idf_escape($C)));if(!$K["drop"])$I=queries("ALTER TABLE ".table($a)." ADD".($K["name"]!=""?" CONSTRAINT ".idf_escape($K["name"]):"")." CHECK ($K[clause])");}queries_redirect(ME."table=".url_escape($a),($K["drop"]?'Check has been dropped.':($C!=""?'Check has been altered.':'Check has been created.')),$I);}page_header(($C!=""?'Alter check':'Create check'),$k,array("table"=>$a),h($C!=""?$C:$a));if(!$K){$ab=driver()->checkConstraints($a);$K=array("name"=>$C,"clause"=>$ab[$C]);}echo'
<form action="" method="post">
<p>';if(JUSH!="sqlite")echo'Name'.': <input name="name" value="'.h($K["name"]).'" data-maxlength="64" autocapitalize="off"> ';echo
doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",),"?"),'<p>';textarea("clause",$K["clause"]);echo'<p><input type=\'submit\' value=\'Save\'>
';if($C!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$C)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$C="$_GET[name]";$dk=trigger_options();$K=(array)trigger($C,$a)+array("Trigger"=>$a."_bi");if($_POST){if(!$k&&in_array($_POST["Timing"],$dk["Timing"])&&in_array($_POST["Event"],$dk["Event"])&&in_array($_POST["Type"],$dk["Type"])){$Eg=" ON ".table($a);$rc="DROP TRIGGER ".idf_escape($C).(JUSH=="pgsql"?$Eg:"");$_=ME."table=".url_escape($a);if($_POST["drop"])query_redirect($rc,$_,'Trigger has been dropped.');else{if($C!="")queries($rc);queries_redirect($_,($C!=""?'Trigger has been altered.':'Trigger has been created.'),queries(create_trigger($Eg,$_POST)));if($C!="")queries(create_trigger($Eg,$K+array("Type"=>reset($dk["Type"]))));}}$K=$_POST;}page_header(($C!=""?'Alter trigger':'Create trigger'),$k,array("table"=>$a),h($C!=""?$C:$a));$ck=on('change','triggerChange',"^".preg_quote($a,"/")."_[ba][iud]$",$a);echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>Time
<td>',html_select("Timing",$dk["Timing"],$K["Timing"],$ck),'<tr><th>Event<td>',html_select("Event",$dk["Event"],$K["Event"],$ck),(in_array("UPDATE OF",$dk["Event"])?" <input name='Of' value='".h($K["Of"])."' class='hidden'>":""),'<tr><th>Type<td>',html_select("Type",$dk["Type"],$K["Type"]),'<tr><th>Name<td><input name="Trigger" value="',h($K["Trigger"]),'" data-maxlength="64" autocapitalize="off">
</table>
',script("fire(qs('#form')['Timing'], 'change');"),'<p>';textarea("Statement",$K["Statement"]);echo'<p>
<input type=\'submit\' value=\'Save\'>
';if($C!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$C)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){function
grant($Fd,array$Ph,$d,$Eg){if(!$Ph)return
true;if($Ph==array("ALL PRIVILEGES","GRANT OPTION"))return($Fd=="GRANT"?queries("$Fd ALL PRIVILEGES$Eg WITH GRANT OPTION"):queries("$Fd ALL PRIVILEGES$Eg")&&queries("$Fd GRANT OPTION$Eg"));return
queries("$Fd ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$d, ",$Ph).$d).$Eg);}$da=$_GET["user"];$Ph=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$K){foreach(explode(",",($K["Privilege"]=="Grant option"?"":$K["Context"]))as$xb)$Ph[$xb=="File access on server"?"Server Admin":$xb][$K["Privilege"]]=$K["Comment"];}unset($Ph["Server Admin"]["Usage"]);foreach($Ph["Tables"]as$w=>$X)unset($Ph["Databases"][$w]);$kg=array();if($_POST){foreach($_POST["objects"]as$w=>$X)$kg[$X]=(array)$kg[$X]+idx($_POST["grants"],$w,array());}$Gd=array();if(isset($_GET["host"])&&($I=connection()->query("SHOW GRANTS FOR ".q($da)."@".q($_GET["host"])))){while($K=$I->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$K[0],$A)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$A[1],$xf,PREG_SET_ORDER)){foreach($xf
as$X){if($X[1]!="USAGE")$Gd["$A[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$K[0]))$Gd["$A[2]$X[2]"]["GRANT OPTION"]=true;}}}}if($_POST&&!$k){$Dg=(isset($_GET["host"])?q($da)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $Dg",ME."privileges=",'User has been dropped.');else{$ng=q($_POST["user"])."@".q($_POST["host"]);$ph=$_POST["pass"];$Db=false;$I=true;if($Dg!=$ng){$Db=queries("CREATE USER $ng IDENTIFIED BY ".($_POST["hashed"]?"PASSWORD ":"").q($ph));$I=$Db;}elseif($ph!="")$I=queries("SET PASSWORD FOR $ng = ".(min_version(8,99)||$_POST["hashed"]?q($ph):"PASSWORD(".q($ph).")"));if($I){$ni=array();foreach($kg
as$ug=>$Fd){if(isset($_GET["grant"]))$Fd=array_filter($Fd);$Fd=array_keys($Fd);if(isset($_GET["grant"]))$ni=array_diff(array_keys(array_filter($kg[$ug],'strlen')),$Fd);elseif($Dg==$ng){$Ag=array_keys((array)$Gd[$ug]);$ni=array_diff($Ag,$Fd);$Fd=array_diff($Fd,$Ag);unset($Gd[$ug]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$ug,$A)&&(!grant("REVOKE",$ni,$A[2]," ON $A[1] FROM $ng")||!grant("GRANT",$Fd,$A[2]," ON $A[1] TO $ng"))){$I=false;break;}}}if($I&&isset($_GET["host"])){if($Dg!=$ng)queries("DROP USER $Dg");elseif(!isset($_GET["grant"])){foreach($Gd
as$ug=>$ni){if(preg_match('~^(.+)(\(.*\))?$~U',$ug,$A))grant("REVOKE",array_keys($ni),$A[2]," ON $A[1] FROM $ng");}}}if($I&&!Queries::$queries)redirect(ME."privileges=");queries_redirect(ME."privileges=",(isset($_GET["host"])?'User has been altered.':'User has been created.'),$I);if($Db)connection()->query("DROP USER $ng");}}page_header((isset($_GET["host"])?'Username'.": ".h("$da@$_GET[host]"):'Create user'),$k,array("privileges"=>array('','Privileges')));$K=$_POST;if($K)$Gd=$kg;else{$K=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$Gd[(DB==""||$Gd?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>Server<td><input name="host" data-maxlength="60" value="',h($K["host"]),'" autocapitalize="off">
<tr><th>Username<td><input name="user" data-maxlength="80" value="',h($K["user"]),'" autocapitalize="off">
<tr><th>Password<td><input name="pass" id="pass" value="',h($K["pass"]),'" autocomplete="new-password">
',($K["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8,99)?"":checkbox("hashed",1,$K["hashed"],'Hashed',on('click','hashedClick'))),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".'Privileges'.doc_link(array('sql'=>"grant.html#priv_level"));$r=0;foreach($Gd
as$ug=>$Fd){echo'<th>'.($ug!="*.*"?"<input name='objects[$r]' value='".h($ug)."' size='10' autocapitalize='off'>":input_hidden("objects[$r]","*.*")."*.*");$r++;}echo"<tbody>\n";foreach(array(""=>"","Server Admin"=>'Server',"Databases"=>'Database',"Tables"=>'Table',"Procedures"=>'Routine',)as$xb=>$Zb){foreach((array)$Ph[$xb]as$Oh=>$nb){echo"<tr><td".($Zb?">$Zb<td":" colspan='2'").' lang="en" title="'.h($nb).'">'.h($Oh);$r=0;foreach($Gd
as$ug=>$Fd){$C="'grants[$r][".h(strtoupper($Oh))."]'";$Y=$Fd[strtoupper($Oh)];if($xb=="Server Admin"&&$ug!=(isset($Gd["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$C><option><option value='1'".($Y?" selected":"").">".'Grant'."<option value='0'".($Y=="0"?" selected":"").">".'Revoke'."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$C value='1'".($Y?" checked":"").($Oh=="All privileges"?" id='grants-$r-all'":($Oh=="Grant option"?"":on('click','grantsClick',"grants-$r-all"))).">","</label>";$r++;}}}echo"</table>\n",'<p>
<input type=\'submit\' value=\'Save\'>
';if(isset($_GET["host"]))echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',"$da@$_GET[host]")),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$k){$Ye=0;foreach((array)$_POST["kill"]as$X){if(adminer()->killProcess($X))$Ye++;}queries_redirect(ME."processlist=",lang_format(array('%d process has been killed.','%d processes have been killed.'),$Ye),$Ye||!$_POST["kill"]);}}page_header('Process list',$k);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds"',on('click','tableClick').on('dblclick','tableClick'),'>
';$r=-1;foreach(adminer()->processList()as$r=>$K){if(!$r){echo"<thead><tr lang='en'>".(support("kill")?"<td class='hover'>":"");foreach($K
as$w=>$X)echo"<th>$w".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($w),));echo"<tbody>\n";}echo"<tr>".(support("kill")?"<td class='hover'>".checkbox("kill[]",$K[JUSH=="sql"?"Id":"pid"],0):"");foreach($K
as$w=>$X)echo"<td>".($X!=""&&((JUSH=="sql"&&$w=="Info"&&preg_match("~Query|Killed~",$K["Command"]))||(JUSH=="pgsql"&&$w=="query")||(JUSH=="oracle"&&$w=="sql_text"))?"<code class='jush-".JUSH."' data-full='".h($X)."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(($K["db"]!=""?preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($K["db"])."&":ME)."sql=".url_escape($X)).'">'.'Clone'.'</a>'.' '.copy_icon():h($X));echo"\n";}echo'</table>
</div>
<p>
',script("copyCode(qsl('table'));");if(support("kill"))echo($r+1)."/".sprintf('%d in total',max_connections()),"<p><input type='submit' value='".'Kill'."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif($_GET["select"]!=""){$a=$_GET["select"];$S=table_status1($a);$v=indexes($a);$m=fields($a);$vd=column_foreign_keys($a);$_g=$S["Oid"];$ja=get_settings("adminer_import");$pi=array();$d=array();$Ai=array();$Pg=array();$Jj=null;foreach($m
as$w=>$l){$C=adminer()->fieldName($l);$hg=html_entity_decode(strip_tags($C),ENT_QUOTES);if(isset($l["privileges"]["select"])&&$C!=""){$d[$w]=$hg;if(is_shortable($l))$Jj=adminer()->selectLengthProcess();}if(isset($l["privileges"]["where"])&&$C!="")$Ai[$w]=$hg;if(isset($l["privileges"]["order"])&&$C!="")$Pg[$w]=$hg;$pi+=$l["privileges"];}list($M,$Id)=adminer()->selectColumnsProcess($d,$v);$M=array_unique($M);$Id=array_unique($Id);$Me=count($Id)<count($M);$Z=adminer()->selectSearchProcess($m,$v);$D=adminer()->selectOrderProcess($m,$v);$y=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$nk=>$K){$va=convert_field($m[key($K)]);$M=array($va?:idf_escape(key($K)));$Z[]=where_check(bracket_escape($nk,true),$m);$J=driver()->select($a,$M,$Z,$M);if($J)echo
first($J->fetch_row());}exit;}$Lh=$pk=array();foreach($v
as$u){if($u["type"]=="PRIMARY"){$Lh=array_flip($u["columns"]);$pk=($M?$Lh:array());foreach($pk
as$w=>$X){if(in_array(idf_escape($w),$M))unset($pk[$w]);}break;}}if($_g&&!$Lh){$Lh=$pk=array($_g=>0);$v[]=array("type"=>"PRIMARY","columns"=>array($_g));}if($_POST&&!$k){$Sk=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$ab=array();foreach($_POST["check"]as$Xa)$ab[]=where_check($Xa,$m);$Sk[]="((".implode(") OR (",$ab)."))";}$Uk=$Sk;$Sk=($Sk?"\nWHERE ".implode(" AND ",$Sk):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$Ci=($M?:array("*"));$zb=convert_fields($d,$m,$M);if($zb)$Ci[]=substr($zb,2);$H="";if(is_array($_POST["check"])&&!$Lh){$zd=implode(", ",$Ci)."\nFROM ".table($a);$Kd=($Id&&$Me?"\nGROUP BY ".implode(", ",$Id):"").($D?"\nORDER BY ".implode(", ",$D):"");$lk=array();foreach($_POST["check"]as$X)$lk[]="(SELECT".limit($zd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$m).$Kd,1).")";$H=implode(" UNION ALL ",$lk);}adminer()->dumpData($a,"table",$H,$Ci,$Uk,($Me?$Id:array()),$D);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$vd)){if($_POST["save"]||$_POST["delete"]){$I=true;$ka=0;$Ja=false;$O=array();if(!$_POST["delete"]){foreach($m
as$C=>$X){$t=bracket_escape($C);if(isset($_POST["fields"][$t])||$_FILES["fields-$t"]){$X=process_input($m[$C]);if($X!==null&&($_POST["clone"]||$X!==false))$O[idf_escape($C)]=($X!==false?$X:idf_escape($C));}}}if($_POST["delete"]||$O){$H=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($O)).")\nSELECT ".implode(", ",$O)."\nFROM ".table($a):"");if($_POST["all"]||($Lh&&is_array($_POST["check"]))||$Me){$I=($_POST["delete"]?driver()->delete($a,$Sk):($_POST["clone"]?queries("INSERT $H$Sk".driver()->insertReturning($a)):driver()->update($a,$O,$Sk)));$ka=connection()->affected_rows;if(is_object($I))$ka+=$I->num_rows;}else{$Ja=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$X){$Rk="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$m);$I=($_POST["delete"]?driver()->delete($a,$Rk,1):($_POST["clone"]?queries("INSERT".limit1($a,$H,$Rk)):driver()->update($a,$O,$Rk,1)));if(!$I)break;$ka+=connection()->affected_rows;}if($Ja&&$I&&!driver()->commit())$I=false;}}$B=lang_format(array('%d item has been affected.','%d items have been affected.'),$ka);if($_POST["clone"]&&$I&&$ka==1){$ef=last_id($I);if($ef)$B=sprintf('Item%s has been inserted.'," $ef");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$B,$I);if($Ja)driver()->rollback();if(!$_POST["delete"]){$Eh=(array)$_POST["fields"];edit_form($a,array_intersect_key($m,$Eh),$Eh,!$_POST["clone"],$k);page_footer();exit;}}elseif(!$_POST["import"]){$I=true;$ka=0;$Ja=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$nk=>$K){$O=array();foreach($K
as$w=>$X){$w=bracket_escape($w,true);$O[idf_escape($w)]=(preg_match('~char|text~',$m[$w]["type"])||$X!=""?adminer()->processInput($m[$w],$X):"NULL");}$I=driver()->update($a,$O," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($nk,true),$m),($Me||$Lh?0:1)," ");if(!$I)break;$ka+=connection()->affected_rows;}if($Ja)$I=$I&&driver()->commit();queries_redirect(remove_from_uri(),lang_format(array('%d item has been affected.','%d items have been affected.'),$ka),$I);if($Ja)driver()->rollback();}elseif(!is_string($id=get_file("csv_file",true)))$k=upload_error($id);elseif(!preg_match('~~u',$id))$k='File must be in UTF-8 encoding.';else{save_settings(array("output"=>$ja["output"],"format"=>$_POST["separator"]),"adminer_import");$kb=array_keys($m);$Hi=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Hb=parse_csv($id,$Hi);$ka=count($Hb);driver()->begin();$L=array();foreach($Hb
as$w=>$Hk){if(!$w&&!array_diff($Hk,$kb)){$kb=$Hk;$ka--;}else{$O=array();foreach($Hk
as$r=>$gb)$O[idf_escape($kb[$r])]=($gb==""&&$m[$kb[$r]]["null"]?"NULL":q(csv_value($gb)));$L[]=$O;}}$I=(!$L||driver()->insertUpdate($a,$L,$Lh));if($I)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang_format(array('%d row has been imported.','%d rows have been imported.'),$ka),$I);driver()->rollback();}}}$sj=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header('Select'.": $sj",$k);$O=null;if(isset($pi["insert"])||!support("table")){$O="";foreach((array)$_GET["where"]as$X){$Y=$X["val"];if(is_array($Y))$Y=(count($Y)==1&&preg_match('~^val-(.*)~s',reset($Y),$A)?$A[1]:"");if($X["col"]!=""&&$Y!=""&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$Y)))))$O
.="&set[".url_escape(bracket_escape($X["col"]))."]=".url_escape($Y);}}adminer()->selectLinks($S,$O);if(!$d&&support("table"))echo"<p class='error'>".'Unable to select the table'.($m?".":": ".adminer()->error())."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($M,$d);adminer()->selectSearchPrint($Z,$Ai,$v);adminer()->selectOrderPrint($D,$Pg,$v);adminer()->selectLimitPrint($y);if($Jj!==null)adminer()->selectLengthPrint($Jj);adminer()->selectActionPrint($v);echo"</form>\n";foreach((array)$_GET["where"]as$X){if($X["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".'Invalid CSRF token. Submit the form again.'.' '.'If you did not send this request from Adminer, close this page.'."\n";page_footer();exit;}}$E=$_GET["page"];$yd=null;if($E=="last"){$yd=get_val(count_rows($a,$Z,$Me,$Id));$E=floor(max(0,intval($yd)-1)/$y);}$Bi=$M;$Jd=$Id;if(!$Bi){$Bi[]="*";$zb=convert_fields($d,$m,$M);if($zb)$Bi[]=substr($zb,2);}foreach($M
as$w=>$X){$l=$m[idf_unescape($X)];if($l&&($va=convert_field($l)))$Bi[$w]="$va AS $X";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$w=>$X){if(isset($Bi[$w])&&$X["fun"])$Bi[$w].=" AS ".idf_escape(apply_sql_function($X["fun"],($X["col"]!=""?$X["col"]:"*")));}}if(!$Me&&$pk){foreach($pk
as$w=>$X){$Bi[]=idf_escape($w);if($Jd)$Jd[]=idf_escape($w);}}$I=driver()->select($a,$Bi,$Z,$Jd,$D,$y,$E,true);if(!is_object($I))echo"<p class='error'>".(adminer()->error()?:'Unknown error.')."\n";else{if(JUSH=="mssql"&&$E)$I->seek($y*$E);$Ac=array();$L=array();while($K=$I->fetch_assoc()){if($E&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}$Ud=($y&&(support("cursor")?$_GET["next"]!="":count($L)>=$y));if(is_ajax()&&$Ud)header("X-Next-Page: ".pagination_href($E+1));if($_GET["modify"]&&$L){$Ff=max_input_vars(count($L[0])+1,20);echo($Ff&&count($L)>$Ff?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'".on_upload_progress($uk).">\n";if($_GET["page"]!="last"&&$y&&$Id&&$Me&&JUSH=="sql")$yd=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".'No rows.'."\n";else{$Fa=adminer()->backwardKeys($a,$sj);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$Id&&$M?"":"<td class='hover check'><input type='checkbox' id='all-page' class='jsonly' title='".'All rows on this page'."'".on('click','formCheck','^check').">");$ig=array();$Cd=array();reset($M);$Yh=1;foreach($L[0]as$w=>$X){if(!isset($pk[$w])){$X=idx($_GET["columns"],key($M))?:array();$l=$m[$M?($X?$X["col"]:current($M)):$w];$C=($l?adminer()->fieldName($l,$Yh):($X["fun"]?"*":h($w)));if($C!=""){$Yh++;$ig[$w]=$C;$c=idf_escape($w);$he=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($w);$Zb="&desc[0]=1";$Ri=preg_replace('~ DESC( NULLS LAST)?$~','',$D[0]);$Ti=($Ri==$c||$Ri==$w);echo"<th id='th[".h(bracket_escape($w))."]'".($Ti?" aria-sort='".($Ri==$D[0]?"ascending":"descending")."'":"").">";$Bd=apply_sql_function($X["fun"],$C);$Si=isset($l["privileges"]["order"])||$Bd!=$C;echo($Si?"<a href='".h($he.($Ti&&$Ri==$D[0]?$Zb:''))."'>$Bd</a>":$Bd);$Nf=($Si?"<a href='".h($he.$Zb)."' title='".'descending'."' class='text'> ↓</a>":'');if(!$X["fun"]&&isset($l["privileges"]["where"]))$Nf
.="<a href='#fieldset-search' title='".'Search'."' class='text jsonly'".on('click','selectSearch',$w)."> =</a>";echo($Nf?"<span class='column'>$Nf</span>":"");}$Cd[$w]=$X["fun"];next($M);}}$lf=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$w=>$X)$lf[$w]=max($lf[$w],min(40,strlen(utf8_decode($X))));}}echo($Fa?"<th>".'Relations':"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($L,$vd)as$fg=>$K){$mk=unique_array($L[$fg],$v);if(!$mk){$mk=array();reset($M);foreach($L[$fg]as$w=>$X){if(!preg_match('~^(COUNT|AVG|GROUP_CONCAT|MAX|MIN|SUM)\(~',current($M)))$mk[$w]=$X;next($M);}}$nk="";foreach($mk
as$w=>$X){$l=(array)$m[$w];$Le=is_blob($l);if((JUSH=="sql"||JUSH=="pgsql")&&($Le||preg_match('~'.text_type().'~',$l["type"]))&&strlen($X)>64){$w=(strpos($w,'(')?$w:idf_escape($w));$w="MD5(".($Le||JUSH!='sql'||preg_match("~^utf8~",$l["collation"])?$w:"CONVERT($w USING ".charset(connection()).")").")";$X=md5($Le?(string)driver()->value($X,$l):$X);}$nk
.="&".($X!==null?"where[".url_escape(bracket_escape($w))."]=".url_escape($X===false?"f":$X):"null[]=".url_escape($w));}echo"<tr>".(!$Id&&$M?"":"<td class='hover check'>".($Me||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$nk)."' class='edit'>".'edit'."</a> ").checkbox("check[]",substr($nk,1),in_array(substr($nk,1),(array)$_POST["check"])));reset($M);foreach($K
as$w=>$X){if(isset($ig[$w])){$c=current($M);$l=(array)$m[$w];if($X!=""&&(!isset($Ac[$w])||$Ac[$w]!=""))$Ac[$w]=(is_mail($X)?$ig[$w]:"");$z="";if(is_blob($l)&&$X!="")$z=ME.'download='.url_escape($a).'&field='.url_escape($w).$nk;if(!$z&&$X!==null){foreach((array)$vd[$w]as$o){if(count($vd[$w])==1||end($o["source"])==$w){$z="";foreach($o["source"]as$r=>$Ui)$z
.=where_link($r,$o["target"][$r],$L[$fg][$Ui]);$z=($o["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.url_escape($o["db"]),ME):ME).'select='.url_escape($o["table"]).$z;if($o["ns"])$z=preg_replace('~([?&]ns=)[^&]+~','\1'.url_escape($o["ns"]),$z);if(count($o["source"])==1)break;}}}if($c=="COUNT(*)"){$z=ME."select=".url_escape($a);$r=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$mk))$z
.=where_link($r++,$W["col"],$W["val"],$W["op"]);}foreach($mk
as$Ve=>$W)$z
.=where_link($r++,$Ve,$W);}$ie=select_value($X,$z,$l,$Jj);$t=bracket_escape($nk);$s=h("val[$t][".bracket_escape($w)."]");$Gh=idx(idx($_POST["val"],$t),bracket_escape($w));$sk=idx($l["privileges"],"update");$xc=!is_array($K[$w])&&!is_blob($l)&&is_utf8($X)&&$L[$fg][$w]==$X&&!$Cd[$w]&&!$l["generated"]&&$sk;$U=(preg_match('~^(AVG|MIN|MAX)\((.+)\)~',$c,$A)?$m[idf_unescape($A[2])]["type"]:$l["type"]);$Ij=preg_match('~text|json|lob~',$U);$Ne=preg_match(number_type(),$U)||preg_match('~^(CHAR_LENGTH|ROUND|FLOOR|CEIL|TIME_TO_SEC|COUNT|SUM)\(~',$c);echo"<td id='$s'".($Ne&&($X===null||is_numeric(strip_tags($ie))||$U=="money")?" class='number'":"");if(($_GET["modify"]&&$xc&&$X!==null)||$Gh!==null){$Pd=h($Gh!==null?$Gh:$X);echo">".($Ij?"<textarea name='$s' cols='30' rows='".(substr_count($X,"\n")+1)."'>$Pd</textarea>":"<input name='$s' value='$Pd' size='$lf[$w]'>");}else{$uf=strpos($ie,"<i>…</i>");echo($sk?" data-text='".($uf?2:($Ij?1:0))."'".($xc?"":" data-warning='".'Use the edit link to modify this value.'."'"):"").">$ie";}}next($M);}if($Fa)echo"<td>";adminer()->backwardKeysPrint($Fa,$L[$fg]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){if($L||$E||$Ud){$Pc=true;if($_GET["page"]!="last"){if(!$y||(count($L)<$y&&($L||!$E)))$yd=($E?$E*$y:0)+count($L);elseif(JUSH!="sql"||!$Me){$yd=($Me?false:found_rows($S,$Z));if(intval($yd)<max(1e4,2*($E+1)*$y))$yd=first(slow_query(count_rows($a,$Z,$Me,$Id)));elseif(JUSH=='sql'||JUSH=='pgsql')$Pc=false;}}if(!support("cursor"))$Ud=(($yd===false?count($L)+1:$yd-$E*$y)>$y);$dh=($y&&($Ud||$E));if($dh)echo($Ud?'<p><a href="'.h(pagination_href($E+1)).'" class="loadmore"'.on('click','selectLoadMore','Loading…').'>'.'Load more data'.'</a>':''),"\n";echo"<div class='footer'><div>\n";if($dh){$Df=($yd===false?$E+($L?(count($L)>=$y?2:1):0):floor(($yd-1)/$y));echo"<fieldset><legend>".'Page'."</legend>";if(!support("cursor")){echo
pagination(0,$E).($E>5?" …":"");for($r=max(1,$E-4);$r<min($Df,$E+5);$r++)echo
pagination($r,$E);if($Df>0)echo($E+5<$Df?" …":""),($Pc&&$yd!==false?pagination($Df,$E):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Df'>".'last'."</a>");}else
echo
pagination(0,$E).($E>1?" …":""),($E?pagination($E,$E):""),($Ud?pagination($E+1,$E)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".'Whole result'."</legend>";$hc=($Pc?"":"~ ").$yd;$Ze=($yd!==false?($Pc?"":"~ ").lang_format(array('%d row','%d rows'),$yd):"");echo
checkbox("all",1,0,$Ze,on('click','countRows',$hc))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".'Ctrl+click on a value to modify it.'."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>Modify</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'Save\'',($_GET["modify"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>Selected <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'Edit\'>
<input type=\'submit\' name=\'clone\' value=\'Clone\'>
<input type=\'submit\' name=\'delete\' value=\'Delete\'',confirm(),'>
</div></fieldset>
';$wd=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$c){if($c["fun"]){unset($wd['sql']);break;}}if($wd){print_fieldset("export",'Export'." <span id='selected2'></span>");$bh=adminer()->dumpOutput();echo($bh?html_select("output",$bh,$ja["output"])." ":""),html_select("format",$wd,$ja["format"])," <input type='submit' name='export' value='".'Export'."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($Ac,'strlen'),$d);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".'Import'."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($uk?input_hidden(ini_get("session.upload_progress.name"),$uk):""),file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$ja["format"])." <input type='submit' name='import' value='".'Import'."'>".($uk?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$Id&&$M?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$P=isset($_GET["status"]);page_header($P?'Status':'Variables');$Ik=($P?adminer()->showStatus():adminer()->showVariables());if(!$Ik)echo"<p class='message'>".'No rows.'."\n";else{echo"<table>\n";foreach($Ik
as$K){echo"<tr>";$w=array_shift($K);echo"<th><code class='jush-".JUSH.($P?"status":"set")."'>".h($w)."</code>";foreach($K
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: application/json; charset=utf-8");if($_GET["script"]=="db"){$mj=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$C=>$S){json_row("Comment-$C",h($S["Comment"]));if(!is_view($S)||preg_match('~materialized~i',$S["Engine"])){foreach(array("Engine","Collation")as$w)json_row("$w-$C",h($S[$w]));foreach(array_keys($mj+array("Auto_increment"=>0,"Rows"=>0))as$w){if(array_key_exists($w,$S))json_row("$w-$C",format_status($S,$w));if($S[$w]!=""&&isset($mj[$w]))$mj[$w]+=($S["Engine"]!="InnoDB"||$w!="Data_free"?$S[$w]:0);}}}if(function_exists('Adminer\db_status'))$mj=db_status();foreach($mj
as$w=>$X)json_row("sum-$w",format_number($X));json_row("");}elseif($_GET["script"]=="kill"){if(!$k)connection()->query("KILL ".number($_POST["kill"]));}else{foreach(count_tables(adminer()->databases(false))as$i=>$X){json_row("tables-$i",$X);json_row("size-$i",db_size($i));}json_row("");}exit;}else{if(!isset($_GET["select"])&&support("single_table")){$T=tables_list();if($T)redirect(ME.(support("table")?"table=":"select=").url_escape(key($T)));}$Kf=ME.(isset($_GET["select"])?"select=&":"");$Aj=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($Aj&&!$k&&!$_POST["search"]){$I=true;$B="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$I=truncate_tables($_POST["tables"]);$B='Tables have been truncated.';}elseif($_POST["move"]){$I=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$B='Tables have been moved.';}elseif($_POST["copy"]){$I=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$B='Tables have been copied.';}elseif($_POST["drop"]){if($_POST["views"])$I=drop_views($_POST["views"]);if($I&&$_POST["tables"])$I=drop_tables($_POST["tables"]);$B='Tables have been dropped.';}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$K)$B
.="<b>".h($R)."</b>: ".h($K["integrity_check"])."<br>";}}elseif(JUSH!="sql"){$I=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$B='Tables have been optimized.';}elseif(!$_POST["tables"])$B='No tables.';elseif($I=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($K=$I->fetch_assoc())$B
.="<b>".h($K["Table"])."</b>: ".h($K["Msg_text"])."<br>";}queries_redirect(relative_uri(),$B,$I);}page_header(($_GET["ns"]==""?'Database'.": ".h(DB):'Schema'.": ".h($_GET["ns"])),$k,true);if(adminer()->homepage()){if($_GET["ns"]!==""){$D=$_GET["order"];$_d=($D||support("fast_status"));echo"<div>\n","<h3 id='tables-views'>".'Tables and views'."</h3>\n";$_j=($_d?table_status():tables_list());if(!$_j)echo"<p class='message'>".'No tables.'."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".'Search data in tables'." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'".on('keydown','submitKeydown','search').">"," <input type='submit' name='search' value='".'Search'."'>\n","</div></fieldset>\n";if(!$k&&$_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr class="wrap">','<td class="hover"><input id="check-all" type="checkbox" class="jsonly" title="'.'All'.'"'.on('click','formCheck','^(tables|views)\[').'>','<th'.(!$D&&JUSH!='sqlite'?" aria-sort='ascending'":'').'><a href="'.h(substr($Kf,0,-1)).'">'.'Table'.'</a>';$d=array("Engine"=>array('Engine'.doc_link(array('sql'=>'storage-engines.html'))));if(collations())$d["Collation"]=array('Collation'.doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')));if(function_exists('Adminer\alter_table'))$d["Data_length"]=array('Data Length'.doc_link(array('sql'=>'show-table-status.html',)),"create",'Alter table',);if(support("indexes"))$d["Index_length"]=array('Index Length'.doc_link(array('sql'=>'show-table-status.html',)),"indexes",'Alter indexes',);$d["Data_free"]=array('Data Free'.doc_link(array('sql'=>'show-table-status.html')),"edit",'New item');if(function_exists('Adminer\alter_table'))$d["Auto_increment"]=array('Auto Increment'.doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),"auto_increment=1&create",'Alter table',);$d["Rows"]=array('Rows'.doc_link(array('sql'=>'show-table-status.html',)),"select",'Select data',);if(support("comment"))$d["Comment"]=array('Comment'.doc_link(array('sql'=>'show-table-status.html',)));$wa=array('Engine','Collation','Comment');foreach($d
as$w=>$c)echo"<th".($D==$w?" aria-sort='".(in_array($w,$wa)?"ascending":"descending")."'":"")."><a href='".h($Kf)."order=$w'>$c[0]</a>";echo"<tbody>\n";if($D){uasort($_j,function($fa,$Ca)use($D,$wa){$J=($fa[$D]<$Ca[$D]?-1:($fa[$D]>$Ca[$D]?1:0));return(in_array($D,$wa)?$J:-$J);});}$T=0;$mj=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach($_j
as$C=>$P){$Lk=($_d?is_view($P):$P!==null&&!preg_match('~table|sequence~i',$P));$P=($_d?$P:array('Engine'=>$P));$s=h("Table-".$C);echo'<tr><td class="hover">'.checkbox(($Lk?"views[]":"tables[]"),$C,in_array("$C",$Aj,true),"","","",$s),'<th>'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".url_escape($C)."' title='".'Show structure'."' id='$s'>".h($C).'</a>':h($C));if($Lk&&!preg_match('~materialized~i',$P['Engine'])){$Oj='View';echo'<td colspan="'.(count($d)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".url_escape($C)."' title='".'Alter view'."'>$Oj</a>":$Oj),"<td align='right'><a href='".h(ME)."select=".url_escape($C)."' title='".'Select data'."'>?</a>";if(support("comment"))echo'<td>'.h($P['Comment']);}else{if($_d){foreach(array_keys($mj)as$w)$mj[$w]+=($P["Engine"]!="InnoDB"||$w!="Data_free"?idx($P,$w):0);}foreach($d
as$w=>$c){$s=" id='$w-".h($C)."'";echo($c[1]?"<td align='right'><a href='".h(ME."$c[1]=").url_escape($C)."'$s title='$c[2]'>".format_status($P,$w)."</a>":"<td$s>".h(idx($P,$w,'?')));}$T++;}echo"\n";}echo"<tr><td class='hover'><th>".sprintf('%d in total',count($_j)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),(collations()?"<td>".h(db_collation(DB,collations())):'');if($_d&&function_exists('Adminer\db_status'))$mj=db_status();foreach($mj
as$w=>$lj)echo($d[$w]?"<td align='right' id='sum-$w'>".($_d?format_number($lj):""):"");echo"\n","</table>\n",($_d?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$Ek="<input type='submit' value='".'Vacuum'."'".on_help("VACUUM")."> ";$Lg="<input type='submit' name='optimize' value='".'Optimize'."'".on_help(JUSH=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE")."> ";$Mh=(JUSH=="sqlite"?$Ek."<input type='submit' name='check' value='".'Check'."'".on_help("PRAGMA integrity_check")."> ":(JUSH=="pgsql"?$Ek.$Lg:(JUSH=="sql"?"<input type='submit' value='".'Analyze'."'".on_help("ANALYZE TABLE")."> ".$Lg."<input type='submit' name='check' value='".'Check'."'".on_help("CHECK TABLE")."> "."<input type='submit' name='repair' value='".'Repair'."'".on_help("REPAIR TABLE")."> ":""))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".'Truncate'."'".confirm().on_help(JUSH=="sqlite"?"DELETE":"TRUNCATE".(JUSH=="pgsql"?"":" TABLE"))."> ":"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".'Drop'."'".confirm().on_help("DROP TABLE").">":"");echo($Mh?"<div class='footer'><div>\n<fieldset><legend>".'Selected'." <span id='selected'></span></legend><div>$Mh\n</div></fieldset>\n":"");$h=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($h)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".'Move to another database'." <span id='selected3'></span></legend><div>";$i=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($h?html_select("target",$h,$i):'<input name="target" value="'.h($i).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".'Move'."'>",(support("copy")?" <input type='submit' name='copy' value='".'Copy'."'> ".checkbox("overwrite",1,$_POST["overwrite"],'overwrite'):""),"</div></fieldset>\n";}echo"<input type='hidden' name='all' value=''".on('click','countTables',$T).">\n",input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links hover'><a href='".h(ME)."create='>".'Create table'."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".'Create view'."</a>\n":""),"</div>\n";if(support("routine")){echo"<div>\n","<h3 id='routines'>".'Routines'."</h3>\n";$ui=routines();if($ui){echo"<table class='odds'>\n",'<thead><tr><th>'.'Name'.'<td>'.'Type'.'<td>'.'Return type'."<td class='hover'><tbody>\n";foreach($ui
as$K){$C=($K["SPECIFIC_NAME"]==$K["ROUTINE_NAME"]?"":"&name=".url_escape($K["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').url_escape($K["SPECIFIC_NAME"]).$C).'" title="'.'Call'.'">'.h($K["ROUTINE_NAME"]).'</a>','<td>'.h($K["ROUTINE_TYPE"]),'<td>'.h($K["DTD_IDENTIFIER"]),'<td class="hover"><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').url_escape($K["SPECIFIC_NAME"]).$C).'">'.'Alter'."</a>";}echo"</table>\n";}echo'<p class="links hover">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.'Create procedure'.'</a>':'').'<a href="'.h(ME).'function=">'.'Create function'."</a>\n","</div>\n";}if(support("event")){echo"<div>\n","<h3 id='events'>".'Events'."</h3>\n";$L=get_rows("SHOW EVENTS");if($L){echo"<table>\n","<thead><tr><th>".'Name'."<td>".'Schedule'."<td>".'Start'."<td>".'End'."<td class='hover'><tbody>\n";foreach($L
as$K)echo"<tr>","<th>".h($K["Name"]),"<td>".($K["Execute at"]?'At given time'."<td>".h($K["Execute at"]):'Every'." ".h($K["Interval value"])." ".h($K["Interval field"])."<td>".h($K["Starts"])),"<td>".h($K["Ends"]),'<td class="hover"><a href="'.h(ME).'event='.url_escape($K["Name"]).'">'.'Alter'.'</a>';echo"</table>\n";$Mc=get_val("SELECT @@event_scheduler");if($Mc&&$Mc!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($Mc)."\n";}echo'<p class="links hover"><a href="'.h(ME).'event=">'.'Create event'."</a>\n","</div>\n";}}}}page_footer();