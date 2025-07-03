<?php

use database\CategorieRepository;
use database\ObjetRepository;
use forms\ItemForm;
use input\BasicInput;
use input\FileInput;
use input\OptionInput;
use input\TextareaInput;

require __DIR__ . '/php/forms/ItemForm.php';
require __DIR__ . '/php/input/FileInput.php';
require __DIR__ . '/php/input/BasicInput.php';
require __DIR__ . '/php/input/TextareaInput.php';
require __DIR__ . '/php/input/OptionInput.php';
require __DIR__ . '/php/database/ObjetRepository.php';
require __DIR__ . '/php/database/CategorieRepository.php';
require __DIR__ . '/php/functions.php';
require __DIR__ . '/inc/session.inc.php';

/* Variable du code HTML */
$title = "";

/* Variable PHP */
$user = $_SESSION["user"] ?? null;
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$message = "";
$success = false;

if ($user == null) {
    header("Location: index.php");
}

$repoObj = new ObjetRepository();
$repoCat = new CategorieRepository();

// Objets de formulaires
$options = $repoCat->getCategorie($message);
$form = new ItemForm($_POST, $options, $_FILES);
$allowedActions = ['a', 'e'];

// Vérification de l'URL valide
if (!in_array($action, $allowedActions) || ($action === 'e' && !$id)) {
    $message = "Erreur dans l'URl.";
    $title = "Erreur";
} else {
    // Switch sur les titres
    $title = match ($action) {
        'e' => "Modifier l'objet",
        'a' => "Ajouter un objet",
        default => "Erreur",
    };

    // On regarde s'il faut préremplir le formulaire
    if ($action == 'e') {
        // Récupération des informations
        $obj = $repoObj->getObjectByID($message, $id);

        if ($obj->id_brocanteur != $user->bid) {
            header("Location: index.php");
        }

        if (!$obj) {
            $message = "Le objet n'existe pas ou l'url est invalide";
        } else {
            $placeholders = $obj->getPlaceholders();
            $image = $obj->getImage();
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                $form->setData($placeholders);
            }
        }
    }

    // On vérifie s'il y a une requête
    if (isPOST() && $user) {
        // Validation du formulaire
        $form->validate();
        $isUpl = $form->uploadFile();
        $imgToUpl = $isUpl ? $form->getFileDestination() : null;

        if ($form->isValid()) {
            switch ($action) {
                // Cas où on ajoute un brocanteur
                case 'a':
                    $obj['brocanteur'] = $user->bid;
                    $obj['image'] = $imgToUpl;
                    $obj += $form->getAllData();

                    $id = $repoObj->insertObject($message, $obj);
                    if (!$id) {
                        $message = "Erreur lors de l'ajout : " . $message;
                        if ($isUpl) {
                            unlink(FILE_UPLOAD . $imgToUpl);
                        }
                    } else {
                        $success = "Objet ajouté avec succès !";
                    }
                    break;
                // Cas où on modifie un brocanteur
                case 'e' && isset($obj):
                    $toUpl = $form->getAllData();
                    if ($isUpl) {
                        $imgToUpl = $form->getFileDestination();
                        $toUpl['image'] = $imgToUpl;
                    }
                    $success = $repoObj->updateObjectByID($message, $id, $toUpl);
                    if ($success) {
                        // Suppression de l'ancienne image
                        if ($isUpl && !empty($obj->image) && file_exists($obj->getImage())) {
                            unlink($obj->getImage());
                        }
                        $success = "Objet mis à jour avec succès !";
                    } else {
                        $message = "Une erreur est survenue : " . $message;
                        if ($isUpl) {
                            unlink(FILE_UPLOAD . $imgToUpl);
                        }
                    }
                    break;
                default:
                    $message = "Une erreur est survenue.";
                    break;
            }
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
    <?php require "inc/backTitle.inc.php" ?>

    <?php if (empty($message) && !$success): ?>
        <div class="detail center space border flex-center">
            <?php
            if ($action == 'e' && isset($image)) {
                echo "<img src='$image' alt='Image' class='detail-img'>";
            }
            ?>
            <form action="<?= PHP_SELF() . "?id=" . $id . "&action=" . $action; ?>" method="post"
                  class="full-space-form" enctype="multipart/form-data">
                <?php
                // Nom de l'objet
                new BasicInput(
                    id: "intitule",
                    name: "Nom de l'objet",
                    form: $form ?? null,
                    placeholder: "Entrez le nom de l'objet",
                    required: true,
                    classInput: "data");

                // Catégorie de l'objet
                new OptionInput(
                    id: "categorie",
                    name: "Catégorie",
                    options: $options,
                    form: $form ?? null,
                    required: true);

                // Image
                new FileInput(
                    id: "image",
                    name: "Image",
                    form: $form ?? null,
                    class: "lab-row");

                // Description
                new TextareaInput(
                    id: "description",
                    name: "Description",
                    form: $form ?? null,
                    placeholder: "Entrez une description...",
                    required: true);
                ?>
                <fieldset class="form-btn">
                    <button type="reset" class="grey-btn" onclick="window.history.back()">Annuler</button>
                    <button type="submit"
                            class="blue-btn"><?= $action == 'e' ? 'Modifier' : 'Ajouter'; ?></button>
                </fieldset>
            </form>
        </div>
    <?php elseif (empty($message) && $success): ?>
        <nav class="center space">
            <p class="success-box flex-center center"><?= $success ?></p>
            <a class="blue-btn" href="detailObjet.php?id=<?= $id ?>">Revenir à l'objet</a>
        </nav>
    <?php else: ?>
        <p class="fail-box flex-center space center"><?= $message ?></p>
    <?php endif; ?>
</main>

<?php require "inc/footer.inc.php"; ?>

</body>

</html>