<?php

namespace database;

use data\Brocanteur;

require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../const.php';

class SellerRepository extends Repository
{
    /**
     * Constante de classe
     */
    const TYPE_OBJECT = "Brocanteur";
    const TABLE_NAME = "brocanteur";
    const TYPE_ID = "bid";

    /**
     * Permet d'obtenir un tableau contenant des tableaux de brocanteurs indicés par zone.
     *
     * @param string $message Message d'erreur
     * @return bool|array
     */
    public function getSellersByZone(string &$message): bool|array
    {
        $query = "SELECT
                  b.bid,
                  b.nom,
                  b.prenom,
                  b.email,
                  b.photo,
                  b.description,
                  z.nom AS nom_zone
                  FROM " . self::TABLE_NAME . " b
                  JOIN emplacement e ON b.emplacement = e.eid
                  JOIN zone z ON e.zone = z.zid
                  WHERE b.visible
                  ORDER BY z.nom, b.nom, b.prenom";

        return self::getByZones($message, $query, self::TYPE_OBJECT);
    }

    /**
     * Permet d'obtenir tous les brocanteurs, trié par zone ou alors sans emplacement.
     *
     * @param string $message Message d'erreur
     * @return bool|array
     */
    public function getEverySellers(string &$message): bool|array
    {
        $query = "SELECT
                  b.bid,
                  b.nom,
                  b.prenom,
                  b.email,
                  b.photo,
                  b.description,
                  e.code AS nom_emplacement,
                  z.nom AS nom_zone
                  FROM " . self::TABLE_NAME . " b
                  LEFT JOIN emplacement e ON b.emplacement = e.eid
                  LEFT JOIN zone z ON e.zone = z.zid
                  WHERE b.visible
                  ORDER BY z.nom, b.nom, b.prenom";
        return self::getByZones($message, $query, self::TYPE_OBJECT);
    }

    /**
     * Permet d'obtenir le nom-prénom de chaque brocanteur avec son ID.
     *
     * @param string $message Message d'erreur
     * @return array
     */
    public function getAllSellerNames(string &$message): array
    {
        $query = "SELECT
                  bid,
                  nom,
                  prenom
                  FROM " . self::TABLE_NAME . "
                  WHERE visible AND emplacement IS NOT NULL
                  ORDER BY prenom, nom";

        $temp = self::returnObjectRequest($message, $query, self::TYPE_OBJECT);
        $array = [];
        foreach ($temp as $seller) {
            $array[$seller->bid] = $seller->getFullName();
        }

        return $array;
    }

    /**
     * Permet de récupérer un brocanteur grâce à l'ID.
     *
     * @param $message string Message d'erreur
     * @param $id int ID du brocanteur à récupérer
     * @return bool|object
     */
    public function getSellerByID(string &$message, int $id): bool|object
    {
        $query = "SELECT
                  b.bid,
                  b.nom,
                  b.prenom,
                  b.description,
                  b.photo,
                  b.email,
                  b.visible,
                  z.nom AS nom_zone,
                  e.code AS nom_emplacement,
                  e.eid AS emplacement
                  FROM brocanteur b
                  LEFT JOIN emplacement e ON b.emplacement = e.eid
                  LEFT JOIN zone z ON e.zone = z.zid
                  WHERE b.bid = :bid";
        $params = ["bid" => $id];

        return self::returnObjectRequest($message, $query, self::TYPE_OBJECT, $params, true);
    }

    public function connexion(string &$message, string $email, string $password): object|bool
    {
        $query = "SELECT *
                  FROM brocanteur b
                  WHERE b.email = :email AND b.mot_passe = :password";
        $params = [":email" => $email, ":password" => hash('sha256', $password)];

        return self::returnObjectRequest($message, $query, self::TYPE_OBJECT, $params, true);
    }

    /**
     * Permet de supprimer un brocanteur grâce à l'ID.
     *
     * @param string $message Message d'erreur
     * @param int $id ID du brocanteur à supprimer
     * @return bool Vrai si le brocanteur a été supprimer, sinon faux
     */
    public function deleteSellerByID(string &$message, int $id): bool
    {
        return self::deleteByID($message, $id, self::TABLE_NAME, self::TYPE_ID);
    }

