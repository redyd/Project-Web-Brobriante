<?php

use database\EmplacementRepository;
use database\SellerRepository;
use forms\SignInForm;
use input\BasicInput;
use input\CheckboxInput;
use input\FileInput;
use input\TextareaInput;

require __DIR__ . '/php/forms/LoginForm.php';
require __DIR__ . '/php/forms/SignInForm.php';
require __DIR__ . '/php/input/Input.php';
require __DIR__ . '/php/input/FileInput.php';
require __DIR__ . '/php/input/CheckboxInput.php';
require __DIR__ . '/php/input/BasicInput.php';
require __DIR__ . '/php/input/TextareaInput.php';
require __DIR__ . '/php/database/SellerRepository.php';
require __DIR__ . '/php/database/EmplacementRepository.php';
require __DIR__ . '/php/functions.php';
require __DIR__ . '/inc/session.inc.php';

checkHoneyPot($_POST);

/* Variable du code HTML */
$title = "S'inscrire";

/* Variable des objets */
$message = "";
$success = false;
$repoEmplacement = new EmplacementRepository();
$form = new SignInForm($_POST, $_FILES);
$isAvailable = !empty($repoEmplacement->getAvailableEmplacement($message));

// Vérifier si le formulaire est correct et insérer un nouveau brocanteur
if (isPOST() && $isAvailable) {

    $repoSeller = new SellerRepository();
    $form->validate();
    // Validation du champ email ici, car plus simple
    $form->validateField('email', fn($email) => !$repoSeller->emailInBDD($message, $email), "Email déjà utilisé");

    if ($form->isValid()) {
        $isUpl = $form->uploadFile();
        $data = $form->getAllData();
        $data['mot_passe'] = hash('sha256', $data['mot_passe']);
        $imgToUpl = $form->getFileDestination();
        $data['photo'] = $imgToUpl;
        unset($data['info']);
        $currentID = $repoSeller->insertSeller($message, $data);
        if ($currentID) {
            $success = "Votre profil a été créé avec succès !<br>
                        Foire aux puces — réservation n°$currentID<br>
                        Prix de l'inscription : 10€<br>
                        Numéro de compte : 12345678901";
            $user = $repoSeller->connexion($message, $form->getData("email"), $form->getData("mot_passe"));
            $_SESSION['user'] = $user;
        } else {
            $message = "Une erreur est survenue : " . $message;
            if ($isUpl) {
                unlink("uploads/" . $imgToUpl);
            }
        }
    }
} else if (!$isAvailable) {
    $message = "Il n'est actuellement plus possible de s'inscrire !";
}
?>

<!DOCTYPE html>
<html lang="fr">

<?php require "inc/head.inc.php"; ?>

<body>

<?php require "inc/header.inc.php"; ?>

<main>
    <h1 class="large-txt">S'inscrire</h1>
    <?php if (empty($message) && !$success): ?>
        <form action="<?= PHP_SELF(); ?>" method="post" class="space small-detail full-space-form border" enctype="multipart/form-data">
            <input type="text" name="info" id="info" value="" class="info-field" aria-hidden="true">
        <?php
        // Email
        new BasicInput(
            id: "email",
            name: "Adresse mail",
            form: $form ?? null,
            placeholder: "Entrez votre adresse mail...",
            required: true);

        // Mot de passe
        new BasicInput(
            id: "mot_passe",
            name: "Mot de passe",
            form: $form ?? null,
            type: "password",
            placeholder: "Entrez votre mot de passe...",
            required: true);

        // Répétition du mot de passe
        new BasicInput(
            id: "mot_passe_confirm",
            name: "Répétition du mot de passe",
            form: $form ?? null,
            type: "password",
            placeholder: "Entrez à nouveau votre mot de passe...",
            required: true);

        // Nom
        new BasicInput(
            id: "nom",
            name: "Nom",
            form: $form ?? null,
            placeholder: "Entrez votre nom...",
            required: true);

        // Prénom
        new BasicInput(
            id: "prenom",
            name: "Prénom",
            form: $form ?? null,
            placeholder: "Entrez votre prénom...",
            required: true);

        // Description
        new TextareaInput(
            id: "description",
            name: "Description",
            form: $form ?? null,
            placeholder: "Entrez votre description...");

        // Photo de profil
        new FileInput(
            id: "photo",
            name: "Photo de profil",
            form: $form ?? null,
            accept: "image/*",
            type: "file");

        // Visible en ligne
        new CheckboxInput(
            id: "visible",
            name: "Visible en ligne",
            form: $form ?? null);
        ?>
        <fieldset class="full-space-btn">
            <button type="reset" class="grey-btn">Annuler</button>
            <button type="submit" class="blue-btn">S'inscrire</button>
        </fieldset>
        <a href="seConnecter.php" class="link small-txt flex-center">J'ai déjà un compte !</a>
    <?php elseif ($success): ?>
        <p class='success-box flex-center space center'><?= $success ?></p>
        </form>
    <?php else: ?>
        <p class='fail-box flex-center space center'><?= $message ?></p>
    <?php endif; ?>
</main>

<?php require "inc/footer.inc.php"; ?>

</body>

</html>