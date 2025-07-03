<?php

use database\ObjetRepository;

require __DIR__ . "/php/database/ObjetRepository.php";
require __DIR__ . "/php/functions.php";
require __DIR__ . '/inc/session.inc.php';

/* Variable du code HTML */
$title = "Détail de l'objet";
$text = "A propos de cet objet";

/* Variable PHP */
$user = $_SESSION['user'] ?? null;
$message = "";
$success = false;
$repoObj = new ObjetRepository();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$action = $action == "d" ? $action : false;

if ($id) {
    $obj = $repoObj->getObjectByID($message, $id);
    if (!$obj) {
        $message = "Le objet n'existe pas ou l'URL est invalide";
    }
    // On vérifie s'il y a une requête
} else if (isPOST()) {
    $postID = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $obj = $repoObj->getObjectByID($message, $postID);
    if ($obj->id_brocanteur == $user->bid) {
        switch ($action) {
            case "d":
                if ($postID && $repoObj->deleteObjectByID($message, $postID)) {
                    if (!empty($obj->image) && file_exists($obj->getImage())) {
                        unlink($obj->getImage());
                    }
                    $success = "L'objet a bien été supprimé !";
                } else {
                    $message = "Erreur lors de la suppression de l'objet : " . $message;
                }
                break;
            default:
                $message = "Erreur dans l'URL";
                break;
        }
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

    <?php if ($action == "d" && empty($message) && !$success && isset($obj) && $obj->id_brocanteur == $user->bid): ?>
        <section class="fail-box center space">
            <p>Êtes-vous sûr de vouloir supprimer cet objet ?</p>
            <form action="<?= PHP_SELF() . "?action=d"; ?>" method="post">
                <input type="hidden" name="id" value="<?= $id; ?>"/>
                <a href="<?= PHP_SELF() . "?id=" . $obj->oid ?>"
                   class="grey-btn small-txt">Annuler</a>
                <button class="grey-btn small-txt" type="submit">Supprimer</button>
            </form>
        </section>
    <?php endif; ?>

    <?php if (empty($message) && !$success && isset($obj)): ?>
        <div class="detail center space border flex-center">
            <img src="<?= $obj->getImage(); ?>" alt="Photo du brocanteur" class="detail-img">
            <section class="elements">
                <h2 class="medium-txt"><?= $obj->intitule ?></h2>
                <section class="title-quote">
                    <h3 class="small-txt">Catégorie: <span class="accent-txt"><?= $obj->nom_categorie ?></span></h3>
                    <blockquote class="basic-txt"><?= $obj->description ?></blockquote>
                </section>
                <?php
                $zone = $obj->nom_zone;
                $emplacement = $obj->nom_emplacement;
                $full_name = $obj->nom_brocanteur;
                $id = $obj->id_brocanteur;

                include "inc/info.inc.php";
                ?>
                <?php if (isset($obj) && $user && $obj->id_brocanteur == $user->bid): ?>
                    <a href="gererObjet.php?id=<?= $obj->oid ?>&action=e" class="blue-btn">Modifier l'objet</a>
                    <a href="<?= PHP_SELF() . "?id=" . $obj->oid . "&action=d" ?>"
                       class="blue-btn">Supprimer l'objet</a>
                <?php endif; ?>
            </section>
        </div>

    <?php elseif ($success): ?>
        <p class="success-box center space"><?= $success ?></p>
    <?php else: ?>
        <p class="fail-box center space flex-center"><?= $message ?></p>
    <?php endif; ?>

</main>

<?php require "inc/footer.inc.php" ?>

</body>

</html>