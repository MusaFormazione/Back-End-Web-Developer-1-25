<?php


class DB {
	public static function connect(): PDO {
		$host = "host.docker.internal";
		$user = 'root';
		$password = 'root';
		return new PDO("mysql:host=$host;dbname=sakila;port=3307;charset=utf8mb4", $user, $password);
	}
}

class Helper {
	public static function head(?string $title): string {
		$result =  <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
HTML;
		$headerTitle = self::formatTitle($title);
		$result .= "<title>$headerTitle  dd</title>";
		$result .=  <<<HTML
<style>
    .table td, .table th { vertical-align: middle; }
    .badge-rating { font-size: .8rem; }
  </style>
</head>
<body class="bg-light">
<div class="container my-4">
HTML;

		return $result;
	}
	public static function formatTitle(?string $title = null): string {
		$projectName = 'Sakila DB Manager ';
		return is_null($title) ? $projectName : "$projectName >> $title";
	}
	public static function footer(): string {
		return <<<HTML
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
	}
}