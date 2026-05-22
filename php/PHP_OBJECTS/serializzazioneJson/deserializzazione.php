<?php

// DESERIALIZZAZIONE

include "definizione.php";

//$serializedUser = 'O:4:"User":2:{s:4:"nome";s:5:"Mario";s:5:"email";s:14:"mario@email.it";}';
$serializedUser = '{"nome":"Mario","email":"mario@email.it"}';

$user = json_decode($serializedUser, true);

var_dump($user);