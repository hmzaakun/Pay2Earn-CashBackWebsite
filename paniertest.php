<?php

$dette = 1;
for ($b = 0; $b < 10 && $dette == 1 ; $b++) {
    if($b==5) {
        echo $b;
        $dette = 0;
    }else{
        echo $b;
    }


}

?>