<?php
// traitement_inscription.php
session_start();

// Ne traiter que les requêtes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: inscription.php');
    exit;
}

// Récupération et nettoyage des données
$role       = $_POST['role']        ?? '';
$nom        = trim($_POST['nom']    ?? '');
$prenom     = trim($_POST['prenom'] ?? '');
$email      = trim($_POST['email']  ?? '');
$mdp        = $_POST['mdp']         ?? '';
$entreprise = trim($_POST['entreprise'] ?? '');

// Tableau pour collecter les éventuelles erreurs
$erreurs = [];

//  Validation des champs
if (!in_array($role, ['candidat','recruteur'], true)) {
    $erreurs[] = "Rôle invalide.";
}
if ($nom === '' || $prenom === '' || $email === '' || $mdp === '') {
    $erreurs[] = "Tous les champs obligatoires doivent être remplis.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse e-mail est invalide.";
}
if (strlen($mdp) < 6) {
    $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";
}
if ($role === 'recruteur' && $entreprise === '') {
    $erreurs[] = "Le nom de l'entreprise est obligatoire pour un recruteur.";
}

//  Vérification de l'unicité de l'e-mail 
$fichier = __DIR__ . '/data/utilisateurs.csv';
if (file_exists($fichier) && is_readable($fichier)) {
    if (($fp = fopen($fichier, 'r')) !== false) {
        while (($row = fgetcsv($fp, 1000, ',')) !== false) {
            $mailExistant = $row[4] ?? '';
            if (strcasecmp($mailExistant, $email) === 0) {
                $roleExistant = $row[1] ?? 'utilisateur';
                $erreurs[] = "Cet e-mail est déjà utilisé par un compte “{$roleExistant}”.";
                break;
            }
        }
        fclose($fp);
    }
}

//  Si des erreurs, on les stocke et on redirige vers le formulaire
if (!empty($erreurs)) {
    $_SESSION['flash_erreurs'] = $erreurs;
    header('Location: inscription.php?role=' . urlencode($role));
    exit;
}

//  Hachage du mot de passe
$hash = password_hash($mdp, PASSWORD_DEFAULT);

// 5) Création d'un ID unique (timestamp)
$id = time();

//  Sauvegarde dans le CSV
if (($fp = fopen($fichier, 'a')) === false) {
    die("Impossible d'ouvrir le fichier d'utilisateurs.");
}
fputcsv($fp, [
    $id,
    $role,
    $nom,
    $prenom,
    $email,
    $hash,
    // pour un recruteur, on stocke aussi l'entreprise
    ($role === 'recruteur' ? $entreprise : '')
]);
fclose($fp);

//  Connexion de l'utilisateur et redirection vers son dashboard
$_SESSION['id']   = $id;
$_SESSION['role'] = $role;
$_SESSION['nom']  = $prenom;

if ($role === 'candidat') {
    header('Location: dashboard-candidat.php');
} else {
    header('Location: dashboard-recruteur.php');
}
exit;

