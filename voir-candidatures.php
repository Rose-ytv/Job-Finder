<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'recruteur') {
    header("Location: connexion.php");
    exit;
}

$id_recruteur = $_SESSION['id'];
$nom = $_SESSION['nom'];
$candidatures_file = "data/candidatures.csv";
$offres_file = "data/offres.csv";
$utilisateurs_file = "data/utilisateurs.csv";

$candidatures = [];
$offres_recruteur = [];

// Charger les offres publiées par ce recruteur
if (file_exists($offres_file)) {
    $fp = fopen($offres_file, "r");
    while (($row = fgetcsv($fp, 1000, ",")) !== false) {
       // Colonne 1 = id_recruteur
        if ($row[1] == $id_recruteur) {
            $offres_recruteur[$row[0]] = $row; // id_offre => détails
        }
    }
    fclose($fp);
}

// Charger les candidatures sur ses offres
if (file_exists($candidatures_file)) {
    $fp = fopen($candidatures_file, "r");
    while (($cand = fgetcsv($fp, 1000, ",")) !== false) {
       // [0]=id_offre, [1]=id_candidat, [2]=id_recruteur, [3]=date, [4]=cv, [5]=lm
        $id_offre    = $cand[0];
        $id_candidat = $cand[1];
        $id_rec      = $cand[2];
        // On ne garde que celles destinées à ce recruteur
        if ($id_rec == $id_recruteur && isset($offres_recruteur[$id_offre])) {
            $candidatures[] = $cand;
        }
    }
    fclose($fp);
}

// Charger les données des utilisateurs (candidats uniquement)
$candidats = [];
if (file_exists($utilisateurs_file)) {
    $fp = fopen($utilisateurs_file, "r");
    while (($u = fgetcsv($fp, 1000, ",")) !== false) {
        if ($u[1] === "candidat") {
            $candidats[$u[0]] = $u;
        }
    }
    fclose($fp);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title> Candidatures reçues</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard">
  <div class="sidebar">
    <h2>Bonjour, <?= htmlspecialchars($nom) ?></h2>
    <a href="dashboard-recruteur.php"> Tableau de bord</a>
    <a href="ajouter-offre.php"> Ajouter une offre</a>
    <a href="voir-candidatures.php" class="active"> Candidatures reçues</a>
    <a href="deconnexion.php"> Déconnexion</a>
  </div>

  <div class="dashboard-main">
    <h1> Candidatures reçues</h1>

    <?php if (empty($candidatures)): ?>
      <p>Aucune candidature reçue pour vos offres.</p>
    <?php else: ?>
      <?php foreach ($candidatures as $cand): ?>
        <?php
          $id_offre    = $cand[0];
          $id_candidat = $cand[1];
          $date        = $cand[3];
          $cv          = $cand[4];
          $lm          = $cand[5] ?? '';
          $offre       = $offres_recruteur[$id_offre];
          $candidat    = $candidats[$id_candidat] ?? null;
        ?>
        <div class="offre-card">
          <h3><?= htmlspecialchars($offre[2]) ?> — <?= htmlspecialchars($offre[3]) ?></h3>
          <p>
            <strong>Candidat :</strong>
            <?php if ($candidat): ?>
              <a href="voir-candidat.php?id=<?= $candidat[0] ?>">
                <?= htmlspecialchars($candidat[2] . " " . $candidat[3]) ?>
              </a>
              (<?= htmlspecialchars($candidat[4]) ?>)
            <?php else: ?>
              Inconnu
            <?php endif; ?>
          </p>
          <p><strong>Date de candidature :</strong> <?= htmlspecialchars($date) ?></p>
          <p>
             <a href="<?= htmlspecialchars($cv) ?>" target="_blank">Voir le CV</a>
            <?php if (!empty($lm)): ?>
              |  <a href="<?= htmlspecialchars($lm) ?>" target="_blank">Lettre de motivation</a>
            <?php endif; ?>
          </p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>

