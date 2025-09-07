<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'recruteur') {
    header("Location: connexion.php");
    exit;
}

$recruteur_id = $_SESSION['id'];
$nom_recruteur = $_SESSION['nom'];
 // Traitement du formulaire (méthode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_offre = time(); // identifiant unique
    $titre = $_POST['titre'];
    $entreprise = $_POST['entreprise'];
    $description = $_POST['description'];
    $contrat = $_POST['contrat'];
    $lieu = $_POST['lieu'];
    $salaire_min = $_POST['salaire_min'];
    $salaire_max = $_POST['salaire_max'];
    $date_debut = $_POST['date_debut'];
    $date_pub = date('Y-m-d');
    $cv_obligatoire = $_POST['cv_obligatoire'];
    $lm_obligatoire = $_POST['lm_obligatoire'];

    $f = fopen("data/offres.csv", "a");
    fputcsv($f, [$id_offre, $recruteur_id, $titre, $entreprise, $description, $contrat, $lieu, $salaire_min, $salaire_max, $date_debut, $date_pub, $cv_obligatoire, $lm_obligatoire]);
    fclose($f);

    echo "<script>alert('Offre ajoutée !'); window.location='dashboard-recruteur.php';</script>";
    exit;
}
?>
<!-- Affichage du formulaire (méthode GET) -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter une offre</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard">
  <div class="sidebar">
    <h2> <?= htmlspecialchars($nom_recruteur) ?></h2>
    <a href="dashboard-recruteur.php">Tableau de bord</a>
    <a href="ajouter-offre.php" class="active"> Ajouter une offre</a>
    <a href="voir-candidatures.php"> Candidatures reçues</a>
    <a href="deconnexion.php"> Déconnexion</a>
  </div>

  <div class="dashboard-main">
    <h1> Ajouter une nouvelle offre</h1>

    <form method="POST" class="form-block">
  <!-- Titre -->
  <label for="titre">Titre du poste :</label>
  <input type="text" name="titre" id="titre" required>

  <!-- Entreprise -->
  <label for="entreprise">Entreprise :</label>
  <input type="text" name="entreprise" id="entreprise" required>

  <!-- Description -->
  <label for="description">Description du poste :</label>
  <textarea name="description" id="description" rows="4" required></textarea>

  <!-- Type de contrat -->
  <label for="contrat">Type de contrat :</label>
  <select name="contrat" id="contrat" required>
    <option value="CDI">CDI</option>
    <option value="CDD">CDD</option>
    <option value="Stage">Stage</option>
    <option value="Alternance">Alternance</option>
    <option value="Freelance">Freelance</option>
  </select>

  <!-- Lieu -->
  <label for="lieu">Lieu :</label>
  <input type="text" name="lieu" id="lieu" required>

  <!-- Salaire min/max côte à côte -->
  <div class="salary-group">
    <div>
      <label for="salaire_min">Salaire minimum (€) :</label>
      <input type="number" name="salaire_min" id="salaire_min" required>
    </div>
    <div>
      <label for="salaire_max">Salaire maximum (€) :</label>
      <input type="number" name="salaire_max" id="salaire_max" required>
    </div>
  </div>

  <!-- Date de début -->
  <label for="date_debut">Date de début souhaitée :</label>
  <input type="date" name="date_debut" id="date_debut" required>

  <!-- CV / LM -->
  <label for="cv_obligatoire">CV obligatoire ?</label>
  <select name="cv_obligatoire" id="cv_obligatoire" required>
    <option value="oui">Oui</option>
    <option value="non">Non</option>
  </select>

  <label for="lm_obligatoire">Lettre de motivation obligatoire ?</label>
  <select name="lm_obligatoire" id="lm_obligatoire" required>
    <option value="oui">Oui</option>
    <option value="non">Non</option>
  </select>

  <!-- soumettre -->
  <button type="submit" class="btn-action"> Enregistrer l’offre</button>
</form>

    
  </div>
    <script src="js/script.js"></script>
</body>
</html>

