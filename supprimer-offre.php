<?php
session_start();
if (!isset($_SESSION['id'], $_GET['id']) || $_SESSION['role'] !== 'recruteur') {
    header("Location: connexion.php");
    exit;
}
// Initialisation des variables
$id_recruteur = $_SESSION['id'];
$id_offre     = $_GET['id'];
$offres_file  = "data/offres.csv";
$new        = [];

// Lecture de csv et filtrage
if (($fp = fopen($offres_file, 'r')) !== false) {
    while (($row = fgetcsv($fp, 1000, ",")) !== false) {
        if ($row[0] == $id_offre) {
            // Seulement le propriétaire peut supprimer
            if ($row[1] != $id_recruteur) {
                die(" Vous n'avez pas la permission de supprimer cette offre.");
            }
            continue; // on saute cette ligne => elle sera pas afficher
        }
        $new[] = $row;
    }
    fclose($fp);
}

// Réécriture
if (($fp = fopen($offres_file, 'w')) !== false) {
    foreach ($new as $r) {
        fputcsv($fp, $r);
    }
    fclose($fp);
}

header("Location: dashboard-recruteur.php");
exit;

