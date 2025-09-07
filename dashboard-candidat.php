<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'candidat') {
    header("Location: connexion.php");
    exit;
}

$id_candidat = $_SESSION['id'];
$nom = $_SESSION['nom'];
$offres_file = "data/offres.csv";
$offres = [];

//  Récupération des filtres depuis GET
$titre_filtre  = strtolower(trim($_GET['titre']   ?? ''));
$lieu_filtre   = strtolower(trim($_GET['lieu']    ?? ''));
$contrat_filtre= $_GET['contrat']   ?? '';
$tri            = $_GET['tri']       ?? 'date';

// Chargement et filtrage des offres publiques
if (file_exists($offres_file)) {
    $fp = fopen($offres_file, "r");
    while (($row = fgetcsv($fp, 1000, ",")) !== false) {
        // [12] cv_public
        if ($row[12] !== 'oui') continue;
        $match = true;
        // filtre titre
        if ($titre_filtre && stripos($row[2], $titre_filtre) === false) {
            $match = false;
        }
        // filtre lieu
        if ($lieu_filtre && stripos($row[6], $lieu_filtre) === false) {
            $match = false;
        }
        // filtre contrat
        if ($contrat_filtre && $row[5] !== $contrat_filtre) {
            $match = false;
        }
        if ($match) {
            $offres[] = [
                'id'          => $row[0],
                'titre'       => $row[2],
                'entreprise'  => $row[3],
                'description' => $row[4],
                'contrat'     => $row[5],
                'lieu'        => $row[6],
                'salaire_min' => $row[7],
                'salaire_max' => $row[8],
                'date_debut'  => $row[9],
                'date_pub'    => $row[10],
            ];
        }
    }
    fclose($fp);
}

//  Tri des résultats
if ($tri === 'salaire') {
    usort($offres, fn($a, $b) => intval($b['salaire_max']) - intval($a['salaire_max']));
} else {
    usort($offres, fn($a, $b) => strcmp($b['date_pub'], $a['date_pub']));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Candidat</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard">
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Bienvenue, <?= htmlspecialchars($nom) ?></h2>
    <a href="dashboard-candidat.php" class="active"> Accueil</a>
    <a href="profil.php"> Mon profil</a>
    <a href="mes-candidatures.php"> Mes candidatures</a>
    <a href="deconnexion.php"> Déconnexion</a>
  </div>

  <!-- Contenu principal -->
  <div class="dashboard-main">
    <h1> Offres disponibles</h1>

    <!-- Formulaire de recherche -->
    <form method="GET" class="search-box" style="margin-bottom: 30px;">
      <div>
        <label for="titre">Quoi ?</label>
        <input type="text" name="titre" id="titre" placeholder="Métier, mot-clé…" 
               value="<?= htmlspecialchars($_GET['titre'] ?? '') ?>">
      </div>
      <div>
        <label for="lieu">Où ?</label>
        <input type="text" name="lieu" id="lieu" placeholder="Ville, code postal…" 
               value="<?= htmlspecialchars($_GET['lieu'] ?? '') ?>">
      </div>
      <div>
        <label for="contrat">Contrat</label>
        <select name="contrat" id="contrat">
          <option value="">Tous</option>
          <?php foreach (['CDI','CDD','Stage','Alternance','Freelance'] as $c): ?>
            <option value="<?= $c ?>" <?= $contrat_filtre === $c ? 'selected' : '' ?>>
              <?= $c ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="tri">Trier par</label>
        <select name="tri" id="tri">
          <option value="date" <?= $tri==='date' ? 'selected' : '' ?>>Date</option>
          <option value="salaire" <?= $tri==='salaire' ? 'selected' : '' ?>>Salaire</option>
        </select>
      </div>
      <button type="submit"> Rechercher</button>
    </form>

    <!-- Résultats -->
    <?php if (empty($offres)): ?>
      <p>Aucune offre ne correspond à vos critères.</p>
    <?php else: ?>
      <?php foreach ($offres as $offre): ?>
        <div class="offre-card">
          <h3><?= htmlspecialchars($offre['titre']) ?></h3>
          <p> <?= htmlspecialchars($offre['lieu']) ?> — <?= htmlspecialchars($offre['contrat']) ?></p>
          <p><?= htmlspecialchars($offre['entreprise']) ?></p>
          <p> <?= htmlspecialchars($offre['salaire_min']) ?> € – <?= htmlspecialchars($offre['salaire_max']) ?> €</p>
          <p> Début : <?= htmlspecialchars($offre['date_debut']) ?></p>
          <a href="offre.php?id=<?= $offre['id'] ?>">Voir l'offre →</a>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
    <script src="js/script.js"></script>
</body>
</html>

