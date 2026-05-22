<?php

$user = [
	"nome" => "Mario",
	"email" => "mario@test.it"
];

$userSerialized = serialize($user);

print($userSerialized);
