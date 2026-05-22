<?php

function connectDb(): mysqli | false{

	$file = 'settings.ini';

	try{

		if (!file_exists($file) || !$settings = parse_ini_file($file, TRUE))
			throw new exception('Unable to open ' . $file . '.');

		$host = $settings['database']['host'];
		$port = $settings['database']['port'];
		$dbName = $settings['database']['schema'];
		$user = $settings['database']['username'];
		$password = $settings['database']['password'];

		return new mysqli(
			$host,
			$user,
			$password,
			$dbName,
			$port
		);

	} catch (Exception $e){
		echo "connessione fallita: " . $e->getMessage();

		return false;
	}
}


function read(){
	$mysqli = connectDb();

	$stmt = $mysqli->prepare("SELECT first_name, last_name FROM actor WHERE first_name LIKE ?");

	$p = '%penelop%';
//$stmt->bind_param('s', $p);
//$stmt->execute();

	$stmt->execute([
		$p
	]);

	$result = $stmt->get_result();

	while($row = $result->fetch_assoc()){
		echo $row["first_name"] . " | " . $row["last_name"];
		echo "<br>";
	}

	while($row = $result->fetch_object(Actor::class)){
		echo $row->first_name . " | " . $row->last_name;
		echo "<br>";
	}

	class Actor{
		public string $first_name;
		public string $last_name;
	}
}

// Transazioni
$mysqli = connectDb();

// 1. AUTO COMMIT
$mysqli->autocommit(false);

try{

	$mysqli->begin_transaction();

	$stmt_rental = $mysqli->prepare("INSERT INTO rental (customer_id, inventory_id, rental_date, staff_id)
                                 VALUES (?, ?, NOW(), ?)");
	$stmt_rental->execute([3, 1, 1]);

	// 2. LAST INSERT ID... come prop.
	$rental_id = $mysqli->insert_id;

	validatePaymentDetails();

	echo "rental executed <br>";

	$stmt_payment = $mysqli->prepare("INSERT INTO payment (customer_id,  staff_id, rental_id, payment_date, amount )
									VALUES (?, ?, ?, NOW(), ?)");

	$stmt_payment->execute([3, 1, $rental_id, 4.99]);

	$mysqli->commit();

} catch(Exception $e){

	$conn->rollback();

} finally {
	$conn->close();
}
