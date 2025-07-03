<?php

// Constantes des fichiers
const FILE_UPLOAD = "uploads/";
const MAX_UPLOAD_SIZE = 5 * 1024 * 1024;
const AUTHORIZE_EXTENSIONS = [
    "jpg",
    "jpeg",
    "png",
    "gif",
    "svg"
];

// Constantes des brocanteurs
const REQUIRE_SELLER_KEYS_ON_SUBMIT = [
    'nom',
    'prenom',
    'email',
    'mot_passe'
];
const CORRECT_SELLER_KEY = [
    'nom',
    'prenom',
    'email',
    'mot_passe',
    'photo',
    'description',
    'visible',
    'est_administrateur',
    'emplacement'
];

// Constantes des objets
const REQUIRE_OBJECTS_KEYS_ON_SUBMIT = [
    'intitule',
    'categorie',
    'brocanteur',
    'description'
];
const CORRECT_OBJECTS_KEY = [
    'oid',
    'intitule',
    'description',
    'image',
    'categorie',
    'brocanteur'
];

const COOKIE_TIME = 30; // DAY

const USER_ROOT = "/~q240078";