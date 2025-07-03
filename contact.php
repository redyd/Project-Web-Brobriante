<?php

use forms\ContactForm;
use input\BasicInput;
use input\TextareaInput;
use mail\MailSender;

require __DIR__ . '/php/data/Brocanteur.php';
require __DIR__ . '/php/forms/ContactForm.php';
require __DIR__ . '/php/input/BasicInput.php';
require __DIR__ . '/php/input/TextareaInput.php';
require __DIR__ . '/php/mail/MailSender.php';
require __DIR__ . '/php/functions.php';
require __DIR__ . '/inc/session.inc.php';

checkHoneyPot($_POST);

/* Variables du code HTML */
$title = "Contacter";

/* Variables PHP */
$user = $_SESSION["user"] ?? null;
$message = "";
$success = false;
$form = new ContactForm($_POST);

if ($user != null && !isPOST()) {
    $form->setData(["email" => $user->email]);
}

if (isPOST()) {
    $form->validate();
    if ($form->isValid()) {
        $data = $form->getAllData();
        if (MailSender::sendMail($message, $data['subject'], $data['message'], $data['email'])) {
            $success = "Message correctement envoyé !<br>Une copie de ce message vous a été envoyé.";
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
    <h1 class="large-txt">Nous contacter</h1>
    <?php if ($success): ?>
        <p class="success-box flex-center center space"><?= $success ?></p>
    <?php elseif (!empty($message)): ?>
        <p class="fail-box flex-center center space"><?= $message ?></p>
    <?php endif; ?>
    <form action="<?= PHP_SELF() ?>" method="post"
          class="detail center space full-space-form border flex-wrap">
        <input type="text" name="info" id="info" value="" class="info-field">
        <?php
        // Email
        new BasicInput(
            id: 'email',
            name: 'Adresse mail',
            form: $form ?? null,
            placeholder: 'Entrez votre email...',
            required: true);

        // Objet de contact
        new BasicInput(
            id: 'subject',
            name: 'Objet du contact',
            form: $form ?? null,
            placeholder: 'Entrez la raison du contact...',
            required: true);

        // Contenu du message
        new TextareaInput(
            id: 'message',
            name: '',
            form: $form ?? null,
            placeholder: 'Entrez votre message...',
            required: true,
            class: 'large-textarea');
        ?>
        <fieldset class="form-btn">
            <button type="reset" class="grey-btn" onclick="window.history.back()">Annuler</button>
            <button type="submit" class="blue-btn">Envoyer</button>
        </fieldset>
    </form>
</main>

<?php require "inc/footer.inc.php"; ?>

</body>

</html>