    /**
     * Permet d'ajouter un brocanteur dans la BDD. Seuls les arguments requis (définit dans la classe Brocanteur)
     * sont obligatoires.
     *
     * @param string $message Message d'erreur
     * @param array $array Tableau indicé avec le nom de la colonne et de sa valeur
     * @return bool|int L'ID du brocanteur qui a été inséré, sinon faux
     */
    public function insertSeller(string &$message, array $array): bool|int
    {
        if ($this->doubleCheckKey($array)) {
            return self::insert($message, $array, self::TABLE_NAME);
        } else {
            $message = "les données sont invalides";
        }

        return false;
    }

    /**
     * Permet de mettre à jour un brocanteur. Il n'est pas nécessaire de passer tous les attributs
     * du brocanteur pour le mettre à jour.
     *
     * @param string $message Message d'erreur
     * @param int $id ID du brocanteur à mettre à jour
     * @param array $array Tableau indicé aux colonnes voulues avec les valeurs
     * @return bool Vrai si le brocanteur a été mis à jour, sinon faux
     */
    public function updateSellerByID(string &$message, int $id, array $array): bool
    {
        if ($this->checkKey($array)) {
            return self::update($message, $id, $array, self::TYPE_ID, self::TABLE_NAME);
        }
        return false;
    }

    /**
     * Permet de mettre à jour l'emplacement d'un brocanteur.
     *
     * @param string $message Message d'erreur
     * @param int $id ID du brocanteur
     * @param int|null $newEmplacement L'ID du nouvel emplacement
     * @return bool
     */
    public function updateEmplacement(string &$message, int $id, int|null $newEmplacement): bool
    {
        return self::update($message, $id, ["emplacement" => $newEmplacement], self::TYPE_ID, self::TABLE_NAME);
    }

    /**
     * Permet de mettre une valeur d'un brocanteur à null.
     *
     * @param string $message Message d'erreur
     * @param int $id ID de l'élément à nuller
     * @param string $value Champs à nuller
     * @return bool
     */
    public function nullSellerValue(string &$message, int $id, string $value): bool
    {
        return self::nullValue($message, $id, $value, self::TABLE_NAME, self::TYPE_ID);
    }

    public function emailInBDD(string &$message, string $email): bool
    {
        $query = "SELECT 1 FROM " . self::TABLE_NAME . " WHERE email = :email";
        $params = ["email" => $email];
        return !empty(self::returnObjectRequest($message, $query, self::TYPE_OBJECT, $params, true));
    }

    public function updatePassword(string &$message, string $email, string $newPassword): bool
    {
        $query = "UPDATE " . self::TABLE_NAME . " SET mot_passe = :mot_passe WHERE email = :email";
        $params = [":mot_passe" => $newPassword, ":email" => $email];
        return self::voidRequest($message, $query, $params);
    }


    /**
     * Permet de déterminer si le tableau passé en argument contient des clés valides.
     *
     * @param array $array tableau à vérifier
     * @return bool Vrai si le tableau est correctement indicé, sinon faux
     */
    private function checkKey(array $array): bool
    {
        $invalidKeys = array_diff(array_keys($array), CORRECT_SELLER_KEY);
        if (!empty($invalidKeys)) {
            return false;
        }
        return true;
    }

    /**
     * Permet de vérifier si un tableau de brocanteur contient des clés valides et toutes
     * les clés requises.
     *
     * @param array $array tableau à vérifier
     * @return bool Vrai si le tableau est correctement indicé, sinon faux
     */
    private function doubleCheckKey(array $array): bool
    {
        $missingRequiredKeys = array_diff(REQUIRE_SELLER_KEYS_ON_SUBMIT, array_keys($array));
        if (!empty($missingRequiredKeys)) {
            return false;
        }
        return $this->checkKey($array);
    }
}