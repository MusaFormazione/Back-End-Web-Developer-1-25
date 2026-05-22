<?php

// Semplicemente include/lega un altro file al file corrente
//include 'pdo_config.php';

require_once 'PDO.php';

// Consente di includere/legare un altro file al file corrente, [MA] posso devo necessariamente includere il file.
// require 'PDO.php';

// Consente di includere/legare un altro file al file corrente, [MA] posso devo necessariamente includere il file, [E] FA IN MODO CHE SIA INCLUSO una sola volta.
// require_once 'PDO.php';

$pdo = connectDatabase();

try{
	$pdo->beginTransaction();

	echo "starting transaction <br>";

	$stmt_rental = $pdo->prepare("INSERT INTO rental (customer_id, inventory_id, rental_date, staff_id)
                                 VALUES (:customer_id, :inventory, NOW(), :staff_id)");
	$stmt_rental->execute([
		':customer_id' => 3,
		':inventory' => 1,
		':staff_id' => 1
	]);

	$rental_id = $pdo->lastInsertId();

	validatePaymentDetails();

	echo "rental executed <br>";

	$stmt_payment = $pdo->prepare("INSERT INTO payment (customer_id,  staff_id, rental_id, payment_date, amount )
									VALUES (:customer_id, :staff_id, :rental_id, NOW(), :amount)");

	$stmt_payment->execute([
		':customer_id' => 3,
		':staff_id' => 1,
		':rental_id' => $rental_id,
		':amount' => 4.99
	]);

	echo "payment executed <br>";

	$pdo->commit();

	echo "transaction_executed";

} catch (Exception $e){

	$pdo->rollback();

	echo "transaction_failed: " . $e->getMessage();
}


function validatePaymentDetails(){
	// IMPORTANTE OPERAZIONE
	//throw new Exception("No, credito non disponibile");
}