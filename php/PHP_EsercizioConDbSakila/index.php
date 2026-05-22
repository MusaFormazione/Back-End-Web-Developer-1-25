<?php require_once 'base.php'; ?>

    <?=Helper::head('Home')?>

	<div class="header text-center mb-5">
        <h1><?=Helper::formatTitle()?></h1>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gestione Film</h5>
                    <p class="card-text">...</p>
                    <a href="films/" class="btn btn-primary">Vai</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Abbinamento Attori</h5>
                    <p class="card-text">...</p>
                    <a href="actors/" class="btn btn-primary">Vai</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Clienti & Indirizzi</h5>
                    <p class="card-text">...</p>
                    <a href="customers/" class="btn btn-primary">Vai</a>
                </div>
            </div>
        </div>
    </div>

    <?=Helper::footer()?>