<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
$total = 25;
function Arrondir($oui){
    if ($oui-(int)$oui>=0.5){
        return (int)$oui+1;
    }
    return (int)$oui;
}

function CalculPoint($la)
{
    $bonus = 0;
    if ($la > 100) {
        $bonus = $la / 100;
    }
    $la = $la * 0.3 + $bonus;
    $la = Arrondir($la);
    return $la;
}
    echo CalculPoint($total);





?>