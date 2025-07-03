<?php

use database\ObjetRepository;
use database\SellerRepository;

require __DIR__ . "/php/database/SellerRepository.php";
require __DIR__ . "/php/database/ObjetRepository.php";
require __DIR__ . "/php/functions.php";
require __DIR__ . '/inc/session.inc.php';

/* Variables du code HTML */
$user = $_SESSION['user'] ?? null;
$isAdmin = $user && $user->est_administrateur;
$name = "Vendeur";
$title = $name;
$text = "A propos de ce vendeur";

/* Variables PHP */
$message = "";
$success = false;
$repoSeller = new SellerRepository();
$repoObject = new ObjetRepository();

// Paramètres de l'URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$action = $action == "d" && $isAdmin ? $action : false;

if ($id) {
    $seller = $repoSeller->getSellerByID($message, $id);
    if (!$seller) {
        $message = "Le brocanteur n'existe pas ou l'URL est invalide";
    }
    // On vérifie s'il y a une requête
} else if (isPOST() && $isAdmin) {
    $postID = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $seller = $repoSeller->getSellerByID($message, $postID);

    if (isset($seller->emplacement)) {
        $message = "Impossible de supprimer un brocanteur qui possède un emplacement.";
    } else {
        switch ($action) {
            case "d":
                if ($postID && $repoSeller->deleteSellerByID($message, $postID)) {
                    if (!empty($seller->photo) && file_exists($seller->getPhoto())) {
                        unlink($seller->getPhoto());
                    }
                    $success = "Le brocanteur a bien été supprimé !";
                } else {
                    $message = "Erreur lors de la suppression du brocanteur : " . $message;
                }
                break;
            default:
                $message = "Erreur dans l'URL";
                break;
        }
    }
}

// Tentative d'accès au brocanteur et à ses objets
if (!$success && $id && empty($message)) {
    $obj = $repoObject->getSellerObject($message, $id);
    if (!isset($obj)) {
        $message = "Le brocanteur n'existe pas ou l'url est invalide";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<?php require "inc/head.inc.php"; ?>

<body>

<?php require "inc/header.inc.php"; ?>

<main>
    <?php require "inc/backTitle.inc.php"; ?>

    <?php if ($action == "d" && empty($message) && !$success && isset($seller) && !isset($seller->emplacement)): ?>
        <section class="fail-box center space">
            <p>Êtes-vous sûr de vouloir supprimer ce brocanteur ?</p>
            <form action="<?= PHP_SELF() . "?action=d"; ?>" method="post">
                <input type="hidden" name="id" value="<?= $id ?>">
                <a href="<?= PHP_SELF() . "?id=" . $seller->bid ?>"
                   class="grey-btn small-txt">Annuler</a>
                <button class="grey-btn small-txt" type="submit">Supprimer</button>
            </form>
        </section>
    <?php elseif (empty($message) && !$success && $action == "d" && isset($seller->emplacement)): ?>
        <p class="fail-box center space">Impossible de supprimer un brocanteur qui possède un emplacement.</p>
    <?php endif; ?>

    <?php if (empty($message) && isset($seller) && !$success): ?>
        <div class="detail center space border flex-center">
            <img src="<?= $seller->getPhoto() ?>" alt="Photo du brocanteur" class="detail-img">
            <section class="elements">
                <h2 class="medium-txt"><?= $seller->getFullName() ?></h2>
                <section class="title-quote">
                    <?php if (!empty($seller->description)): ?>
                        <h3 class="small-txt">Description</h3>
                        <blockquote class="basic-txt"><?= $seller->description ?></blockquote>
                    <?php endif; ?>
                </section>
                <?php
                $zone = $seller->nom_zone;
                $emplacement = $seller->nom_emplacement;
                $id = $seller->bid;
                if ($zone && $emplacement && $id) {
                    include "inc/info.inc.php";
                } else {
                    echo "<p class='accent-txt'>Emplacement non-attribué</p>";
                }
                ?>
                <nav class="mult-btn">
                    <?php if ($user && $user->bid == $id): ?>
                        <a href="modifierProfil.php?id=<?= $seller->bid ?>" class="blue-btn">Modifier mes
                            informations</a>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <a href="attribuerEmplacement.php?id=<?= $seller->bid ?>" class="blue-btn">Attribuer un
                            emplacement</a>
                        <?php if (!isset($seller->emplacement)): ?>
                            <a href="<?= PHP_SELF() . "?id=" . $seller->bid . "&action=d" ?>"
                               class="blue-btn">Supprimer ce brocanteur</a>
                        <?php else: ?>
                            <p class="noclick-btn" title="Impossible de supprimer un brocanteur avec un emplacement">
                                Supprimer ce brocanteur</p>
                        <?php endif; ?>

                    <?php endif; ?>
                </nav>
            </section>
        </div>
        <h2 class="large-txt">Les objets proposés</h2>

        <?php if (isset($obj) && count($obj) > 0): ?>
            <div class="card-list space flex-stretch flex-wrap">
                <?php
                foreach ($obj as $card) {
                    $card->createCard();
                }
                ?>
            </div>
        <?php else: ?>
            <p class="flex-center small-txt">Ce brocanteur n'a pas encore mis d'objet en ligne</p>
        <?php endif; ?>

    <?php elseif ($success): ?>
        <p class="success-box center space"><?= $success ?></p>
    <?php else: ?>
        <p class="fail-box center space flex-center"><?= $message ?></p>
    <?php endif; ?>
</main>

<?php require "inc/footer.inc.php"; ?>

</body>

</html>