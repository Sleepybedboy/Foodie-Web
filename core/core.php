<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');

$_TITRE_PAGE = "Foodie";

$mysqli = new mysqli('localhost', 'foodie', 'foodie', 'Foodie');
if ($mysqli->connect_errno) {
    exit('Probleme de connexion à la BDD');
}

session_start();


function ppp($txt = "")
{
    global $mysqli;
    return  $mysqli->real_escape_string(trim($txt));
}
