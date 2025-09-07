<?php
// Initialisation et récupération des filtres
$fichier = 'data/offres.csv';
$offres = [];

//  Filtres depuis formulaire GET
$titre = strtolower(trim($_GET['titre'] ?? ''));
$lieu = strtolower(trim($_GET['lieu'] ?? ''));
$contrat = $_GET['contrat'] ?? '';
$tri = $_GET['tri'] ?? 'date';

// Lecture du fichier CSV et application des filtres
if (file_exists($fichier)) {
    $fp = fopen($fichier, "r");
    while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) {
        // Champs dans l'ordre :
        // [0] id, [1] id_recruteur, [2] titre, [3] entreprise,
        // [4] description, [5] contrat, [6] adresse,
        // [7] salaire_min, [8] salaire_max, [9] date_debut, [10] date_creation

        $match = true;

        if ($titre && stripos($data[2], $titre) === false) $match = false;
        if ($lieu && stripos($data[6], $lieu) === false) $match = false;
        if ($contrat && $data[5] !== $contrat) $match = false;

        if ($match) {
            $offres[] = $data;
        }
    }
    fclose($fp);
}

//  Tri
if ($tri === 'salaire') {
    usort($offres, fn($a, $b) => intval($b[8]) - intval($a[8]));
} else {
    usort($offres, fn($a, $b) => strcmp($b[10], $a[10])); // date_creation desc
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Offres d'emploi</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <main>
    <h1> Liste des offres</h1>

    <form method="GET" action="offres.php">
      <label for="titre">Titre de poste :</label>
      <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($titre) ?>">

      <label for="lieu">Lieu :</label>
      <input type="text" name="lieu" id="lieu" value="<?= htmlspecialchars($lieu) ?>">

      <label for="contrat">Type de contrat :</label>
      <select name="contrat" id="contrat">
        <option value="">-- Tous --</option>
        <option value="CDI" <?= $contrat == 'CDI' ? 'selected' : '' ?>>CDI</option>
        <option value="CDD" <?= $contrat == 'CDD' ? 'selected' : '' ?>>CDD</option>
        <option value="Stage" <?= $contrat == 'Stage' ? 'selected' : '' ?>>Stage</option>
        <option value="Alternance" <?= $contrat == 'Alternance' ? 'selected' : '' ?>>Alternance</option>
      </select>

      <label for="tri">Trier par :</label>
      <select name="tri" id="tri">
        <option value="date" <?= $tri == 'date' ? 'selected' : '' ?>>Date</option>
        <option value="salaire" <?= $tri == 'salaire' ? 'selected' : '' ?>>Salaire</option>
      </select>

      <button type="submit"> Rechercher</button>
    </form>

    <!-- Affichage des résultats selon l'option choisér-->
    <section>
      <h2>Résultats :</h2>
      <?php if (empty($offres)) : ?>
        <p>Aucune offre trouvée.</p>
      <?php else : ?>
        <ul>
          <?php foreach ($offres as $offre) : ?>
            <li>
              <strong><?= htmlspecialchars($offre[2]) ?></strong> chez <?= htmlspecialchars($offre[3]) ?>
              (<?= htmlspecialchars($offre[5]) ?> à <?= htmlspecialchars($offre[6]) ?>)
              <br> <?= $offre[7] ?>€ – <?= $offre[8] ?>€
              <br> Début souhaité : <?= $offre[9] ?>
              <br><a href="offre.php?id=<?= $offre[0] ?>"> Voir cette offre</a>
              <hr>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>

    <p><a href="index.php"> Retour à l'accueil</a></p>
  </main>
  <script src="js/script.js"></script>

</body>
</html>
