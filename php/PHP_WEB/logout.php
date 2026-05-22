<?php

include 'include.php';

session_destroy();

setcookie("username", "", time() - 300);

