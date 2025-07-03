<?php

use database\SellerRepository;
use forms\EmailForm;
use input\BasicInput;
use mail\MailSender;

require __DIR__ . '/php/forms/EmailForm.php';
require __DIR__ . '/php/input/BasicInput.php';
require __DIR__ . '/php/functions.php';
require __DIR__ . '/php/database/SellerRepository.php';
require __DIR__ . '/php/mail/MailSender.php';
require __DIR__ . '/php/mail/templates.php';
require __DIR__ . '/inc/session.inc.php';

if (isset($_SESSION['user'])) {
    header('Location: index.php');
}

/* Variables du code HTML */
$title = "Mot de passe";
$connected = false;
$text = "Mot de passe oublié";

$message = "";
$success = false;
$repoSeller = new SellerRepository();
$form = new EmailForm($_POST);

if (isPOST()) {
    $form->validate();
    if ($form->isValid()) {
        $email = $form->getData("email");
        if ($repoSeller->emailInBDD($message, $email)) {
            $password = createPassword();
            MailSender::sendMail(
                message: $message,
                subject: "Réinitialisation du mot de passe",
                body: getMailForResetPassword($password)
            );
            $repoSeller->updatePassword($message, $email, hash('sha256', $password));
            $success = "Un email vous a été envoyé !";
        } else if (empty($message)) {
            $form->addError("email", "Email invalide");
        } else {
            $message = "Une erreur est survenue : " . $message;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php require "inc/head.inc.php" ?>

<body>

<?php require "inc/header.inc.php"; ?>

<main>

    <?php require "inc/backTitle.inc.php"; ?>

    <?php if ($success): ?>
        <p class="flex-center center success-box space"><?= $success ?></p>
    <?php endif; ?>

    <form action="<?= PHP_SELF(); ?>" method="post" class="space small-detail full-space-form border">
        <?php
        new BasicInput(
            id: 'email',
            name: 'Email',
            form: $form ?? null,
            placeholder: 'Entrez votre email...',
            required: true);
        ?>
        <p class="small-txt"><em>Un email de réinitialisation de mot de passe va vous être envoyé</em></p>
        <fieldset class="full-space-btn">
            <button type="reset" class="grey-btn" onclick="window.history.back()">Annuler</button>
            <button type="submit" class="blue-btn">Réinitialiser</button>
        </fieldset>
    </form>
</main>

<?php require "inc/footer.inc.php" ?>

</body>

</html>