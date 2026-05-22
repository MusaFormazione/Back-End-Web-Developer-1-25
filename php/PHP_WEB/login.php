<?php


setcookie("username", base64_encode("Mario"), time() + 300);
echo "Logged in";

