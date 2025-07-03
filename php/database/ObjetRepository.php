<?php

namespace database;

use data\Objet;
use PDOStatement;

require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../const.php';

/**
 * Cette classe permet de faire des requêtes surw des objets.
 */
class ObjetRepository extends Repository
{
    /*
     * Constante de classe
     */
    const TYPE_OBJECT = "Objet";
    const TABLE_NAME = "objet";
    const TYPE_ID = "oid";


    /**
     * Permet de récupérer tous les objets et de les organiser par zone.
     * Les résultats sont stockés dans un tableau indicé au nom de chaque zone.
     *
     * @param string $message Message d'erreur
     * @return bool|array
     */
    public function getObjetByZone(string &$message): bool|array
    {
        $query = "SELECT
                  o.oid,
                  o.intitule,
                  o.image,
                  o.description,
                  c.intitule AS nom_categorie,
                  b.bid as id_brocanteur
                  FROM " . self::TABLE_NAME . " o
                  JOIN brocanteur b ON o.brocanteur = b.bid
                  JOIN categorie c ON o.categorie = c.cid
                  WHERE b.visible AND b.emplacement IS NOT NULL
                  ORDER BY c.intitule, o.intitule";
        return self::getByOrder($message, $query, self::TYPE_OBJECT);
    }

    public function getFilteredObjets(string &$message, string $search, int $id): bool|array
    {
        $query = "SELECT
                  o.oid,
                  o.intitule,
                  o.image,
                  o.description,
                  c.intitule AS nom_categorie,
                  b.bid as id_brocanteur
                  FROM " . self::TABLE_NAME . " o
                  JOIN brocanteur b ON o.brocanteur = b.bid
                  JOIN categorie c ON o.categorie = c.cid
                  WHERE b.visible AND b.emplacement IS NOT NULL
                  AND (o.intitule LIKE :search OR o.description LIKE :search)";
        $query .= $id != 0 ? " AND b.bid LIKE :id " : "";
        $query .= "ORDER BY c.intitule, o.intitule";
        $params = [":search" => "%" . $search . "%", ":id" => $id];
        return self::returnObjectRequest($message, $query, self::TYPE_OBJECT, $params);
    }

    /**
     * Permet de récupérer un objet grâce à l'ID.
     *
     * @param $message string Message d'erreur
     * @param $id int ID de l'objet à récupérer
     * @return bool|Objet|PDOStatement
     */
    public function getObjectByID(string &$message, int $id): bool|Objet|PDOStatement
    {
        $query = "SELECT
                  o.oid,
                  o.intitule,
                  o.image,
                  o.description,
                  c.intitule AS nom_categorie,
                  c.cid AS id_categorie,
                  z.nom AS nom_zone,
                  CONCAT(b.prenom, ' ', b.nom) AS nom_brocanteur,
                  b.bid AS id_brocanteur,
                  e.code AS id_emplacement,
                  e.zone AS nom_emplacement
                  FROM " . self::TABLE_NAME . " o
                  JOIN brocanteur b ON o.brocanteur = b.bid
                  LEFT JOIN emplacement e ON b.emplacement = e.eid
                  LEFT JOIN zone z ON e.zone = z.zid
                  JOIN categorie c ON o.categorie = c.cid
                  WHERE " . self::TYPE_ID . " = :id";
        $params = [':id' => $id];
        return $this->returnObjectRequest($message, $query, self::TYPE_OBJECT, $params, true);
    }

    /**
     * Permet d'obtenir tous les objets d'un brocanteur.
     *
     * @param string $message Message d'erreur
     * @param int $id ID du brocanteur
     * @return array|bool
     */
    public function getSellerObject(string $message, int $id): array|bool
    {
        $query = "SELECT
                  o.oid,
                  o.intitule,
                  o.image
                  FROM objet o
                  JOIN brocanteur b ON b.bid = o.brocanteur
                  WHERE b.visible AND b.bid = :id";
        $params = [":id" => $id];
        return self::returnObjectRequest($message, $query, self::TYPE_OBJECT, $params);
    }

