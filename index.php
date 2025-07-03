<?php

use database\ObjetRepository;

require __DIR__ . '/php/database/ObjetRepository.php';
require __DIR__ . '/inc/session.inc.php';

/* Variables du code HTML */
$title = "Accueil";

/* Variables des objets */
$message = "";
$objets = new ObjetRepository();

$preview = $objets->getRandom($message, 3);

?>

<!DOCTYPE html>
<html lang="fr">

<?php require "inc/head.inc.php"; ?>

<body>

<?php require "inc/header.inc.php"; ?>

<main>
    <section class="center space">
        <h1 class="large-txt">La meilleure brocante d'outils de bricolage et de jardinage de votre région</h1>
        <section class="title-quote">
            <h2 class="medium-txt">Notre concept</h2>
            <blockquote class="basic-txt">Ici, nous donnons une seconde vie aux outils et accessoires que vous
                utilisez
                pour
                entretenir vos espaces extérieurs ou réaliser vos projets de bricolage. Que vous soyez un jardinier
                amateur
                ou un bricoleur passionné, vous trouverez une large sélection d’outils de qualité, soigneusement
                triés
                et
                accessibles à tous.
            </blockquote>
        </section>
    </section>

    <div class="clear-color strip-colored space">
        <section>
            <h3 class="medium-txt">Quand ?</h3>
            <p>Tous les jours de la semaine, de 8 à 17 heures</p>
        </section>
        <section>
            <h3 class="medium-txt">Où ?</h3>
            <p>Rue de la Bonnevente 4020, Liège - Belgique</p>
        </section>
        <section>
            <h3 class="medium-txt">Combien ?</h3>
            <p>Le prix d'inscription est de 10€</p>
        </section>
    </div>

    <section class="center space">
        <h2 class="medium-txt">Aperçu de nos objets</h2>
        <?php
        echo "<div class=\"card-list space flex-stretch flex-wrap\">\n";

        if ($preview !== false && sizeof($preview) > 0) {
            foreach ($preview as $obj) {
                $obj->createCard();
            }
        } else {
            echo "<p>Aucun objet disponible pour l'instant</p>";
        }

        echo "</div>\n";
        ?>
    </section>

</main>

<?php require "inc/footer.inc.php"; ?>

</body>

</html>