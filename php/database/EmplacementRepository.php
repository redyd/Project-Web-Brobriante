<?php

namespace database;

require_once __DIR__ . '/Repository.php';

class EmplacementRepository extends Repository
{
    const TYPE_OBJECT = "Emplacement";
    const TABLE_NAME = "emplacement";

    /**
     * Permet de récupérer tous les emplacements encore disponibles sous forme d'un tableau.
     *
     * @param string $message Message d'erreur
     * @param int $current L'ID de l'emplacement actuel à ajouter au résultat (optionnel)
     * @return bool|array
     */
    public function getAvailableEmplacement(string &$message, int $current = 0): bool|array
    {
        $query = "SELECT
                  e.eid,
                  e.code
                  FROM emplacement e
                  LEFT JOIN brocanteur b ON e.eid = b.emplacement
                  WHERE b.emplacement IS NULL OR e.eid = :id";
        $params = [":id" => $current];
        $result = self::returnObjectRequest($message, $query, self::TYPE_OBJECT, $params);

        $formated = [];
        foreach ($result as $emplacement) {
            $formated[$emplacement->eid] = $emplacement->code;
        }

        return $formated;
    }

}