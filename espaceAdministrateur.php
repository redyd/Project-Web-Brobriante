<?php

require __DIR__ . '/php/database/SellerRepository.php';
require __DIR__ . '/inc/session.inc.php';

use database\SellerRepository;

/* Variables du code HTML */
$title = "Espace administrateur";
$connected = true;

/* Variables des objets */
$message = "";
$user = $_SESSION["user"] ?? null;

if ($user == null || !$user->est_administrateur) {
    header("Location: index.php");
}

$repoSeller = new SellerRepository();
$zones = $repoSeller->getEverySellers($message);

?>

<!DOCTYPE html>
<html lang="fr">

<?php require "inc/head.inc.php"; ?>

<body>

<?php require "inc/header.inc.php"; ?>

<main>
    <h1 class="large-txt"><span class="underline">Espace administrateur</span></h1>

    <?php if (is_array($zones) && empty($message) && !empty($zones)): ?>
        <?php foreach ($zones as $zone => $data): ?>
            <section class="space">
                <h2 class="medium-txt spacer"><?= empty($zone) ? "Emplacement non-attribué" : $zone ?></h2>
                <div class="card-list space flex-stretch flex-wrap">
                    <?php
                    foreach ($data as $brocanteur) {
                        $brocanteur->createAdminCard();
                    }
                    ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="basic-txt space">Il n'y a aucun brocanteur pour l'instant...</p>
    <?php endif; ?>

</main>

<?php require "inc/footer.inc.php"; ?>

</body>

</html>