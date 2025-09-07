<?php
session_start();

// Redirection si utilisateur est déjà connecté
if (isset($_SESSION['id']) && isset($_SESSION['role'])) {
    $url = $_SESSION['role'] === 'recruteur' ? 'dashboard-recruteur.php' : 'dashboard-candidat.php'; // redirection vers le tableau de bord de l'utilisateur selon son role 
    header("Location: $url");
    exit;
}

$erreur = "";

// Traitement du formulaire de connexion avec boite mail et mdp hashé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération et nettoyage des données envoyées
    $email = strtolower(trim($_POST['email'] ?? ''));
    $mdp_saisi = $_POST['mdp'] ?? '';

   // Vérification que les deux champs sont remplis
    if ($email && $mdp_saisi) {
        $fichier = 'data/utilisateurs.csv'; // Définition du chemin du fichier CSV

        // verifier que le le ficher existe
        if (file_exists($fichier)) {
             // ouverture du fichier ligne par ligner en lecture
            $fp = fopen($fichier, 'r');
            while (($ligne = fgetcsv($fp, 1000, ",")) !== false) {
                $email_csv = strtolower(trim($ligne[4]));
                $hash_mdp = $ligne[5];

                if ($email === $email_csv && password_verify($mdp_saisi, $hash_mdp)) {
                    // Connexion réussie
                    $_SESSION['id'] = $ligne[0];
                    $_SESSION['role'] = $ligne[1];
                    $_SESSION['nom'] = $ligne[2];

                    fclose($fp);
                    $redirect = $_SESSION['role'] === 'recruteur' ? 'dashboard-recruteur.php' : 'dashboard-candidat.php';
                    header("Location: $redirect");
                    exit;
                }
            }
            fclose($fp);
            $erreur = " Identifiants incorrects.";
            
            // Messages d’erreur pour les cas non trouvés
        } else {
            $erreur = " Aucun utilisateur trouvé.";
        }
        // si le champ est vide
    } else {
        $erreur = " Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion - Job Finder</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="form-container">
    <h1>Job Finder</h1>

    <div class="tabs">
      <a href="inscription.php">S’inscrire</a>
      <a href="connexion.php" class="active">Se connecter</a>
    </div>

    <?php if (!empty($erreur)): ?>
      <div class="alert"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form class="validate" method="POST" action="connexion.php">
      <label>Email :</label>
      <input type="email" name="email" required>

      <label>Mot de passe :</label>
      <input type="password" name="mdp" required>

      <button type="submit">Je me connecte</button>
    </form>
  </div>
    <script src="js/script.js"></script>
</body>
</html>

