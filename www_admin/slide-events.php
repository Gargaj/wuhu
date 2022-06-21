<?php

if (($data = @file_get_contents(sys_get_temp_dir() . "/.wuhu.slide-events")) === false) {
    http_response_code(404);
    echo "<html><body>404: file not found</body></html>";
} else {
    header("Content-type: text/xml");
    echo $data;
}
?>
