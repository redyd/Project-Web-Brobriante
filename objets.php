<?php

use database\ObjetRepository;
use database\SellerRepository;
use input\OptionInput;

require __DIR__ . "/php/database/ObjetRepository.php";
require __DIR__ . "/php/input/OptionInput.php";
require __DIR__ . "/php/input/BasicInput.php";
require __DIR__ . "/php/database/SellerRepository.php";
require __DIR__ . '/php/functions.php';
require __DIR__ . '/inc/session.inc.php';
require_once __DIR__ . '/php/const.php';

/* Variable du code HTML */
$title = "Objets";

/* Variable PHP */
$message = "";

$repoObj = new ObjetRepository();
$categorie = new SellerRepository();
$options[0] = "Filtrer";
$options += $categorie->getAllSellerNames($message);

/* Filtrage */
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING) ?? $_COOKIE['search'] ?? false;
$id_brocanteur = filter_input(INPUT_GET, 'option', FILTER_SANITIZE_NUMBER_INT) ?? $_COOKIE['id_brocanteur'] ?? false;
if (isset($_GET['clear'])) {
    setcookie('search', '', time() - 3600, USER_ROOT);
    setcookie('id_brocanteur', '', time() - 3600, USER_ROOT);
    $search = false;
    $id_brocanteur = false;
}

$isGet = isGET() && ($search || $id_brocanteur);

if ($isGet) {
    setcookie('search', $search, time() + (86400 * COOKIE_TIME), USER_ROOT);
    setcookie('id_brocanteur', $id_brocanteur, time() + (86400 * COOKIE_TIME), USER_ROOT);
    $obj = [];
    $results = $repoObj->getFilteredObjets($message, $search, $id_brocanteur ?? 0);
    if (!empty($results)) {
        $obj["Résultats de la recherche"] = $results;
    }
} else {
    $obj = $repoObj->getObjetByZone($message);
}


?>

<!DOCTYPE html>
<html lang="fr">

<?php require "inc/head.inc.php" ?>

<body>

<?php require "inc/header.inc.php" ?>

<main>
    <h1 class="large-txt"><span class="underline">Rechercher des objets</span></h1>
    <form class="search center flex-center" method="get" action="<?= PHP_SELF(); ?>">
        <div class="search-bar">
            <input type="text" placeholder="Rechercher ici..." id="search" name="search" value="<?= $search ?? "" ?>">
            <?php
            $inputOption = new OptionInput(
                id: "option",
                name: "",
                options: $options,
                form: null
            );
            $inputOption->create($id_brocanteur ?? 0);
            ?>
            <nav class="flex-center">
                <button type="submit" class="btn-img" title="Rechercher">
                    <img src="images/icon/search.svg" alt="Rechercher" class="icon">
                </button>
                <a href="<?= PHP_SELF() . "?clear" ?>" title="Annuler">
                    <img src="images/icon/cancel.svg" alt="Annuler" class="icon">
                </a>
            </nav>
        </div>
    </form>
    <?php if (!empty($obj)): ?>
        <?php foreach ($obj as $zone => $repoObj) : ?>
            <section class="space">
                <h2 class="medium-txt spacer"><?= $zone ?></h2>
                <div class="card-list space flex-stretch flex-wrap">
                    <?php
                    foreach ($repoObj as $objet) {
                        $objet->createCard();
                    }
                    ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php elseif ($isGet): ?>
        <p class="basic-txt">Cette recherche n'a donné aucun résultat</p>
    <?php else: ?>
        <p class="basic-txt">Il n'y a aucun objet pour l'instant...</p>
    <?php endif; ?>

</main>

<?php require "inc/footer.inc.php" ?>

</body>

</html>