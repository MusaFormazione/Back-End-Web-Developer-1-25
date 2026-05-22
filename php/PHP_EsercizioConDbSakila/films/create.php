<?php

require_once '../base.php';

$db = DB::connect();

if($_SERVER['REQUEST_METHOD'] == "POST"){

	try {
		$db->beginTransaction();

		$query = <<<SQL
			INSERT INTO film (title, slug, release_year, description, language_id, rating, rental_rate, rental_duration, length, replacement_cost, special_features) 
			VALUES (:title, :slug, :year, :description, :language_id, :rating, :rental_rate, :rental_duration, :length, :replacement_cost, :special_features)
		SQL;

		$db->prepare($query)->execute([
			':title' => $_POST['title'],
			':slug' => urlencode($_POST['title']),
			':year' => $_POST['year'],
			':description' => $_POST['description'],
			':language_id' => $_POST['language'],
			':rating' => $_POST['rating'],
			':rental_rate' => $_POST['rental_rate'],
			':rental_duration' => $_POST['rental_duration'],
			':length' => $_POST['length'],
			':replacement_cost' => $_POST['replacement_cost'],
			':special_features' => $_POST['special_features']
		]);

		$film_id = $db->lastInsertId();

		$query = <<<SQL
			INSERT INTO film_category (film_id, category_id) 
			VALUES (:film_id, :category_id)
		SQL;

		$db->prepare($query)->execute([
			':film_id' => $film_id,
			':category_id' => $_POST['category']
		]);

		$db->commit();

	} catch (PDOException $e){
		$db->rollBack();
		header("Location: create.php?error=" . urlencode($e->getMessage()));
		exit;
	}

	header("Location: index.php?success=Film created");

	exit;
}

// Lingue
$query = "SELECT language_id, name FROM language";
$stmt = $db->prepare($query);
$stmt->execute();
$languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Categorie
$query = "SELECT category_id, name FROM category";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?=Helper::head('Aggiungi Film')?>

<div class="header text-center mb-5">
	<h1><?=Helper::formatTitle('Aggiungi Film')?></h1>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div>
					<a href="index.php" class="btn btn-secondary">Lista Film</a>
				</div>
				<form method="post">

						<div class="row">
							<div class="col-md-8">
								<label for="title">Titolo</label>
								<input type="text" name="title" id="title" class="form-control" maxlength="100" required>
							</div>
							<div class="col-md-4">
								<label for="year">Anno di Uscita</label>
								<input type="number" name="year" id="year" class="form-control" max="2030" min="1900" required>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<label for="description">Descrizione</label>
								<textarea name="description" id="description" class="form-control" rows="5" required></textarea>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<label for="language">Lingua</label>
								<select name="language" id="language" class="form-control" required>
									<option value="">---</option>
									<?php foreach($languages as $language): ?>
										<option value="<?= $language['language_id'] ?>"><?= $language['name'] ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-4">
								<label for="category">Category</label>
								<select name="category" id="category" class="form-select" required>
									<option value="">---</option>
									<?php foreach($categories as $category): ?>
										<option value="<?= $category['category_id'] ?>"><?= $category['name'] ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-4">
								<label for="rating">Voto</label>
								<select name="rating" id="rating" class="form-select">
								<option value="">---</option>
								<?php foreach(['G', 'PG', 'PG-13', 'R', 'NC-17'] as $i): ?>
										<option value="<?= $i ?>"><?= $i ?></option>
								<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<label for="rental-rate">Tariffa Noleggio</label>
								<input type="number" name="rental_rate" id="rental-rate" min="0.01" max="10" class="form-control" required>
							</div>
							<div class="col-md-4">
								<label for="rental-duration">Durata Noleggio</label>
								<input type="number" name="rental_duration" id="rental_duration" max="30" class="form-control" required>
							</div>
							<div class="col-md-4">
								<label for="length">Durata (min)</label>
								<input type="number" name="length" id="length" min="20" class="form-control" required>
							</div>
						</div>
						<div class="row mb-5">
							<div class="col-md-4">
								<label for="special_features">Special Features</label>
								<select name="special_features" id="special_features" class="form-select">
									<option value="">---</option>
									<?php foreach(['Trailers', 'Commentaries', 'Deleted Scenes', 'Behind the Scenes'] as $i): ?>
										<option value="<?= $i ?>"><?= $i ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-4">
								<label for="replacement_cost">Costo Sostituzione</label>
								<input type="number" name="replacement_cost" id="replacement_cost" min="0.01" class="form-control" required>
							</div>
						</div>
						<input class="btn btn-primary" type="submit" value="Salva">
					</form>
			</div>
		</div>
	</div>
</div>

<?=Helper::footer()?>