<?php
     function debug($x) {
         file_put_contents("/tmp/mikem", $x."\n", FILE_APPEND);
     }
?>
