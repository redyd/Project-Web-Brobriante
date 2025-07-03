<?php

namespace data;

require_once __DIR__ . '/../const.php';
require_once __DIR__ . '/../const.php';

class Brocanteur
{
    // Champs de base
    public int $bid;
    public string $nom;
    public string $prenom;
    public string $email;
    public string $mot_passe;
    public string|null $photo;
    public string $description;
    public bool $visible;
    public bool $est_administrateur;
    public int|null $emplacement;

    // Informations supplémentaires
    public string|null $nom_zone;
    public string|null $nom_emplacement;

    public function getPhoto(): string
    {
        if (isset($this->photo)) {
            return FILE_UPLOAD . $this->photo;
        } else {
            return "images/icon/default.svg";
        }
    }

    public function createCard(): bool
    {
        if (!isset($this->nom) || !isset($this->bid)) {
            return false;
        }

        $src_item = $this->getPhoto();
        $name_item = $this->getFullName();
        $link_item = "detailBrocanteur.php?id=" . urlencode($this->bid);

        include "inc/card.inc.php";
        return true;
    }

    public function createAdminCard(): bool
    {
        if (!isset($this->nom) || !isset($this->bid)) {
            return false;
        }

        $src_item = $this->getPhoto();
        $name_item = $this->getFullName();
        $link_item = "detailBrocanteur.php?id=" . urlencode($this->bid);
        $id_item = $this->bid;
        $location_item = $this->nom_emplacement ?? null;
        include("inc/cardAdmin.inc.php");
        return true;
    }

    public function getFullName(): string
    {
        return $this->prenom . " " . $this->nom;
    }

    public function getPlaceholders(): array
    {
        return [
            'email' => $this->email,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'photo' => $this->getPhoto(),
            'description' => $this->description,
            'visible' => $this->visible
        ];
    }

    public function getEmplacement(): string
    {
        if (isset($this->emplacement)) {
            return "Emplacement attribué : n°$this->nom_emplacement ($this->nom_zone)";
        } else {
            return "Emplacement pas encore attribué";
        }
    }
}