<?php

//ini_set('session.save_path', 'C:\tempsession');

include 'include.php';

$_SESSION['hits'] = ((isset($_SESSION['hits'])) ? $_SESSION['hits'] : 0) + 1;

echo "Count: " .  $_SESSION['hits'];