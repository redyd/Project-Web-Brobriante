<?php

namespace data;

require_once __DIR__ . '/../const.php';

class Objet
{
    // Champs de base
    public int $oid;
    public string $intitule;
    public string $description;
    public string|null $image;
    public int $categorie;
    public int $brocanteur;

    // Information supplémentaire
    public int $id_brocanteur;
    public string $nom_brocanteur;
    public int $id_categorie;
    public string $nom_categorie;
    public string|null $nom_zone;
    public int|null $id_emplacement;
    public string|null $nom_emplacement;

    public function getImage(): string
    {
        if (isset($this->image)) {
            return FILE_UPLOAD . $this->image;
        } else {
            return "images/icon/default.svg";
        }
    }

    /**
     * Permet d'obtenir les informations nécessaires pour préremplir un formulaire.
     *
     * @return array Un tableau indicé avec les bonnes valeurs
     */
    public function getPlaceholders(): array
    {
        return [
            'intitule' => $this->intitule,
            'image' => $this->image,
            'description' => $this->description,
            'categorie' => $this->id_categorie
        ];
    }

    /**
     * Permet de créer une carte grâce aux informations.
     *
     * @return bool Vrai si la carte a été créée, sinon faux
     */
    public function createCard(): bool
    {
        if (!isset($this->intitule) || !isset($this->oid)) {
            return false;
        }

        $src_item = $this->getImage();
        $name_item = $this->intitule;
        $link_item = "detailObjet.php?id=" . urlencode($this->oid);

        include "inc/card.inc.php";
        return true;
    }
}