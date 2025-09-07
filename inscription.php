<?php
session_start();
$role = $_GET['role'] ?? 'candidat'; // Démarrage de la session et lecture du rôle
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription – Job Finder</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/script.js" defer></script>
</head>
<body class="auth-page">

  <div class="form-container">
    <h1 class="app-title">Job Finder</h1>

    <div class="tabs">
      <a href="inscription.php?role=<?= $role ?>" class="active">S’inscrire</a>
      <a href="connexion.php">Se connecter</a>
    </div>

    <!-- Formulaire d’incription -->
    
    <form method="POST" action="traitement_inscription.php" class="auth-form">
     <!-- Choix du rôle -->
      <label for="role">Je suis :</label>
      <select name="role" id="role" onchange="toggleEntreprise(this.value)">
        <option value="candidat"  <?= $role==='candidat' ? 'selected':'' ?>>Candidat</option>
        <option value="recruteur" <?= $role==='recruteur'?'selected':'' ?>>Recruteur</option>
      </select>

      <label for="nom">Nom :</label>
      <input type="text" name="nom" id="nom" required>

      <label for="prenom">Prénom :</label>
      <input type="text" name="prenom" id="prenom" required>

      <label for="email">Email :</label>
      <input type="email" name="email" id="email" required>

      <label for="mdp">Mot de passe :</label>
      <input type="password" name="mdp" id="mdp" required>
      <!-- Champ “Nom de l’entreprise” pour les recruteurs -->
      <div id="entreprise-field" class="hidden">
        <label for="entreprise">Nom de l’entreprise :</label>
        <input type="text" name="entreprise" id="entreprise">
      </div>
        <!-- bouton pour enregister -->
      <button type="submit" class="btn">Créer mon compte</button>
    </form>
  </div>
  <script src="js/script.js"></script>
</body>
</html>