    /**
     * Permet de supprimer un objet grâce à l'ID.
     *
     * @param string $message Message d'erreur
     * @param int $id ID de l'objet à supprimer
     * @return bool Vrai si l'objet a été supprimer, sinon faux
     */
    public function deleteObjectByID(string &$message, int $id): bool
    {
        return self::deleteByID($message, $id, self::TABLE_NAME, self::TYPE_ID);
    }

    /**
     * Permet d'ajouter un objet dans la BDD. Seuls les arguments requis (définit dans la classe objet)
     * sont obligatoires.
     *
     * @param string $message Message d'erreur
     * @param array $array Tableau indicé avec le nom de la colonne et de sa valeur
     * @return bool|int L'ID de l'objet qui a été inséré, sinon faux
     */
    public function insertObject(string &$message, array $array): bool|int
    {
        if ($this->doubleCheckKey($array)) {
            return self::insert($message, $array, self::TABLE_NAME);
        }
        return false;
    }

    /**
     * Permet de mettre à jour un objet. Il n'est pas nécessaire de passer tous les attributs
     * de l'objet pour le mettre à jour.
     *
     * @param string $message Message d'erreur
     * @param int $id ID de l'objet à mettre à jour
     * @param array $array Tableau indicé aux colonnes voulues avec les valeurs
     * @return bool Vrai si l'objet a été mis à jour, sinon faux
     */
    public function updateObjectByID(string &$message, int $id, array $array): bool
    {
        if ($this->checkKey($array)) {
            return self::update($message, $id, $array, self::TYPE_ID, self::TABLE_NAME);
        }
        return false;
    }

    /**
     * Permet d'obtenir tous les ID des objets.
     *
     * @param string $message Message d'erreur
     * @return bool|array un tableau contenant tous les ID des objets
     */
    public function getAllObjectsID(string &$message): bool|array
    {
        $query = "SELECT " . self::TYPE_ID . "
                  FROM " . self::TABLE_NAME . " o
                  JOIN brocanteur b ON o.brocanteur = b.bid
                  WHERE b.visible
                  ORDER BY :id";
        $params = [':id' => self::TYPE_ID];

        return self::returnArrayRequest($message, $query, $params);
    }

    /**
     * Permet d'obtenir un nombre déterminé d'objets aléatoirement.
     *
     * @param string $message Message d'erreur
     * @param int $nbRand Le nombre d'objets voulu
     * @return bool|array|PDOStatement
     */
    public function getRandom(string &$message, int $nbRand): bool|array|PDOStatement
    {
        $ids = $this->getAllObjectsID($message);
        $ids = array_map(fn($arrayID) => $arrayID[0], $ids);

        $nbRand = min($nbRand, count($ids));
        if ($nbRand == 0) return false;

        $rdIndex = array_rand($ids, $nbRand);
        $random = array_map(fn($index) => $ids[$index], $rdIndex);

        return self::getAllInID($message, $random, self::TABLE_NAME, self::TYPE_ID, self::TYPE_OBJECT);
    }

    /**
     * Permet de déterminer si le tableau passé en argument contient des clés valides.
     *
     * @param array $array tableau à vérifier
     * @return bool Vrai si le tableau est correctement indicé, sinon faux
     */
    private function checkKey(array $array): bool
    {
        $invalidKeys = array_diff(array_keys($array), CORRECT_OBJECTS_KEY);
        if (!empty($invalidKeys)) {
            return false;
        }
        return true;
    }

    /**
     * Permet de vérifier si un tableau d'objet contient des clés valides et toutes
     * les clés requises.
     *
     * @param array $array tableau à vérifier
     * @return bool Vrai si le tableau est correctement indicé, sinon faux
     */
    private function doubleCheckKey(array $array): bool
    {
        $missingRequiredKeys = array_diff(REQUIRE_OBJECTS_KEYS_ON_SUBMIT, array_keys($array));
        if (!empty($missingRequiredKeys)) {
            return false;
        }

        return $this->checkKey($array);
    }
}