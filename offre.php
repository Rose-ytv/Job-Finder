<?php
session_start(); // demarrage de la session

// Vérification qu'un id est fourni en GET
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    echo " ID de l'offre manquant ou invalide.";
    exit;
}

$id_offre = $_GET['id'];

// Lecture du CSV et recherche de l’offre correspondante
$offre = null;
$offres_file = __DIR__ . '/data/offres.csv';

//  Lecture du CSV et recherche de l'offre
if (file_exists($offres_file)) {
    if (($fp = fopen($offres_file, 'r')) !== false) {
        while (($row = fgetcsv($fp, 1000, ',')) !== false) {
            // row[0] = id_offre
            if ($row[0] === $id_offre) {
                $offre = $row;
                break;
            }
        }
        fclose($fp);
    }
}

//  si offre est introuvable
if (!$offre) {
    echo " Offre introuvable.";
    exit;
}

//  Extraction des champs
// Indices : 0=id,1=id_recruteur,2=titre,3=entreprise,4=description,
// 5=contrat,6=lieu,7=salaire_min,8=salaire_max,9=date_debut,
// 10=date_pub,11=cv_obligatoire,12=lm_obligatoire
list(
    $_, $_,
    $titre,
    $entreprise,
    $description,
    $contrat,
    $lieu,
    $salaire_min,
    $salaire_max,
    $date_debut,
    $_, $_,
    $lm_obligatoire
) = $offre;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($titre) ?> — Détail de l’offre</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <main class="offre-detail">
    <article>
      <h1><?= htmlspecialchars($titre) ?></h1>
      <p class="meta">
        <span class="entreprise"><?= htmlspecialchars($entreprise) ?></span> —
        <span class="contrat"><?= htmlspecialchars($contrat) ?></span>,
        <span class="lieu"><?= htmlspecialchars($lieu) ?></span>
      </p>
      <p class="salaire">
        Salaire : <strong><?= htmlspecialchars($salaire_min) ?> €</strong>
        – <strong><?= htmlspecialchars($salaire_max) ?> €</strong>
      </p>
      <p class="debut">
        Début souhaité : <time datetime="<?= htmlspecialchars($date_debut) ?>"><?= htmlspecialchars($date_debut) ?></time>
      </p>
      <section class="description">
        <?= nl2br(htmlspecialchars($description)) ?>
      </section>

      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'candidat'): ?>
        <a href="postuler.php?id=<?= $id_offre ?>" class="btn btn-primary">
           Postuler
        </a>
      <?php else: ?>
        <p class="info"> Connectez-vous en tant que candidat pour postuler.</p>
      <?php endif; ?>

      <p class="back"><a href="offres.php">← Retour à la liste des offres</a></p>
    </article>
  </main>
  <script src="js/script.js"></script>
</body>
</html>

