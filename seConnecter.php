<?php

use database\SellerRepository;
use forms\LoginForm;
use input\BasicInput;

require __DIR__ . '/php/forms/LoginForm.php';
require __DIR__ . '/php/input/BasicInput.php';
require __DIR__ . '/php/functions.php';
require __DIR__ . '/php/database/SellerRepository.php';
require __DIR__ . '/inc/session.inc.php';

/* Variables du code HTML */
$text = "Se connecter";
$title = "Se connecter";
$message = "";
$success = false;

$form = new LoginForm($_POST);
$repoSeller = new SellerRepository();

if (isPOST()) {
    $form->validate();

    if ($form->isValid()) {
        $user = $repoSeller->connexion($message, $form->getData("email"), $form->getData("mot_passe"));
        if ($user) {
            $_SESSION['user'] = $user;
            $success = "Connection réussie ! Vous allez être redirigé";
            header('Refresh: 1; espaceBrocanteur.php');
        } else {
            $form->addError("email", "Email ou mot de passe incorrect");
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<?php require "inc/head.inc.php" ?>

<body>

<?php require "inc/header.inc.php" ?>

<main>

    <?php require "inc/backTitle.inc.php" ?>
    <?php if ($success && empty($message)): ?>
        <p class="success-box flex-center center space"><?= $success ?></p>
    <?php endif; ?>
    <form action="<?= PHP_SELF() ?>" method="post" class="space small-detail full-space-form border">
        <?php
        new BasicInput(
            id: "email",
            name: "Adresse mail",
            form: $form ?? null,
            placeholder: "Entrez votre adresse mail...",
            required: true);

        new BasicInput(
            id: "mot_passe",
            name: "Mot de passe",
            form: $form ?? null,
            type: "password",
            placeholder: "Entrez votre mot de passe...",
            required: true);
        ?>
        <fieldset class="full-space-btn">
            <button type="reset" class="grey-btn">Annuler</button>
            <button type="submit" class="blue-btn">Se connecter</button>
        </fieldset>
        <nav>
            <a href="motDePasse.php" class="link small-txt flex-center">Mot de passe oublié ?</a>
            <a href="inscription.php" class="link small-txt flex-center">Pas encore de compte ?</a>
        </nav>
    </form>
</main>

<?php require "inc/footer.inc.php" ?>

</body>

</html>