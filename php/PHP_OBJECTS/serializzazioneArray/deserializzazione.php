<?php

// DESERIALIZZAZIONE

$serializedUser = 'a:2:{s:4:"nome";s:5:"Mario";s:5:"email";s:13:"mario@test.it";}';

$user = unserialize($serializedUser);

var_dump($user);