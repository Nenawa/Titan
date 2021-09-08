<?php
// connexion à la BDD
require_once('cnx.php');


// on vérifie que le formulaire existe
if (isset($_POST['inscription'])) {

    // on nettoie les données pour qu'elles aient la forme attendue
    function valid_donnees($donnees)
    {
        $donnees = trim($donnees);
        $donnees = stripslashes($donnees);
        $donnees = htmlspecialchars($donnees);
        return $donnees;
    }

    $prenom = valid_donnees($_POST["prenom"]);
    $nom    = valid_donnees($_POST["nom"]);
    $email  = valid_donnees($_POST["email"]);


    // on valide les champs avant de les injecter dans la BDD
    // on vérifie que le champ prénom est rempli correctement
    if (!empty($prenom) && strlen($prenom) <= 20 && preg_match("#^[A-Za-z'àáâãäåçèéêëìíîïðòóôõöùúûüýÿ -]+$#", $prenom)) {

        // on vérifie le champ nom
        if (!empty($nom) && strlen($nom) <= 20 && preg_match("#^[A-Za-z'àáâãäåçèéêëìíîïðòóôõöùúûüýÿ -]+$#", $nom)) {

            // on vérifie le champ email
            if (!empty($email)) {

                //on vérifie que l'adresse email soit valide
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                    //on vérifie que l'adresse email n'existe pas déjà
                    $sql = 'SELECT id FROM membres WHERE email = "' . $email . '"';
                    $testEmail = $cnx->query($sql);
                    if ($testEmail->rowCount() < 1) {

                        // on vérifie si la case est cochée ou non
                        if (!empty($_POST['newsletter'])) {
                            $_POST['newsletter'] = 1;
                        } else {
                            $_POST['newsletter'] = 0;
                        }
                        $newsletter = $_POST["newsletter"];

                        //on peut maintenant insérer le nouveau membre dans la BDD
                        $sql = "INSERT into membres (prenom, nom, email, newsletter) VALUES (:prenom, :nom, :email, :newsletter)";
                        $rs_insert = $cnx->prepare($sql);

                        // on vérifie les valeurs que l'on envoie dans la table
                        $rs_insert->bindParam(':prenom', $prenom, PDO::PARAM_STR, 20);
                        $rs_insert->bindParam(':nom', $nom, PDO::PARAM_STR, 20);
                        $rs_insert->bindParam(':email', $email);
                        $rs_insert->bindParam(':newsletter', $newsletter);

                        //on execute l'insertion dans la BDD
                        $rs_insert->execute();

                        // l'insertion est ok et la case newsletter est cochée 
                        if (($rs_insert) && $newsletter == 1) {
                            $msg = '<p class="msgOk">Bravo ' . $prenom . ', vous êtes inscrit pour faire partie des premiers touristes sur Titan ! <br/>Vous êtes maintenant abonné à la newsletter!</p>';

                            // l'insertion est ok et la case newsletter n'est pas cochée 
                        } elseif (($rs_insert) && $newsletter == 0) {
                            $msg = '<p class="msgOk">Bravo ' . $prenom . ', vous êtes inscrit pour faire partie des premiers touristes sur Titan !</p>';
                        } else {
                            $errorInsert = $cnx->errorInfo();
                            echo $errorInsert[2];
                        }
                    } else {
                        $msg = '<p class="msgErreur">Cette adresse email est déjà utilisée.</p>';
                    }
                } else {
                    $msg = '<p class="msgErreur">L\'adresse mail n\'est pas valide.</p>';
                }
            } else {
                $msg = '<p class="msgErreur">Veuillez renseigner votre email.</p>';
            }
        } else {
            $msg = '<p class="msgErreur">Le nom n\'est pas valide.</p>';
        }
    } else {
        $msg = '<p class="msgErreur">Le prénom n\'est pas valide.</p>';
    }
}






?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="css/normalize.css" />
    <link rel="stylesheet" href="css/main.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Discover Saturne like never">
    <title>TiTan</title>
    <script src="https://kit.fontawesome.com/8630b7e664.js" crossorigin="anonymous"></script>
</head>

<body>
    <!-- contenu -->
    <div class="conteneur">

        <!-- Logo d'entête -->
        <div class="logo ">
            <img src="img/logo.jpg" alt="fusée">
        </div>

        <!-- Chapeau de présentation -->
        <div class="chapo">
            <h1>Inscrivez-vous pour faire partie des premiers touristes de Titan,<br>
                la fameuse lune de Saturne !</h1>
        </div>

        <!-- Message suite au traitement du formulaire -->
        <?php if (isset($_POST['inscription']) && isset($msg)) echo $msg; ?>

        <!-- Formulaire d'inscription -->
        <form class="flex" action="#" method="POST">

            <div class="formulaire">
                <label for="prenom">Prénom<span class="obligatoire">*</span></label>
                <input type="text" name="prenom" id="prenom" required="required">
            </div>

            <div class="formulaire">
                <label for="nom">Nom<span class="obligatoire">*</span></label>
                <input type="text" name="nom" id="nom" required="required">
            </div>

            <div class="formulaire">
                <label for="email">Email<span class="obligatoire">*</span></label>
                <input type="email" name="email" id="email" required="required">
            </div>

            <div class="newsletter">
                <input type="checkbox" name="newsletter" id="newsletter" value="on">
                <label for="newsletter">J'accepte de m'inscrire à la newsletter de Titan</label>
            </div>

            <div class="formulaire">
                <button type="submit" name="inscription">valider mon inscription</button>
            </div>

        </form>

    </div>

</body>

</html>