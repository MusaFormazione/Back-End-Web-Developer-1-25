<?php

include "definizione.php";

$user = new User("Mario", "mario@email.it");

$userSerialized = serialize($user);

print($userSerialized);
