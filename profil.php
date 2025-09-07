<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'candidat') {
    header("Location: connexion.php");
    exit;
}
$id = $_SESSION['id'];
// Définition des chemins des CSV
$utilisateur_file = __DIR__ . "/data/utilisateurs.csv";
$profil_file      = __DIR__ . "/data/profils.csv";
$job_file         = __DIR__ . "/data/job_recherche.csv";
$comp_file        = __DIR__ . "/data/competences.csv";
$exp_file         = __DIR__ . "/data/experiences.csv";
$diplome_file     = __DIR__ . "/data/diplomes.csv";

//  reload page 
function refresh(){ header("Location: profil.php"); exit; }

// Chargement des informations de base de user
$nom = $prenom = $email = "";
if (($fp = fopen($utilisateur_file, "r")) !== false) {
    while (($row = fgetcsv($fp)) !== false) {
        if ($row[0] == $id) {
            list(,,$nom,$prenom,$email) = $row;
            break;
        }
    }
    fclose($fp);
}

// supp get avant tout post
// supprimer compétence
if (isset($_GET['del_comp'])) {
    $idx = intval($_GET['del_comp']);
    $rows = [];
    if (($fp = fopen($comp_file, "r")) !== false) {
        $i=0;
        while($r = fgetcsv($fp)) {
            if (!($r[0]==$id && $i==$idx)) $rows[]=$r;
            if ($r[0]==$id) $i++;
        }
        fclose($fp);
    }
    $fp = fopen($comp_file,"w");
    foreach($rows as $r) fputcsv($fp,$r);
    fclose($fp);
    refresh();
}
// delete expérience
if (isset($_GET['del_exp'])) {
    $eid = $_GET['del_exp'];
    $rows = [];
    if (($fp = fopen($exp_file,"r"))!==false){
        while($r=fgetcsv($fp)){
            if (!($r[0]==$eid && $r[1]==$id)) $rows[]=$r;
        }
        fclose($fp);
    }
    $fp = fopen($exp_file,"w");
    foreach($rows as $r) fputcsv($fp,$r);
    fclose($fp);
    refresh();
}
// delete diplôme
if (isset($_GET['del_diplome'])) {
    $did = $_GET['del_diplome'];
    $rows = [];
    if (($fp = fopen($diplome_file,"r"))!==false){
        while($r=fgetcsv($fp)){
            if (!($r[0]==$did && $r[1]==$id)) $rows[]=$r;
        }
        fclose($fp);
    }
    $fp = fopen($diplome_file,"w");
    foreach($rows as $r) fputcsv($fp,$r);
    fclose($fp);
    refresh();
}

// traitement des formulaires POST
//  update infos & job rechérchés
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_infos'])) {
    // collect
    $adresse        = trim($_POST['adresse']);
    $job_titre      = $_POST['job_titre'];
    $job_contrat    = $_POST['job_contrat'];
    $job_lieu       = $_POST['job_lieu'];
    $job_salaire_min= $_POST['job_salaire_min'];
    $job_salaire_max= $_POST['job_salaire_max'];
    // ajout d'une photo
    if (!empty($_FILES['photo']['tmp_name'])) {
        if (!is_dir("uploads")) mkdir("uploads");
        $photo = "uploads/photo_{$id}.jpg";
        move_uploaded_file($_FILES['photo']['tmp_name'],$photo);
    } else {
        
        $photo = "";
        if (($fp=fopen($profil_file,"r"))!==false){
            while($r=fgetcsv($fp)){
                if($r[0]==$id){ $photo=$r[1]; break;}
            }
            fclose($fp);
        }
    }
    // upload cv recherché
    if (!empty($_FILES['cv']['tmp_name'])) {
        if (!is_dir("uploads")) mkdir("uploads");
        $job_cv = "uploads/cv_job_{$id}.pdf";
        move_uploaded_file($_FILES['cv']['tmp_name'],$job_cv);
    } else {
        $job_cv = "";
        if (($fp=fopen($job_file,"r"))!==false){
            while($r=fgetcsv($fp)){
                if($r[0]==$id){ $job_cv=$r[6]; break;}
            }
            fclose($fp);
        }
    }
    // reecrire profils.csv
    $rows=[];
    if (($fp=fopen($profil_file,"r"))!==false){
        while($r=fgetcsv($fp)) if($r[0]!=$id) $rows[]=$r;
        fclose($fp);
    }
    $rows[]=[$id,$photo,$adresse];
    $fp=fopen($profil_file,"w");
    foreach($rows as $r) fputcsv($fp,$r);
    fclose($fp);
    // reecrire job_recherche.csv
    $jobs=[];
    if (($fp=fopen($job_file,"r"))!==false){
        while($r=fgetcsv($fp)) if($r[0]!=$id) $jobs[]=$r;
        fclose($fp);
    }
    $jobs[]=[$id,$job_titre,$job_contrat,$job_lieu,$job_salaire_min,$job_salaire_max,$job_cv];
    $fp=fopen($job_file,"w");
    foreach($jobs as $r) fputcsv($fp,$r);
    fclose($fp);
    refresh();
}

