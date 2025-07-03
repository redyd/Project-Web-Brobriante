<?php

namespace database;

class CategorieRepository extends Repository
{
    const TABLE_NAME = "categorie";
    const TYPE_ID = "cid";

    public function getCategorie(string &$message): array
    {
        $query = "SELECT cid, intitule
                  FROM " . self::TABLE_NAME . "
                  ORDER BY cid";

        $temp = self::returnArrayRequest($message, $query);
        $array = [];
        foreach ($temp as $categorie) {
            $array[$categorie[self::TYPE_ID]] = $categorie["intitule"];
        }
        return $array;
    }

}