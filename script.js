

/**
 * Affiche ou cache le champ "Entreprise" selon le rôle sélectionné
 */
function toggleEntreprise(role) {
  const entrepriseField = document.getElementById("entreprise-field");
  if (entrepriseField) {
    entrepriseField.style.display = (role === "recruteur") ? "block" : "none";
  }
}

/**
 * Ajoute une confirmation avant action sur tous les liens <a data-confirm="…">
 */
function ajouterConfirmationSuppression() {
  document.querySelectorAll('a[data-confirm]').forEach(link => {
    link.addEventListener("click", function(e) {
      if (!confirm(link.getAttribute("data-confirm"))) {
        e.preventDefault();
      }
    });
  });
}

/**
 * Validation client-side sur tous les formulaires .validate
 */
function validationForms() {
  document.querySelectorAll('form.validate').forEach(form => {
    form.addEventListener("submit", function(e) {
      //  Tous les champs required
      for (const el of form.querySelectorAll('[required]')) {
        if (!el.value.trim()) {
          const label = el.previousElementSibling?.textContent.trim() || el.name;
          alert('Veuillez remplir le champ « ' + label + ' »');
          el.focus();
          e.preventDefault();
          return;
        }
      }
      //  Email valide
      const email = form.querySelector('input[type="email"]');
      if (email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!re.test(email.value.trim())) {
          alert("Adresse email invalide.");
          email.focus();
          e.preventDefault();
          return;
        }
      }
      //  Mot de passe min 6 caractères
      const pwd = form.querySelector('input[type="password"]');
      if (pwd && pwd.value.length < 6) {
        alert("Le mot de passe doit faire au moins 6 caractères.");
        pwd.focus();
        e.preventDefault();
        return;
      }
    });
  });
}

/**
 * Filtrage "live" des offres (éléments .offre-card)
 */
function filterOffers() {
  const titre   = document.getElementById('titre')?.value.trim().toLowerCase() || '';
  const lieu    = document.getElementById('lieu')?.value.trim().toLowerCase()  || '';
  const contrat = document.getElementById('contrat')?.value                   || '';

  document.querySelectorAll('.offre-card').forEach(card => {
    const text = card.textContent.toLowerCase();
    const okTitre   = !titre   || text.includes(titre);
    const okLieu    = !lieu    || text.includes(lieu);
    const okContrat = !contrat || text.includes(contrat.toLowerCase());
    card.style.display = (okTitre && okLieu && okContrat) ? '' : 'none';
  });
}

/**
 * Branche les écouteurs pour le filtrage
 */
function attachFilterEvents() {
  ['titre','lieu'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', filterOffers);
  });
  const sel = document.getElementById('contrat');
  if (sel) sel.addEventListener('change', filterOffers);
}

// Au chargement du DOM
window.addEventListener("DOMContentLoaded", () => {
  // Toggle "Entreprise" dans inscription.php
  const roleSelect = document.querySelector('select[name="role"]');
  if (roleSelect) {
    toggleEntreprise(roleSelect.value);
    roleSelect.addEventListener("change", e => toggleEntreprise(e.target.value));
  }

  //  Confirmation suppression
  ajouterConfirmationSuppression();

  //  Validation des formulaires qui portent la classe .validate
  validationForms();

  // Filtrage live des offres
  attachFilterEvents();
  filterOffers();

  //  Redirection depuis les boutons de filtre rapide (.tags button)
  document.querySelectorAll('.tags button').forEach(btn => {
    btn.addEventListener('click', () => {
      const contrat = btn.getAttribute('data-contrat');
      if (!contrat) return;
      // On conserve éventuels autres filtres GET (titre, lieu, tri, etc.)
      const params = new URLSearchParams(window.location.search);
      params.set('contrat', contrat);
      window.location.href = 'offres.php?' + params.toString();
    });
  });
});