// ajouter une compétence
if (isset($_POST['add_comp']) && trim($_POST['competence'])!=='') {
    $c = trim($_POST['competence']);
    $fp=fopen($comp_file,"a");
    fputcsv($fp,[$id,$c]);
    fclose($fp);
    refresh();
}
// ajouter une expérience
if (isset($_POST['add_exp'])) {
    $eid = time();
    $post = $_POST['poste'];
    $entre = $_POST['entreprise'];
    $adr_e = $_POST['adresse_exp'];
    $deb   = $_POST['debut'];
    $fin   = $_POST['fin'];
    $desc  = $_POST['description'];
    $fp=fopen($exp_file,"a");
    fputcsv($fp,[$eid,$id,$post,$entre,$adr_e,$deb,$fin,$desc]);
    fclose($fp);
    refresh();
}
// ajouter un diplôme
if (isset($_POST['add_diplome'])) {
    $did  = time();
    $nom  = $_POST['nom'];
    $etab = $_POST['etablissement'];
    $niv  = $_POST['niveau'];
    $date = $_POST['date'];
    $obt  = $_POST['obtenu'];
    $desc = $_POST['description'];
    $fp=fopen($diplome_file,"a");
    fputcsv($fp,[$did,$id,$nom,$etab,$niv,$date,$obt,$desc]);
    fclose($fp);
    refresh();
}

