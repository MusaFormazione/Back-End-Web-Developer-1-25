<?php

// DESERIALIZZAZIONE

include "definizione.php";

//$serializedUser = 'O:4:"User":2:{s:4:"nome";s:5:"Mario";s:5:"email";s:14:"mario@email.it";}';
$serializedUser = 'O:4:"User":3:{s:4:"nome";s:5:"Mario";s:5:"email";s:14:"mario@email.it";s:12:"Usernumber";i:69502;}';
$serializedUser = 'O:4:"User":2:{s:4:"nome";s:5:"Mario";s:5:"email";s:14:"mario@email.it";}';
$serializedUser = 'O:4:"User":3:{s:4:"nome";s:5:"Mario";s:5:"email";s:14:"mario@email.it";s:12:"Usernumber";i:86446;}';
$serializedUser = 'O:4:"User":3:{s:6:"number";i:79372;s:4:"nome";s:5:"Mario";s:5:"email";s:14:"mario@email.it";}';

$user = unserialize($serializedUser);

var_dump($user);