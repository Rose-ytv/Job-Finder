<?php
session_start();
// Vérification d'authentification et présence de l'ID
if (!isset($_SESSION['id'], $_GET['id']) || $_SESSION['role'] !== 'recruteur') {
    header("Location: connexion.php");
    exit;
}

$id_recruteur = $_SESSION['id'];
$id_offre     = $_GET['id'];
$fichier      = "data/offres.csv";
$offres       = [];
$offre        = null;

//  Lire toutes les offres et extraire celle à modifier si elles sont publier par le user qui est recruteur
if (($fp = fopen($fichier, "r")) !== false) {
    while (($row = fgetcsv($fp, 1000, ",")) !== false) {
        if ($row[0] == $id_offre) {
            if ($row[1] != $id_recruteur) {
                die(" Vous n'avez pas la permission de modifier cette offre.");
            }
            $offre = $row;
        }
        $offres[] = $row;
    }
    fclose($fp);
}
if (!$offre) {
    die(" Offre introuvable.");
}

//  Si POST, on met à jour le CSV
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des valeurs du formulaire
    $titre       = $_POST['titre'];
    $entreprise  = $_POST['entreprise'];
    $description = $_POST['description'];
    $contrat     = $_POST['contrat'];
    $lieu        = $_POST['lieu'];
    $smin        = $_POST['salaire_min'];
    $smax        = $_POST['salaire_max'];
    $debut       = $_POST['date_debut'];
    $pub         = $offre[10];
    $cv_req      = $offre[11];
    $lm_req      = $offre[12];

    // mettre a jour le tableau offre
    foreach ($offres as &$row) {
        if ($row[0] == $id_offre) {
            $row = [
                $id_offre,
                $id_recruteur,
                $titre,
                $entreprise,
                $description,
                $contrat,
                $lieu,
                $smin,
                $smax,
                $debut,
                $pub,
                $cv_req,
                $lm_req
            ];
            break;
        }
    }
    unset($row);

    // Réécriture du fichier csv
    $fp = fopen($fichier, "w");
    foreach ($offres as $row) {
        fputcsv($fp, $row);
    }
    fclose($fp);
    //  Redirection vers le dashboard recruteur
    header("Location: dashboard-recruteur.php");
    exit;
}

// Affichage du formulaire prérempli
list(
    $offre_id, $owner_id, $titre, $entreprise,
    $description, $contrat, $lieu,
    $smin, $smax, $debut, $pub, $cv_req, $lm_req
) = $offre;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier Offre</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard">
  <div class="sidebar">
    <h2>Modifier une offre</h2>
    <a href="dashboard-recruteur.php">← Retour</a>
  </div>
  <div class="dashboard-main">
    <h1>Modifier : <?= htmlspecialchars($titre) ?></h1>
    <form method="POST" class="form-block validate">
      <label>Titre :</label><br>
      <input type="text" name="titre" required value="<?= htmlspecialchars($titre) ?>">

      <label>Entreprise :</label><br>
      <input type="text" name="entreprise" required value="<?= htmlspecialchars($entreprise) ?>">

      <label>Description :</label><br>
      <textarea name="description" rows="4" required><?= htmlspecialchars($description) ?></textarea>

      <label>Contrat :</label><br>
      <select name="contrat">
        <?php foreach (["CDI","CDD","Stage","Alternance","Freelance"] as $c): ?>
          <option value="<?= $c ?>" <?= $contrat === $c ? 'selected' : '' ?>><?= $c ?></option>
        <?php endforeach; ?>
      </select>

      <label>Lieu :</label><br>
      <input type="text" name="lieu" required value="<?= htmlspecialchars($lieu) ?>">

      <label>Salaire min (€) :</label><br>
      <input type="number" name="salaire_min" required value="<?= htmlspecialchars($smin) ?>">

      <label>Salaire max (€) :</label><br>
      <input type="number" name="salaire_max" required value="<?= htmlspecialchars($smax) ?>">

      <label>Date de début :</label><br>
      <input type="date" name="date_debut" required value="<?= htmlspecialchars($debut) ?>">

      <button type="submit" class="btn-action"> Enregistrer</button>
    </form>
  </div>
  <script src="js/script.js"></script>
</body>
</html>