// chargement des données existantes pour affichage
$adresse = $photo = "";
if (($fp=fopen($profil_file,"r"))!==false){
    while($r=fgetcsv($fp)) if($r[0]==$id){ list(,$photo,$adresse)= $r; break;}
    fclose($fp);
}
$job_titre=$job_contrat=$job_lieu=$job_salaire_min=$job_salaire_max=$job_cv="";
if (($fp=fopen($job_file,"r"))!==false){
    while($r=fgetcsv($fp)) if($r[0]==$id){
        list(,$_, $job_titre,$job_contrat,$job_lieu,$job_salaire_min,$job_salaire_max,$job_cv)= $r;
        break;
    }
    fclose($fp);
}
$competences = [];
if (($fp=fopen($comp_file,"r"))!==false){
    while($r=fgetcsv($fp)) if($r[0]==$id) $competences[] = $r[1];
    fclose($fp);
}
$experiences = [];
if (($fp=fopen($exp_file,"r"))!==false){
    while($r=fgetcsv($fp)) if($r[1]==$id) $experiences[] = $r;
    fclose($fp);
}
$diplomes = [];
if (($fp=fopen($diplome_file,"r"))!==false){
    while($r=fgetcsv($fp)) if($r[1]==$id) $diplomes[] = $r;
    fclose($fp);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Profil de <?= htmlspecialchars("$prenom $nom") ?></title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/script.js"></script>
</head>
<body class="dashboard">
  <div class="sidebar">
    <h2> <?= htmlspecialchars("$prenom $nom") ?></h2>
    <a href="dashboard-candidat.php">Accueil</a>
    <a href="profil.php" class="active"> Profil</a>
    <a href="mes-candidatures.php"> Candidatures</a>
    <a href="deconnexion.php"> Déconnexion</a>
  </div>

  <div class="profile-main">
    <!-- Infos & Job recherché -->
    <div class="card">
      <h3>Informations et job recherché</h3>
      <form method="POST" enctype="multipart/form-data" class="validate">
        <input type="hidden" name="update_infos" value="1">
        <label>Adresse :</label>
        <input type="text" name="adresse" value="<?= htmlspecialchars($adresse) ?>" required>

        <label>Photo :</label>
        <div style="display:flex;align-items:center;gap:10px;">
          <div class="photo-cercle">
            <?php if ($photo && file_exists($photo)): ?>
              <img src="<?= $photo ?>" alt="Photo de profil">
            <?php endif; ?>
          </div>
          <input type="file" name="photo" accept="image/*">
        </div>

        <hr style="grid-column:1/3">

        <h4 style="grid-column:1/3"> Job recherché</h4>
        <label>Titre :</label>
        <input type="text" name="job_titre" value="<?= htmlspecialchars($job_titre) ?>" required>

        <label>Contrat :</label>
        <select name="job_contrat" required>
          <?php foreach (["CDI","CDD","Stage","Alternance","Freelance"] as $c): ?>
            <option value="<?= $c ?>" <?= $job_contrat==$c?"selected":"" ?>><?= $c ?></option>
          <?php endforeach;?>
        </select>

        <label>Lieu :</label>
        <input type="text" name="job_lieu" value="<?= htmlspecialchars($job_lieu) ?>" required>

        <label>Salaire min (€):</label>
        <input type="number" name="job_salaire_min" value="<?= $job_salaire_min ?>" required>

        <label>Salaire max (€):</label>
        <input type="number" name="job_salaire_max" value="<?= $job_salaire_max ?>" required>

        <label>CV (PDF) :</label>
        <div>
          <a href="<?= $job_cv ?>" target="_blank" style="margin-right:10px;"> Voir CV</a>
          <input type="file" name="cv" accept="application/pdf">
        </div>

        <button type="submit"> Enregistrer</button>
      </form>
    </div>

    <!-- Compétences -->
    <div class="card">
      <h3> Compétences</h3>
      <ul class="list-simple">
        <?php foreach($competences as $i=>$c): ?>
          <li>
            <?= htmlspecialchars($c) ?>
            <form method="GET" style="display:inline;">
              <input type="hidden" name="del_comp" value="<?=$i?>">
              <button type="submit" class="delete-btn" data-confirm="Supprimer cette compétence ?"></button>
            </form>
          </li>
        <?php endforeach;?>
      </ul>
      <form method="POST" class="validate">
        <input type="text" name="competence" placeholder="Nouvelle compétence" required>
        <button type="submit" name="add_comp">Ajouter</button>
      </form>
    </div>

    <!-- Expériences -->
    <div class="card">
      <h3> Expériences</h3>
      <?php foreach($experiences as $e): ?>
        <div class="item">
          <strong><?= htmlspecialchars($e[2]) ?></strong> chez <?= htmlspecialchars($e[3]) ?><br>
           <?= htmlspecialchars($e[4]) ?> —  <?= htmlspecialchars($e[5]) ?>→<?= $e[6]?:'Aujourd’hui' ?><br>
          <?= nl2br(htmlspecialchars($e[7])) ?>
          <form method="GET" style="display:inline;">
            <input type="hidden" name="del_exp" value="<?=$e[0]?>">
            <button type="submit" class="delete-btn" data-confirm="Supprimer cette expérience ?"></button>
          </form>
        </div>
        <hr>
      <?php endforeach;?>
      <form method="POST" class="validate">
        <input type="text" name="poste"        placeholder="Poste"       required>
        <input type="text" name="entreprise"   placeholder="Entreprise"  required>
        <input type="text" name="adresse_exp"  placeholder="Lieu"        required>
        <input type="month" name="debut"       required>
        <input type="month" name="fin"         placeholder="Fin (optionnel)">
        <textarea name="description" placeholder="Description" required></textarea>
        <button type="submit" name="add_exp">Ajouter</button>
      </form>
    </div>

    <!-- Diplômes -->
    <div class="card">
      <h3> Diplômes &amp; formations</h3>
      <?php if($diplomes): foreach($diplomes as $d): ?>
        <div class="item">
          <strong><?= htmlspecialchars($d[2]) ?></strong> à <?= htmlspecialchars($d[3]) ?> (<?= htmlspecialchars($d[4]) ?>)<br>
           <?= htmlspecialchars($d[5]) ?> — <?= $d[6]==='oui'?'Obtenu':' Non obtenu' ?><br>
          <?= nl2br(htmlspecialchars($d[7])) ?>
          <form method="GET" style="display:inline;">
            <input type="hidden" name="del_diplome" value="<?=$d[0]?>">
            <button type="submit" class="delete-btn" data-confirm="Supprimer ce diplôme ?"></button>
          </form>
        </div>
        <hr>
      <?php endforeach; else: ?>
        <p>Aucun diplôme enregistré.</p>
      <?php endif;?>
      <form method="POST" class="validate">
        <input type="text" name="nom"           placeholder="Nom diplôme"    required>
        <input type="text" name="etablissement" placeholder="Établissement" required>
        <input type="text" name="niveau"        placeholder="Niveau (ex: Bac+3)" required>
        <input type="text" name="date"          placeholder="Année ou période" required>
        <select name="obtenu">
          <option value="oui"> Obtenu</option>
          <option value="non"> Non obtenu</option>
        </select>
        <textarea name="description" placeholder="Description (optionnel)"></textarea>
        <button type="submit" name="add_diplome">Ajouter</button>
      </form>
    </div>
  </div>
</body>
</html>

