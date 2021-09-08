<?php
// connexion à la BDD
$dsn  = 'mysql:host=localhost; dbname=Votre_BDD;charset=utf8'; // Remplacez Votre_BDD par le nom de votre base de données
$user = 'Votre_LOGIN'; // Remplacez Votre_LOGIN par le Login de connexion à votre base de données
$pass = 'Votre_PASS'; // Remplacez Votre_PASS par le mot de passe de connexion à votre base de données

try {
  $cnx = new PDO($dsn, $user, $pass);
  
} catch (PDOException $e) {
  echo 'Erreur de cnx à la bdd :' . $e;
}

?>
