<?php

function getMailForResetPassword($password) {
    return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #e8e9ea; text-align: center;">

    <table role="presentation" style="width: 100%; max-width: 600px; margin: 20px auto; background-color: white; border-radius: 10px; box-shadow: 0px 2px 5px rgba(0,0,0,0.2); padding: 20px;">
        <tr>
            <td>
                <h1 style="color: #556B2F; font-size: 24px; font-weight: 700;">Réinitialisation de votre mot de passe</h1>
                <p style="font-size: 16px; color: #000;">Voici votre nouveau mot de passe :</p>
                <p style="background-color: #B4C424; color: white; display: inline-block; padding: 10px 20px; border-radius: 5px; font-size: 18px; font-weight: bold;">$password</p>
                <p style="font-size: 14px; margin-top: 20px;">Vous pourrez modifier votre mot de passe ici :</p>
                <a href="https://panoramix.cg.helmo.be/~q240078/EVAL_V4/modifierProfil.php" 
                   style="display: inline-block; background-color: #556B2F; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-size: 16px;">
                   Modifier mon mot de passe
                </a>
            </td>
        </tr>
    </table>

    <table role="presentation" style="width: 100%; background-color: #B4C424; padding: 20px; margin-top: 20px;">
        <tr>
            <td style="text-align: center;">
                <p style="font-size: 14px; color: #000; margin: 0;">Une question ? Un problème ? N'hésitez pas à nous contacter via notre formulaire de 
                    <a href="https://panoramix.cg.helmo.be/~q240078/EVAL_V4/contact.php" style="color: #000; text-decoration: underline;">contact</a>.
                </p>
            </td>
        </tr>
    </table>

</body>
</html>
HTML;
}