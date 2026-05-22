<?php


if(isset($_COOKIE['username']))
	echo "Benvenuto " . base64_decode( $_COOKIE['username'] );
else
	echo "Cookie Non Trovato";