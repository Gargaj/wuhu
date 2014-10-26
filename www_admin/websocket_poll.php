<?
include_once(dirname(__FILE__)."/bootstrap.inc.php");

$listener = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($listener, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($listener, 0, 31337);
socket_listen($listener);
$clients = array($listener);

function decode($data) 
{
	$length = ord($data[1]) & 0x7F;

	if ($length == 126) 
	{
		$masks = substr($data, 2 + 2, 4);
		$str = substr($data, 8);
	}
	else if ($length == 127) 
	{
		$masks = substr($data, 2 + 8, 4);
		$str = substr($data, 14);
	}
	else 
	{
		$masks = substr($data, 2 + 0, 4);
		$str = substr($data, 6);
	}

	$text = '';
  for ($i = 0; $i < strlen($str); $i++)
		$text .= $str[$i] ^ $masks[$i & 3];
	
	return $text;
}

function encode($text)
{
	$b1 = 0x8f;
	$length = strlen($text);

	if($length <= 125) 		
	  $header = pack('CC', $b1, $length); 	
	elseif($length > 125 && $length < 65536) 		
	  $header = pack('CCS', $b1, 126, $length); 	
	elseif($length >= 65536)
		$header = pack('CCN', $b1, 127, $length);

	return $header . $text;
}

echo "Opening socket...\n";
while (true) 
{
  $read = $clients;
  $write = $clients;
  $except = $clients;
  
  $n = socket_select($read, $write, $except, 0, 10 * 1000);
  if ($n === false)
  {
    echo "socket_select() failed, reason: " . socket_strerror(socket_last_error()) . "\n";
  }
  if ($n >= 1)
  {
    if (in_array($listener, $read)) 
    {
      $newsock = socket_accept($listener);
      
      socket_getpeername($newsock, $ip);
      echo "Client ".$ip." connected...\n";
      
      $init = socket_read($newsock,1024);

      if (preg_match("/Sec-WebSocket-Key: (.*)/",$init,$m))
      {
        echo "Websocket key: " . $m[1] . "\n";
        $sha = sha1(trim($m[1])."258EAFA5-E914-47DA-95CA-C5AB0DC85B11",true);
        //$u = unpack("H*",$sha);
        $res = base64_encode($sha);
        
        echo "Websocket response: " . $res . "\n";
        
        $s = "HTTP/1.1 101 Switching Protocols\r\n";
        $s .= "Upgrade: websocket\r\n";
        $s .= "Connection: Upgrade\r\n";
        $s .= "Sec-WebSocket-Accept: ".$res."\r\n";
        $s .= "\r\n";
        
        socket_write($newsock,$s);
        
        $clients[] = $newsock;
      }
    }
  }
 
  $fn = ADMIN_DIR . "/.sockCommand";
  $f = fopen($fn,"c+");
  if ($f) 
  {
    flock($f,LOCK_EX);

    $s = "";
    while(!feof($f)) $s .= fread($f,1024);
    ftruncate($f,0);
    
    flock($f,LOCK_UN);
    fclose($f);
    
    if ($s)
    {
      echo "Sending: ".$s."\n";
      foreach ($clients as $k=>$sock) 
      {
        if ($sock == $listener)
          continue;
        if (socket_write($sock,encode($s)) == false)
        {
          unset($clients[$k]);
        }
        echo "Sent...\n";
      }
    }
  } 
  
  usleep( 1000 * 2 );
}
socket_close($sock);
?>