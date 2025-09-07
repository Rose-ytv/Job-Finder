<?php
session_start();

// Accès restreint aux candidats
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'candidat') {
    header("Location: connexion.php");
    exit;
}

$id_candidat     = $_SESSION['id'];
$offres_file     = "data/offres.csv";
$cand_file       = "data/candidatures.csv";
$id_offre        = $_GET['id'] ?? null;
$offre           = null;
$erreur          = "";
$deja_postule    = false;

// Chargement de l'offre
if ($id_offre && file_exists($offres_file)) {
    $fp = fopen($offres_file, "r");
    while (($row = fgetcsv($fp, 1000, ",")) !== false) {
        if ($row[0] == $id_offre) {
            $offre = $row;
            break;
        }
    }
    fclose($fp);
}

if (!$offre) {
    die(" Offre introuvable.");
}
// La lettre de motivation peut être déclarée "oui" à la colonne 13 du CSV
$lettre_obligatoire = ($offre[13] ?? '') === "oui";

// Vérifier si le candidat a déjà postulé
if (file_exists($cand_file)) {
    $fp = fopen($cand_file, "r");
    while (($c = fgetcsv($fp, 1000, ",")) !== false) {
        if ($c[0] == $id_offre && $c[1] == $id_candidat) {
            $deja_postule = true;
            break;
        }
    }
    fclose($fp);
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($deja_postule) {
        $erreur = " Vous avez déjà postulé à cette offre.";
    } elseif (empty($_FILES['cv']['tmp_name'])) {
        $erreur = "Le CV est obligatoire.";
    } elseif ($lettre_obligatoire && empty($_FILES['lm']['tmp_name'])) {
        $erreur = "La lettre de motivation est obligatoire.";
    } else {
            // Création du dossier d'uploads si nécessaire
        if (!is_dir("uploads")) mkdir("uploads");
            // Déplacement des fichiers uploadés
        $cv_path = "uploads/cv_{$id_offre}_{$id_candidat}.pdf";
        move_uploaded_file($_FILES['cv']['tmp_name'], $cv_path);

        $lm_path = "";
        if (!empty($_FILES['lm']['tmp_name'])) {
            $lm_path = "uploads/lm_{$id_offre}_{$id_candidat}.pdf";
            move_uploaded_file($_FILES['lm']['tmp_name'], $lm_path);
        }
        // Enregistrement de la candidature dans le CSV
        $date = date("Y-m-d");
        $fp = fopen($cand_file, "a");
        fputcsv($fp, [$id_offre, $id_candidat, $date, $cv_path, $lm_path]);
        fclose($fp);
        // Confirmation et redirection vers  Mes candidatures 
        echo "<script>alert(' Candidature envoyée.'); window.location='mes-candidatures.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Postuler à <?= htmlspecialchars($offre[2]) ?></title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/script.js"></script>
</head>
<body>

  <div class="offre-detail">
    <h2> Postuler à : <?= htmlspecialchars($offre[2]) ?></h2>
    <p><strong>Entreprise :</strong> <?= htmlspecialchars($offre[3]) ?></p>

    <?php if ($erreur): ?>
      <div class="alert"><?= htmlspecialchars($erreur) ?></div>
    <?php elseif ($deja_postule): ?>
      <div class="alert"> Vous avez déjà postulé à cette offre.</div>
    <?php else: ?>
      <form method="POST" enctype="multipart/form-data" class="offre-form">
        <label>CV (PDF) :</label>
        <input type="file" name="cv" id="cv" accept="application/pdf" required>
        <label>Lettre de motivation <?= $lettre_obligatoire ? "(obligatoire)" : "(optionnel)" ?> :
        </label>
        <input type="file" name="lm" id="lm" accept="application/pdf"
               <?= $lettre_obligatoire ? "required" : "" ?>>

        <button type="submit" class="btn"> Envoyer ma candidature</button>
      </form>
    <?php endif; ?>

    <a href="dashboard-candidat.php" class="back"> Retour</a>
  </div>

</body>
</html>

