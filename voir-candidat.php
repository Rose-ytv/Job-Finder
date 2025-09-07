<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'recruteur') {
    header("Location: connexion.php");
    exit;
}
// Récupération de l’identifiant du candidat
$id_candidat = $_GET['id'] ?? null;
if (!$id_candidat) {
    die(" Aucun ID de candidat fourni.");
}
// Initialisation des structures et chemins
$utilisateur = null;
$profil = [];
$competences = [];
$experiences = [];
$diplomes = [];

// Fichiers
$utilisateurs_file = "data/utilisateurs.csv";
$profils_file = "data/profils.csv";
$experiences_file = "data/experiences.csv";
$diplomes_file = "data/diplomes.csv";

// Rechercher le candidat
if (file_exists($utilisateurs_file)) {
    $fp = fopen($utilisateurs_file, "r");
    while (($u = fgetcsv($fp, 1000, ",")) !== false) {
        if ($u[0] == $id_candidat && $u[1] === "candidat") {
            $utilisateur = $u;
            break;
        }
    }
    fclose($fp);
}

// Rechercher son profil
if (file_exists($profils_file)) {
    $fp = fopen($profils_file, "r");
    while (($p = fgetcsv($fp, 1000, ",")) !== false) {
        if ($p[0] == $id_candidat) {
            $profil = $p;
            break;
        }
    }
    fclose($fp);
}

// Rechercher ses expériences
if (file_exists($experiences_file)) {
    $fp = fopen($experiences_file, "r");
    while (($e = fgetcsv($fp, 1000, ",")) !== false) {
        if ($e[1] == $id_candidat) {
            $experiences[] = $e;
        }
    }
    fclose($fp);
}

// Rechercher ses diplômes
if (file_exists($diplomes_file)) {
    $fp = fopen($diplomes_file, "r");
    while (($d = fgetcsv($fp, 1000, ",")) !== false) {
        if ($d[1] == $id_candidat) {
            $diplomes[] = $d;
        }
    }
    fclose($fp);
}

// Compétences 
$competences = !empty($profil[5]) ? json_decode($profil[5], true) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Profil du candidat</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body style="font-family:'Segoe UI', sans-serif; background:#f4f7fb; padding:30px;">
  <div style="max-width:800px; margin:auto; background:white; padding:30px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.08);">

    <?php if (!$utilisateur): ?>
      <p> Candidat introuvable.</p>
    <?php else: ?>
      <h2> <?= htmlspecialchars($utilisateur[2] . " " . $utilisateur[3]) ?></h2>
      <p>Email : <?= htmlspecialchars($utilisateur[4]) ?></p>

      <?php if (!empty($profil[1]) && file_exists($profil[1])): ?>
        <img src="<?= $profil[1] ?>" width="120" style="border-radius:50%; margin:10px 0;">
      <?php endif; ?>

      <p>Adresse : <?= htmlspecialchars($profil[2] ?? '-') ?></p>
      <p>Niveau d'études : <?= htmlspecialchars($profil[4] ?? '-') ?></p>

      <?php if (!empty($profil[3]) && file_exists($profil[3]) && ($profil[2] ?? '') === 'oui'): ?>
        <p> <a href="<?= $profil[3] ?>" target="_blank">Voir le CV</a></p>
      <?php else: ?>
        <p><em>CV non disponible ou privé.</em></p>
      <?php endif; ?>

      <hr>

      <h3> Compétences</h3>
      <ul>
        <?php foreach ($competences as $comp): ?>
          <li><?= htmlspecialchars($comp) ?></li>
        <?php endforeach; ?>
      </ul>

      <h3> Expériences</h3>
      <?php if ($experiences): ?>
        <?php foreach ($experiences as $exp): ?>
          <div style="margin-bottom: 10px;">
            <strong><?= htmlspecialchars($exp[2]) ?></strong> chez <?= htmlspecialchars($exp[3]) ?>
            (<?= $exp[4] ?>)<br>
            <small><?= nl2br(htmlspecialchars($exp[5])) ?></small>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p><em>Aucune expérience renseignée.</em></p>
      <?php endif; ?>

      <h3> Diplômes</h3>
      <?php if ($diplomes): ?>
        <?php foreach ($diplomes as $dip): ?>
          <div style="margin-bottom: 10px;">
            <strong><?= htmlspecialchars($dip[2]) ?></strong> — <?= htmlspecialchars($dip[3]) ?>
            (<?= htmlspecialchars($dip[4]) ?>)<br>
             <?= htmlspecialchars($dip[5]) ?> — <?= $dip[6] === 'oui' ? ' Obtenu' : ' Non obtenu' ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p><em>Aucun diplôme renseigné.</em></p>
      <?php endif; ?>
    <?php endif; ?>

    <p style="margin-top:30px;"><a href="voir-candidatures.php">← Retour aux candidatures</a></p>
  </div>
    <script src="js/script.js"></script>
</body>
</html>

