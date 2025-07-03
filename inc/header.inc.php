<?php
$isSeller = false;
$isAdmin = false;
if (!empty($_SESSION) && isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $isSeller = true;
    if ($user->est_administrateur) {
        $isAdmin = true;
    }
}
?>
<header class="flex-center">
    <nav>
        <ul class="h-list">
            <li>
                <a href="index.php" class="medium-txt">Brobriante</a>
            </li>
            <li>
                <img src="images/picture/logo.png" alt="Logo brobriante" class="icon">
            </li>
        </ul>
    </nav>
    <nav>
        <ul class="h-list info">
            <li class="<?php if ($title == "Objets") {
                echo "current";
            }; ?>">
                <span class="linked">
                    <a href="objets.php" class="basic-txt">
                        Objets
                    </a>
                </span>
            </li>
            <li class="<?php if ($title == "Brocanteurs") {
                echo "current";
            }; ?>">
                <span class="linked">
                    <a href="brocanteurs.php" class="basic-txt">
                        Brocanteurs
                    </a>
                </span>
            </li>
            <?php if ($isSeller): ?>
                <li class="<?php if ($title == "Espace brocanteur") {
                    echo "current";
                }; ?>">
                    <span class="linked">
                        <a href="espaceBrocanteur.php" class="basic-txt">
                            Espace brocanteur
                        </a>
                    </span>
                </li>
            <?php endif; ?>
            <?php if ($isAdmin): ?>
                <li class="<?php if ($title == "Espace administrateur") {
                    echo "current";
                }; ?>">
                    <span class="linked">
                        <a href="espaceAdministrateur.php" class="basic-txt">
                            Espace administrateur
                        </a>
                    </span>
                </li>
            <?php endif; ?>
        </ul>

    </nav>
    <nav>
        <?php if ($isSeller): ?>
            <ul class="h-list info">
                <li class="<?php if ($title == "Modifier mon profil") {
                    echo "current";
                }; ?>">
                    <img src="<?= $user->getPhoto() ?>" alt="Photo de profil" class="profil-picture">
                    <span class="linked">
                        <a href="modifierProfil.php" class="basic-txt"><?= $user->getFullName() ?></a>
                    </span>
                </li>
                <li>
                    <a href="deconnection.php" class="flex-center"><img src="images/icon/logout.svg"
                                                                        alt="Se déconnecter"></a>
                </li>
            </ul>
        <?php else: ?>
            <ul class="h-list info">
                <li class="<?php if ($title == "Se connecter") {
                    echo "current";
                }; ?>">
                    <span class="linked">
                        <a href="seConnecter.php" class="basic-txt">
                            Se connecter
                        </a>
                    </span>

                </li>
                <li class="<?php if ($title == "S'inscrire") {
                    echo "current";
                } ?>">
                <span class="linked">
                    <a href="inscription.php" class="basic-txt">
                            S'inscrire
                    </a>
                </span>
                </li>
            </ul>
        <?php endif; ?>
    </nav>
</header>