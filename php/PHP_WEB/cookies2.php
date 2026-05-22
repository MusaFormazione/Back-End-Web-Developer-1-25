<?php
$arr_cookie_options = array (
	'expires' => time() + 60*60*24*30,
	'path' => '/',
	//'domain' => '.example.com', // leading dot for compatibility or use subdomain
	'secure' => false,     // or false
	'httponly' => false,    // or false
	//'samesite' => 'None' // None || Lax  || Strict
);


$someData = [
	"UserSession" => "xx",
	"Prop" => "yy"
];

setcookie('TestCookie', json_encode($someData), $arr_cookie_options);

var_dump(json_decode($_COOKIE['TestCookie'], true));
