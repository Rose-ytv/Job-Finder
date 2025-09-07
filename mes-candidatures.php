<?php
session_start();
// verifation de l'authentification
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'candidat') {
    header("Location: connexion.php");
    exit;
}

$id_candidat = $_SESSION['id'];
$nom = $_SESSION['nom'];
// Chargement des candidatures
$candidatures_file = "data/candidatures.csv";
$offres_file = "data/offres.csv";
$candidatures = [];

// Charger les candidatures du candidat
if (file_exists($candidatures_file)) {
    $fp = fopen($candidatures_file, "r");
    while (($row = fgetcsv($fp, 1000, ",")) !== false) {
        if ($row[1] == $id_candidat) {
            $candidatures[] = $row;
        }
    }
    fclose($fp);
}

// Charger les offres 
$offres = [];
if (file_exists($offres_file)) {
    $fp = fopen($offres_file, "r");
    while (($row = fgetcsv($fp, 1000, ",")) !== false) {
        $offres[$row[0]] = $row;
    }
    fclose($fp);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes candidatures</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard">
  <div class="sidebar">
    <h2>Bienvenue, <?= htmlspecialchars($nom) ?></h2>
    <a href="dashboard-candidat.php"> Accueil</a>
    <a href="profil.php"> Mon profil</a>
    <a href="mes-candidatures.php" class="active"> Mes candidatures</a>
    <a href="deconnexion.php"> Déconnexion</a>
  </div>

  <div class="dashboard-main">
    <h1> Mes candidatures</h1>

    <?php if (count($candidatures) === 0): ?>
      <p>Vous n'avez postulé à aucune offre pour le moment.</p>
    <?php else: ?>
      <?php foreach ($candidatures as $cand): ?>
        <?php
          $id_offre = $cand[0];
          $date = $cand[3];
          $cv = $cand[4];
          $lm = $cand[5];
          $offre = $offres[$id_offre] ?? null;
        ?>
        <?php if ($offre): ?>
          <div class="offre-card">
            <h3><?= htmlspecialchars($offre[2]) ?> — <?= htmlspecialchars($offre[3]) ?></h3>
            <p> Candidature envoyée le : <?= $date ?></p>
            <p> Lieu : <?= htmlspecialchars($offre[6]) ?> — Contrat : <?= htmlspecialchars($offre[5]) ?></p>
            <p> Salaire : <?= htmlspecialchars($offre[7]) ?> € - <?= htmlspecialchars($offre[8]) ?> €</p>
            <p>
            
              <?php if ($lm): ?>
                |  <a href="<?= $lm ?>" target="_blank">Lettre de motivation</a>
              <?php endif; ?>
            </p>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
  <script src="js/script.js"></script>
</html>

