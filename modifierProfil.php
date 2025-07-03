<?php

use database\SellerRepository;
use forms\PasswordForm;
use forms\PersonForm;
use input\BasicInput;
use input\CheckboxInput;
use input\FileInput;
use input\TextareaInput;

require __DIR__ . '/php/forms/PersonForm.php';
require __DIR__ . '/php/forms/PasswordForm.php';
require __DIR__ . '/php/input/FileInput.php';
require __DIR__ . '/php/input/BasicInput.php';
require __DIR__ . '/php/input/TextareaInput.php';
require __DIR__ . '/php/database/SellerRepository.php';
require __DIR__ . '/php/input/CheckboxInput.php';
require __DIR__ . '/php/functions.php';
require __DIR__ . '/inc/session.inc.php';

/* Variables du code HTML */
$title = "Modifier mon profil";
$text = "Modifier mon profil";

/* Variables des objets */
$message = "";
$success = false;
$profil = new PersonForm($_POST, $_FILES);
$password = new PasswordForm($_POST);
$repoSeller = new SellerRepository();
$user = $_SESSION['user'] ?? null;

if ($user == null) {
    header("Location: index.php");
}

$id = $user->bid;
$seller = $repoSeller->getSellerById($message, $id);

if (!$seller) {
    $message = "Le brocanteur n'existe pas ou l'url est invalide";
}

if ($id !== false && $id > 0 && $seller) {
    $profilPlaceholders = $seller->getPlaceholders();

    if (isPOST()) {
        $formType = $_POST['form_type'] ?? false;

        switch ($formType) {
            // Gérer le formulaire du profil
            case 'profil':
                $profil->validate();
                $isUpl = $profil->uploadFile('photo');
                if ($profil->isValid()) {
                    $toUpl = $profil->getAllData();
                    // Gestion de la checkbox
                    $toUpl['visible'] = isset($toUpl['visible']) ? 1 : 0;

                    if ($isUpl) {
                        $imgToUpl = $profil->getFileDestination();
                        $toUpl['photo'] = $imgToUpl;
                        $_SESSION['user']->photo = $imgToUpl;
                    }

                    if ($repoSeller->updateSellerByID($message, $id, $toUpl)) {
                        if ($isUpl && !empty($seller->photo) && file_exists($seller->getPhoto())) {
                            unlink($seller->getPhoto());
                        }
                        $success = "Votre profil a été correctement modifié !";
                    } else {
                        $message = "Erreur lors de la modification du profil : " . $message;
                        if ($isUpl) {
                            unlink(FILE_UPLOAD . $imgToUpl);
                        }
                    }
                }
                break;
            // Gérer le formulaire du mot de passe
            case 'password':
                $password->validate();
                if ($password->isValid()) {
                    $toUpl = $password->getAllData();
                    $toUpl['mot_passe'] = hash('sha256', $toUpl['mot_passe']);
                    if ($repoSeller->updateSellerByID($message, $id, $toUpl)) {
                        $success = "Votre mot de passe a été correctement modifié !";
                    } else {
                        $message = "Erreur lors de la modification du mot de passe : " . $message;
                    }
                }
                break;
            default:
                $profil->setData($profilPlaceholders);
                break;
        }
    } else {
        $profil->setData($profilPlaceholders);
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

    <?php if (isset($id) && empty($message) && !$success && isset($seller)): ?>
        <div class="detail center space border flex-center">
            <img src="<?= $seller->getPhoto() ?>" alt="Photo du brocanteur" class="detail-img">
            <form action="<?= PHP_SELF() ?>" method="post" class="full-space-form"
                  enctype="multipart/form-data">
                <input type="hidden" name="form_type" value="profil">
                <?php
                // Email
                new BasicInput(
                    id: 'email',
                    name: 'Email',
                    form: $profil ?? null,
                    placeholder: 'Entrez votre mail...',
                    required: true
                );

                // Nom
                new BasicInput(
                    id: 'nom',
                    name: 'Nom',
                    form: $profil ?? null,
                    placeholder: 'Entrez votre nom...',
                    required: true);

                // Prénom
                new BasicInput(
                    id: 'prenom',
                    name: 'Prénom',
                    form: $profil ?? null,
                    placeholder: 'Entrez votre prénom...',
                    required: true);

                // Image
                new FileInput(
                    id: 'photo',
                    name: 'Image',
                    form: $profil ?? null,
                    class: 'lab-row');

                // Description
                new TextareaInput(
                    id: 'description',
                    name: 'Description',
                    form: $profil ?? null,
                    placeholder: 'Entrez votre description...');

                // Visible en ligne
                new CheckboxInput(
                    id: "visible",
                    name: "Visible en ligne",
                    form: $profil ?? null);

                $zone = $seller->nom_zone;
                $emplacement = $seller->nom_emplacement;
                include("inc/info.inc.php");

                ?>
                <fieldset class="form-btn">
                    <button type="reset" class="grey-btn" onclick="window.history.back()">Annuler</button>
                    <button type="submit" class="blue-btn">Modifier</button>
                </fieldset>
            </form>
        </div>

        <section class="detail center space border">
            <h1 class="large-txt underline">Changer mon mot de passe</h1>
            <form action="<?= PHP_SELF() ?>" method="post" class="full-space-form">
                <input type="hidden" name="form_type" value="password">
                <?php
                // Mot de passe
                new BasicInput(
                    id: 'mot_passe',
                    name: 'Mot de passe',
                    form: $password ?? null,
                    type: 'password',
                    placeholder: 'Mot de passe...',
                    required: true);

                // Répétition du mot de passe
                new BasicInput(
                    id: 'mot_passe_confirm',
                    name: 'Répétition de mot de passe',
                    form: $password ?? null,
                    type: 'password',
                    placeholder: 'Répétez votre mot de passe...',
                    required: true);
                ?>
                <div class="form-btn">
                    <button type="reset" class="grey-btn" onclick="window.history.back()">Annuler</button>
                    <button type="submit" class="blue-btn">Modifier</button>
                </div>
            </form>
        </section>
    <?php elseif (empty($message) && $success): ?>
        <nav class="center space">
            <p class="success-box flex-center center"><?= $success ?></p>
            <a class="blue-btn" href="modifierProfil.php">Revenir à mon profil</a>
        </nav>
    <?php else: ?>
        <p class="fail-box flex-center space center"><?= $message ?></p>
    <?php endif; ?>
</main>

<?php require "inc/footer.inc.php"; ?>

</body>

</html>