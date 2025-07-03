<?php

use database\ObjetRepository;
use database\SellerRepository;

require __DIR__ . "/php/database/SellerRepository.php";
require __DIR__ . "/php/database/ObjetRepository.php";
require __DIR__ . '/inc/session.inc.php';

/* Variables du code HTML */
$title = "Espace brocanteur";

/* Variables des objets */
$user = $_SESSION["user"] ?? null;

if ($user == null) {
    header("Location: index.php");
}

$message = "";
$repoSeller = new SellerRepository();
$repoObjet = new ObjetRepository();
$id = $user->bid;
$seller = $repoSeller->getSellerByID($message, $id);

if (!$seller) {
    $message = "Une erreur est survenue";
} else {
    $obj = $repoObjet->getSellerObject($message, $id);
}

?>

<!DOCTYPE html>
<html lang="en">

<?php require "inc/head.inc.php"; ?>

<body>

<?php require "inc/header.inc.php"; ?>

<main>
    <h1 class="large-txt"><span class="underline">Espace brocanteur</span></h1>
    <?php if (empty($message) && isset($obj)): ?>
        <p class="small-txt"><?= $seller->getEmplacement() ?></p>
        <section class="spacer-box clear-color space flex-wrap">
            <h2 class="medium-txt">Mes objets</h2>
            <a href="gererObjet.php?action=a" class="blue-btn">Ajouter un objet +</a>
        </section>
        <div class="card-list space flex-stretch flex-wrap">
            <?php

            foreach ($obj as $objet) {
                $objet->createCard();
            }
            ?>
        </div>
    <?php else: ?>
        <p class="fail-box flex-center basic-txt center space"><?= $message ?></p>
    <?php endif; ?>
</main>

<?php require "inc/footer.inc.php"; ?>

</body>

</html>