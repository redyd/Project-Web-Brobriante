<?php

namespace database;

use config\DBLink;
use Exception;
use PDO;

require_once __DIR__ . '/../config/DBLink.php';
require_once __DIR__ . '/../data/Objet.php';
require_once __DIR__ . '/../data/Brocanteur.php';
require_once __DIR__ . '/../data/Emplacement.php';

/**
 * Classe qui propose plusieurs fonctionnalités principales de requête.
 * Cette classe ne peut pas être instanciée.
 */
abstract class Repository
{
    const OBJECT_ROOT = "data";

    /**
     * Permet de récupérer des données sous forme d'objet. Si on attend un seul tuple,
     * l'objet lui-même sera retourné. Sinon, un tableau de cet objet sera retourné.
     *
     * @param string $message Message d'erreur
     * @param string $query Requête SQL (non) préparée
     * @param string $typeObject Type d'objet attendu
     * @param array $params Paramètres de la requête SQL (optionnel)
     * @param bool $single Vrai si on attend un seul objet, sinon faux (de base)
     * @return bool|array|object
     */
    protected function returnObjectRequest(string &$message, string $query, string $typeObject, array $params = [], bool $single = false): bool|array|object
    {
        try {
            $bdd = DBLink::connect2db(MYDB, $message);
            if (!$bdd) return false;
            $stmt = $bdd->prepare($query);
            $stmt->execute($params);
            return $single ? $stmt->fetchObject(self::OBJECT_ROOT . "\\$typeObject")
                : $stmt->fetchAll(PDO::FETCH_CLASS, self::OBJECT_ROOT . "\\$typeObject");
        } catch (Exception $e) {
            $message .= $e->getMessage();
        } finally {
            DBLink::disconnect($bdd);
        }
        return false;
    }

    /**
     * Permet de récupérer des éléments sous forme d'un tableau.
     *
     * @param string $message Message d'erreur
     * @param string $query Requête SQL (non) préparée
     * @param array $params Paramètres de la requête SQL (optionnel)
     * @return bool|array
     */
    protected function returnArrayRequest(string &$message, string $query, array $params = []): bool|array
    {
        try {
            $bdd = DBLink::connect2db(MYDB, $message);
            if (!$bdd) return false;
            $stmt = $bdd->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            $message .= $e->getMessage();
        } finally {
            DBLink::disconnect($bdd);
        }
        return false;
    }

    /**
     * Permet d'exécuter une requête sans attendre de valeurs de retour.
     *
     * @param string $message Message d'erreur
     * @param string $query Requête SQL (non) préparée
     * @param array $params Paramètres de la requête SQL (optionnel)
     * @return bool|int
     */
    protected function voidRequest(string &$message, string $query, array $params = []): bool|int
    {
        try {
            $bdd = DBLink::connect2db(MYDB, $message);
            if (!$bdd) return false;
            $stmt = $bdd->prepare($query);
            if ($stmt->execute($params)) {
                $id = (int) $bdd->lastInsertId();
                if ($id > 0) {
                    return $id;
                }
                return true;
            }
        } catch (Exception $e) {
            $message .= $e->getMessage();
        } finally {
            DBLink::disconnect($bdd);
        }
        return false;
    }

    /* Fonctions enfants des fonctions de requêtes principales */

    /**
     * Permet de récupérer des éléments par zones (objets ou brocanteurs)
     * et le les ordonner par zone.
     *
     * @param string $message Message d'erreur
     * @param string $query Requête SqL
     * @param string $typeObject Le type d'objet à retourner
     * @return object|bool|array
     */
    public function getByZones(string &$message, string $query, string $typeObject): object|bool|array
    {
        $results = self::returnObjectRequest($message, $query, $typeObject);

        // Traitement des résultats dans un tableau
        if (is_array($results)) {
            $array = [];
            foreach ($results as $result) {
                $zoneName = $result->nom_zone;
                if (!isset($array[$zoneName])) {
                    $array[$zoneName] = [];
                }
                $array[$zoneName][] = $result;
            }
            return $array;
        }

        return $results;
    }

