<?php

function connectDatabase(): PDO | bool{

	$file = 'settings.ini';

	try{

		if (!file_exists($file) || !$settings = parse_ini_file($file, TRUE))
			throw new exception('Unable to open ' . $file . '.');

		$host = $settings['database']['host'];
		$port = $settings['database']['port'];
		$dbName = $settings['database']['schema'];
		$user = $settings['database']['username'];
		$password = $settings['database']['password'];

		return new PDO("mysql:host=$host;port=$port;dbname=$dbName;", $user, $password);

	} catch(PdoException $pdo){

		var_dump($pdo);

		echo "Impossibile connettersi al Database, $host:$port, $dbName";
		echo "<br><br>";
		echo $pdo->errorInfo[2];
		echo "<br><br>";
		echo $pdo->getTraceAsString();

		return false;

	} catch (Exception $exception){

		echo $exception->getMessage();

		return false;
	}
}



function searchActorByName(string $name): array{

	$connection = connectDatabase();

	$query = "SELECT * FROM actor WHERE first_name LIKE :name";

	$options = [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY];

	if(!$statement = $connection->prepare($query, $options))
		throw new exception("Prepare statement fallisce");

	$statement->execute([
		":name" => "%$name%"
	]);

	$result = [];
	while( $row = $statement->fetch(PDO::FETCH_ASSOC) ){
		$result[] = $row;
	}

	return $result;
}

function filmFormActor(?string $first_name = null, ?string $last_name = null): array{

	if(is_null($first_name) && is_null($last_name))
		return [];

	$connection = connectDatabase();

	$query = "SELECT film.film_id, film.title, film.rating, category.name as category
			  FROM film
			  INNER JOIN film_actor ON film.film_id = film_actor.film_id
			  INNER JOIN film_category ON film.film_id = film_category.film_id
			  INNER JOIN category ON film_category.category_id = category.category_id
			  INNER JOIN actor ON actor.actor_id = film_actor.actor_id";

	$params = [];

	if (!is_null($first_name) && is_null($last_name)){
		$params[] = "%$first_name%";
		$query .= " WHERE actor.first_name LIKE ?";
	}
	else if (is_null($first_name) && !is_null($last_name)){
		$params[] = "%$last_name%";
		$query .= " WHERE actor.last_name LIKE ?";
	}
	else{
		$params = [
			"%$first_name%",
			"%$last_name%",
		];
		$query .= " WHERE actor.first_name LIKE :first_name AND actor.last_name LIKE :last_name";
	}

	$options = [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY];

	if(!$statement = $connection->prepare($query, $options))
		throw new exception("Prepare statement fallisce");

	$statement->execute($params);

	return $statement->fetchAll(PDO::FETCH_ASSOC);
}