<?php

use database\EmplacementRepository;
use database\SellerRepository;
use forms\EmplacementForm;
use input\OptionInput;

require __DIR__ . '/php/forms/EmplacementForm.php';
require __DIR__ . '/php/input/OptionInput.php';
require __DIR__ . '/php/database/EmplacementRepository.php';
require __DIR__ . '/php/database/SellerRepository.php';
require __DIR__ . '/php/functions.php';
require __DIR__ . '/inc/session.inc.php';

/* Variables du code HTML */
$title = "Attribuer un emplacement";
$connected = true;
$name = "Vendeur";
$text = "Attribuer un emplacement";

/* Variables PHP */
$user = $_SESSION['user'] ?? null;

if ($user == null || !$user->est_administrateur) {
    header("location: index.php");
}

$success = false;
$message = "";
$repoEmp = new EmplacementRepository();
$repoSeller = new SellerRepository();
$form = false;
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$id || $id < 0) {
    $message = "URL invalide";
}

// Récupération des informations
if (!empty($id)) {
    $seller = $repoSeller->getSellerByID($message, $id);
    if (!$seller) {
        $message = "Ce brocanteur n'existe pas";
    }
    $available = $repoEmp->getAvailableEmplacement($message, $seller->emplacement ?? 0);
    if (sizeof($available) == 0) {
        $message = "Il n'y a plus d'emplacement disponible.";
    } else {
        $emplacements = [null => "Aucun"] + $available;
        $form = new EmplacementForm($_POST, $emplacements);

        // Remplissage du formulaire
        if (isset($seller->emplacement) && !isPOST()) {
            $form->setData(['emplacement' => $seller->emplacement]);
        }
        $form->emplacements = array_keys($emplacements);
    }
}

// Dans le cas d'une soumission
if (isPOST()) {
    $form->validate();
    if ($form->isValid()) {
        $data = $form->getData('emplacement');
        if (empty($data)) {
            $success = $repoSeller->nullSellerValue($message, $id, 'emplacement');
        } else {
            $success = $repoSeller->updateEmplacement($message, $id, $data);
        }
        if ($success) {
            $success = "Modification enregistrée.";
        } else {
            $message = "Erreur lors de l'attribution : " . $message;
        }
    } else $message = "Cet emplacement n'existe pas.";
}

?>

<!DOCTYPE html>
<html lang="fr">

<?php require "inc/head.inc.php"; ?>

<body>

<?php require "inc/header.inc.php"; ?>

<main>
    <?php require "inc/backTitle.inc.php" ?>

    <?php if (empty($message) && isset($seller) && isset($emplacements) && !$success): ?>
        <div class="detail space center border flex-center">
            <img src="<?= $seller->getPhoto() ?>" alt="Photo du brocanteur" class="detail-img">
            <form action="<?= PHP_SELF() . "?id=$id"; ?>" method="post"
                  class="full-space-form">
                <section class="title-quote">
                    <h2 class="medium-txt"><?= $seller->getFullName() ?></h2>
                    <blockquote class="basic-txt"><?= $seller->description ?></blockquote>
                </section>
                <fieldset class="lab-row">
                    <?php
                    new OptionInput(
                        id: "emplacement",
                        name: "",
                        options: $emplacements,
                        form: $form ?? null);
                    ?>
                    <button type="submit" class="blue-btn">Attribuer</button>
                </fieldset>
            </form>
        </div>
    <?php elseif ($success): ?>
        <nav class="center space">
            <p class="success-box flex-center center"><?= $success ?></p>
            <a class="blue-btn" href="detailBrocanteur.php?id=<?= $id ?>">Revenir au brocanteur</a>
        </nav>
    <?php else: ?>
        <span class="fail-box center space flex-center"><?= $message ?></span>
    <?php endif; ?>
</main>

<?php require "inc/footer.inc.php"; ?>
</body>

</html>