<?php
session_start();
//  Vérification d'authentification
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'recruteur') {
    header("Location: connexion.php");
    exit;
}

$id_recruteur = $_SESSION['id'];
$nom_recruteur = $_SESSION['nom'];
$offres = [];

// Charger toutes les offres
$fichier = "data/offres.csv";
if (file_exists($fichier)) {
    $fp = fopen($fichier, "r");
    while (($row = fgetcsv($fp, 1000, ",")) !== false) {
        $offres[] = $row;
    }
    fclose($fp);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Recruteur</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard">
  <div class="sidebar">
    <h2> <?= htmlspecialchars($nom_recruteur) ?></h2>
    <a href="dashboard-recruteur.php" class="active"> Tableau de bord</a>
    <a href="ajouter-offre.php"> Ajouter une offre</a>
    <a href="voir-candidatures.php"> Candidatures reçues</a>
    <a href="deconnexion.php"> Déconnexion</a>
  </div>

  <div class="dashboard-main">
    <h1> Toutes les offres</h1>

    <?php if (empty($offres)): ?>
      <p>Aucune offre publiée pour le moment.</p>
    <?php else: ?>
      <?php foreach ($offres as $offre): 
        list(
          $offre_id, $owner_id, $titre, $entreprise,
          $description, $contrat, $lieu,
          $smin, $smax, $debut, $pub, $cv_req, $lm_req
        ) = $offre;
      ?>
        <div class="offre-card">
          <h3><?= htmlspecialchars($titre) ?> — <?= htmlspecialchars($entreprise) ?></h3>
          <p> <?= htmlspecialchars($lieu) ?> — <?= htmlspecialchars($contrat) ?></p>
          <p> <?= htmlspecialchars($smin) ?>€ – <?= htmlspecialchars($smax) ?>€</p>
          <p> Débute : <?= htmlspecialchars($debut) ?> | Publiée le <?= htmlspecialchars($pub) ?></p>

          <?php if ($owner_id == $id_recruteur): ?>
            <a href="modifier-offre.php?id=<?= $offre_id ?>" class="btn-action"> Modifier</a>
            <a href="supprimer-offre.php?id=<?= $offre_id ?>" class="btn-action" data-confirm="Supprimer cette offre ?"> Supprimer</a>
          <?php else: ?>
            <span class="tag">Proposé par recruteur #<?= $owner_id ?></span>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <script src="js/script.js"></script>
</body>
</html>

