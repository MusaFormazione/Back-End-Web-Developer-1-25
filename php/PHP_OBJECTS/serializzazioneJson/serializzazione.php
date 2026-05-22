<?php

include "definizione.php";

$user = new User("Mario", "mario@email.it");

$userSerialized = json_encode($user, JSON_PRETTY_PRINT);

print($userSerialized);
