<?php
	require 'myrrix/vendor/autoload.php';
    $myrrix = new BCC\Myrrix\MyrrixService('localhost', 9090);

    $preferences = array();

    $myrrix->setPreference(11,101,0.3);
    $myrrix->setPreference(11,102,0.3);

    $myrrix->setPreference(12,101,0.3);
    $myrrix->setPreference(12,103,0.3);
    $myrrix->setPreference(12,104,0.3);

    $myrrix->setPreference(13,101,0.3);
    $myrrix->setPreference(13,105,0.3);
    $myrrix->setPreference(13,106,0.3);

    $myrrix->setPreference(14,105,0.3);

    $myrrix->setPreference(15,106,0.3);
    $myrrix->setPreference(15,107,0.3);
    $myrrix->setPreference(15,108,0.3);

    /*array_push($preferences, array('11','101','0.3'));
    array_push($preferences, array('11','102','0.3'));

    array_push($preferences, array('12','101','0.3'));
    array_push($preferences, array('12','103','0.3'));
    array_push($preferences, array('12','104','0.3'));

    array_push($preferences, array('13','101','0.3'));
    array_push($preferences, array('13','105','0.3'));
    array_push($preferences, array('13','106','0.3'));

    array_push($preferences, array('14','105','0.3'));

    $myrrix->setPreferences($preferences);*/

    $myrrix->refresh();
?>