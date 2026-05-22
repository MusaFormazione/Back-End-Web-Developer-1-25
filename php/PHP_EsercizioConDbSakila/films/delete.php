<?php

error_reporting(0);

require_once '../base.php';

if(!$_SERVER['REQUEST_METHOD'] == "POST"){
	header("Location: index.php");
	exit;
}

$id = $_POST['id'];

// Aggiungere controllo che il film esista

if(!is_numeric($id)){
	header("Location: index.php?error=Invalid ID");
	exit;
}

$db = DB::connect();


try{
	$db->beginTransaction();

	$db->prepare("DELETE FROM film_actor WHERE film_id = :id")->execute([ ':id' => $id ]);
	$db->prepare("DELETE FROM film_category WHERE film_id = :id")->execute([ ':id' => $id ]);

	$inventory = $db->prepare("SELECT * FROM inventory WHERE film_id = :id");
	$inventory->execute([ ':id' => $id ]);
	while($row = $inventory->fetch(PDO::FETCH_ASSOC)){
		$db->prepare("DELETE FROM rental WHERE inventory_id = :id")->execute([ ':id' => $row['inventory_id'] ]);
	}

	$db->prepare("DELETE FROM inventory WHERE film_id = :id")->execute([ ':id' => $id ]);

	$db->prepare("DELETE FROM film WHERE film_id = :id")->execute([ ':id' => $id ]);

	$db->commit();

	header("Location: index.php?success=Film deleted");
	exit;

}catch(PDOException $e){
	$db->rollBack();
	header("Location: index.php?error=Error deleting film - " . urlencode($e->getMessage()));
	exit;
}