    protected function getByOrder(string &$message, string $query, string $typeObject): object|bool|array
    {
        $results = self::returnObjectRequest($message, $query, $typeObject);

        // Traitement des résultats dans un tableau
        if (is_array($results)) {
            $array = [];
            foreach ($results as $result) {
                $categorie = $result->nom_categorie;
                if (!isset($array[$categorie])) {
                    $array[$categorie] = [];
                }
                $array[$categorie][] = $result;
            }
            return $array;
        }

        return $results;
    }

    /**
     * Permet de supprimer un élément par son ID.
     *
     * @param string $message Message d'erreur
     * @param int $id ID de l'élément
     * @param string $table Table dans laquelle se trouve l'élément à supprimer
     * @param string $typeID Le nom de l'ID dans la table
     * @return bool
     */
    protected function deleteByID(string &$message, int $id, string $table, string $typeID): bool
    {
        $query = "DELETE FROM $table WHERE $typeID = :id";
        $params = [':id' => $id];

        return $this->voidRequest($message, $query, $params);
    }

    /**
     * Permet d'insérer un élément par son ID.
     *
     * @param string $message Message d'erreur
     * @param array $value Tableau des champs de l'objet à ajouter indicé par le nom des colonnes
     * @param string $table Table dans laquelle se trouve l'élément à ajouter
     * @return bool|int
     */
    protected function insert(string &$message, array $value, string $table): bool|int
    {
        $value = $this->convertToCorrectTypes($value);
        $key = implode(',', array_keys($value));
        $placeholder = implode(',', array_fill(0, count($value), '?'));
        $query = "INSERT INTO $table ($key) VALUES ($placeholder)";

        return $this->voidRequest($message, $query, array_values($value));
    }

    /**
     * Permet de mettre à jour un élément.
     *
     * @param string $message Message d'erreur
     * @param int|string $id ID de l'élément à modifier
     * @param array $value Tableau des champs à modifier indicé par le nom des colonnes
     * @param string $typeID Le nom de l'ID dans la table
     * @param string $table Table dans laquelle se trouve l'élément à modifier
     * @return bool
     */
    protected function update(string &$message, int|string $id, array $value, string $typeID, string $table): bool
    {
        $value = $this->convertToCorrectTypes($value);
        $placeholder = implode(',', array_map(fn($k) => "$k = :$k", array_keys($value)));
        $query = "UPDATE $table SET $placeholder WHERE $typeID = :id";
        $params = [':id' => $id];
        foreach ($value as $key => $val) {
            $params[":$key"] = $val;
        }

        return $this->voidRequest($message, $query, $params);
    }

    /**
     * Permet de mettre une valeur à NULL.
     *
     * @param string $message Message d'erreur
     * @param int $id ID de l'élément à modifier
     * @param string $value Champs à nuller
     * @param string $table Table dans laquelle se trouve l'élément à nuller
     * @param string $typeID Type de l'ID
     * @return bool
     */
    protected function nullValue(string &$message, int $id, string $value, string $table, string $typeID): bool
    {
        $query = "UPDATE $table SET $value = NULL WHERE $typeID = :id";
        $params = [':id' => $id];
        return $this->voidRequest($message, $query, $params);
    }

    /**
     * Permet de récupérer tous les éléments d'une table dont les ID font partis d'un tableau.
     *
     * @param string $message Message d'erreur
     * @param array $array Tableau des ID voulus
     * @param string $table Table dans laquelle se trouve les éléments recherchés
     * @param string $typeID Le nom de l'ID dans la table
     * @param string $typeObject Le type d'objet retourné
     * @return bool|array
     */
    protected function getAllInID(string &$message, array $array, string $table, string $typeID, string $typeObject): bool|array
    {
        $placeholders = implode(', ', array_map(fn($key) => ":id$key", array_keys($array)));
        $query = "SELECT *
                  FROM " . $table . " o
                  JOIN brocanteur b ON o.brocanteur = b.bid
                  WHERE b.visible
                  AND " . $typeID . " IN ($placeholders)";
        $params = [];
        foreach ($array as $key => $val) {
            $params[":id$key"] = $val;
        }

        return $this->returnObjectRequest($message, $query, $typeObject, $params);
    }

    /**
     * Permet de convertir les données int passées en string en int.
     *
     * @param array $array
     * @return array
     */
    protected function convertToCorrectTypes(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_string($value)) {
                if (is_numeric($value)) {
                    $array[$key] = (int)$value;
                }
            }
        }
        return $array;
    }

}
