<?php session_start(); ?> <!-- debut de la session -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Job Finder – Accueil</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/script.js" defer></script>
</head>
<body class="home-page">

  <section class="hero">
    <div class="hero-content">
      <h1 class="hero-title">Notre job, vous aider<br>à choisir le vôtre</h1>
    </div>
  </section>
  
  <!-- chercher une offre -->
  <div class="search-container">
    <form method="GET" action="offres.php" class="search-box">
      <div class="field">
        <label for="titre">QUOI ?</label>
        <input type="text" name="titre" id="titre" placeholder="Métier, entreprise, compétence…">
      </div>
      <div class="field">
        <label for="lieu">OÙ ?</label>
        <input type="text" name="lieu" id="lieu" placeholder="Ville, département, code postal…">
      </div>
      <button type="submit" class="btn-search">Rechercher</button>
    </form>

    <!-- option afficher les offres selon le type de l'offre -->
    <div class="tags">
  <button type="button" data-contrat="Stage"> Stage</button>
  <button type="button" data-contrat="Alternance"> Alternance</button>
  <button type="button" data-contrat="CDI"> CDI</button>
  <button type="button" data-contrat="CDD"> CDD</button>
  <button type="button" data-contrat="Freelance"> Freelance</button>
</div>


  </div>

  <!-- choix entre candidat ou recruteur -->
  <section class="choix-utilisateur">
    <h2>Vous êtes :</h2>
    <div class="cards-container">
      <a href="inscription.php?role=candidat" class="role-card">
        <h3> Candidat</h3>
        <p>Je cherche un emploi, un stage ou une alternance.</p>
      </a>
      <a href="inscription.php?role=recruteur" class="role-card">
        <h3> Recruteur</h3>
        <p>Je souhaite publier des offres et trouver des talents.</p>
      </a>
    </div>
  </section>

  

  <footer class="site-footer">
    <p>&copy; 2025 Job Finder</p>
  </footer>
  <script src="js/script.js" defer></script>
</body>
</html>

