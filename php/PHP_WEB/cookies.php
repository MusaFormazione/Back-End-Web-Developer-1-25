<?php
$pageAccesses = array_key_exists('accesses', $_COOKIE) ? $_COOKIE['accesses'] : 0;
setcookie('accesses', ++$pageAccesses);

var_dump($_COOKIE);