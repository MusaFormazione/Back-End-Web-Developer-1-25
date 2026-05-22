<?php

require_once '../base.php';

$db = DB::connect();

$query = <<<SQL
SELECT 
    film.film_id, 
    film.title, 
    film.release_year, 
	film.rating, 
    film.rental_rate,
    language.name AS language_name, 
    category.name AS category_name
FROM film
INNER JOIN language ON film.language_id = language.language_id
INNER JOIN film_category ON film.film_id = film_category.film_id
INNER JOIN category ON film_category.category_id = category.category_id
ORDER BY film.film_id DESC
SQL;

$stmt = $db->prepare($query);

$stmt->execute();

$films = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

	<?=Helper::head('Film')?>

	<div class="header text-center mb-5">
		<h1><?=Helper::formatTitle('Film')?></h1>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">Gestione Film</h5>
					<p class="card-text">...</p>
                    <a href="create.php" class="btn btn-primary mb-3">Aggiungi Film</a>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle" id="filmTable">
                            <thead>
                            	<tr>
                                    <th>#</th>
                                    <th>Titolo</th>
                                    <th>Anno</th>
                                    <th>Lingua</th>
                                    <th>Categoria</th>
                                    <th>Rating</th>
                                    <th>Tariffa</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            	<?php foreach ($films as $film): ?>
                            	<tr>
                                    <td><?= $film['film_id'] ?></td>
                                    <td><?= $film['title'] ?></td>
                                    <td><?= $film['release_year'] ?></td>
                                    <td><?= $film['language_name'] ?></td>
                                    <td><?= $film['category_name'] ?></td>
                                    <td><?= $film['rating'] ?></td>
                                    <td><?= $film['rental_rate'] ?>€</td>
                                    <td>
                                        <form action="edit.php" class="d-inline" method="get">
                                            <input type="hidden" name="id" value="<?= $film['film_id'] ?>">
                                            <button type="submit" class="btn btn-success">Edit</button>
                                        </form>
                                        <form action="delete.php" class="d-inline" onsubmit="return confirm('Eliminare il film?')" method="post">
                                            <input type="hidden" name="id" value="<?= $film['film_id'] ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            	<?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
				</div>
			</div>
		</div>
	</div>

	<?=Helper::footer()?>
