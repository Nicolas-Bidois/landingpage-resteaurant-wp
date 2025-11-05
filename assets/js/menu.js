// ===== Utilitaire échappement HTML =====
function nbEsc(s){
  return String(s)
    .replaceAll('&','&amp;')
    .replaceAll('<','<')
    .replaceAll('>','>')
    .replaceAll('"','"')
    .replaceAll("'",'&#039;');
}

// ===== CARTE DYNAMIQUE + ONGLET CATEGORIES (remplace la carte statique) =====
(async function(){
  const section = document.getElementById('carte');
  if(!section) return;

  const grid   = section.querySelector('.nb-carte-grid') || (() => {
    const el = document.createElement('div');
    el.className = 'nb-carte-grid';
    section.appendChild(el);
    return el;
  })();

  const tabsEl = section.querySelector('#nb-tabs') || (() => {
    const el = document.createElement('div');
    el.className = 'nb-tabs';
    el.id = 'nb-tabs';
    section.prepend(el);
    return el;
  })();

  // Show loading state
  grid.innerHTML = '<div class="nb-loading">Chargement de la carte...</div>';

  // 1) Catégories depuis l'API WP (taxonomie show_in_rest:true)
  let cats = [];
  try {
    const resCats = await fetch('/wp-json/wp/v2/categorie-plat?per_page=100');
    if(resCats.ok) cats = await resCats.json();
  } catch(e){ /* ignore */ }

  // Ordonne/filtre (adapte la liste pour limiter un "secteur")
  const order = ['entrees','plats','desserts','boissons']; // <- réduis ici si besoin
  const sortedCats = cats
    .filter(t => order.includes(t.slug))
    .sort((a,b)=> order.indexOf(a.slug) - order.indexOf(b.slug));

  // 2) Rendu des onglets
  tabsEl.innerHTML = [
    `<button class="nb-tab is-active" data-cat="all">Tout</button>`,
    ...sortedCats.map(t => `<button class="nb-tab" data-cat="${t.slug}">${nbEsc(t.name)}</button>`)
  ].join('');

  // 3) Récup de tous les plats (endpoint enfant nb/v1/carte)
  let plats = [];
  try {
    const res = await fetch('/wp-json/nb/v1/carte');
    if(!res.ok) throw new Error('API');
    plats = await res.json();
  } catch(e){
    grid.innerHTML = `<p class="nb-info">Erreur de chargement de la carte.</p>`;
    return;
  }

  // 4) Rendu d'une carte (match le design existant .nb-card/.nb-thumb/...)
function cardHTML(p){
  const ep = Math.max(0, Math.min(3, parseInt(p.epice || 0, 10)));
  const spicesHTML = `
    <div class="nb-spices" title="Épices ${ep} sur 3">
      <span aria-hidden="true">${'🌶️'.repeat(ep)}</span>
    </div>`;

  const descHTML = p.description_courte ? `<p class="nb-desc">${nbEsc(p.description_courte)}</p>` : '';

  const allsHTML = p.allergenes
    ? `<div class="nb-alls"><span class="nb-chip">Allergènes : ${nbEsc(p.allergenes)}</span></div>`
    : (p.est_vegetarien ? `<div class="nb-alls"><span class="nb-chip">Végétarien</span></div>` : '');

  return `
    <div class="nb-item" data-cats="${(p.categories||[]).join(' ')}">
      <article class="nb-card" tabindex="0" data-nb-title="${nbEsc(p.title)}" data-nb-desc="${nbEsc(p.description_courte || '')}" data-nb-price="${nbEsc(p.prix || '—')}" data-nb-image="${p.image || ''}" data-nb-spices="${ep}" data-nb-alls="${p.allergenes || (p.est_vegetarien ? 'Végétarien' : '')}">
        <div class="nb-head">
          <div class="nb-left">
            <h3 class="nb-title">${nbEsc(p.title)}</h3>
            ${descHTML}
          </div>
          <div class="nb-right">
            ${p.prix ? `<div class="nb-price">${nbEsc(p.prix)}</div>` : `<div class="nb-price">—</div>`}
            ${spicesHTML}
            ${allsHTML}
          </div>
        </div>
        ${p.image ? `<img src="${p.image}" alt="${nbEsc(p.title)}" class="nb-thumb" loading="lazy">` : ``}
      </article>
    </div>
  `;
}

  // 5) Rendu initial (toutes catégories)
  grid.innerHTML = plats.map(cardHTML).join('');

  // 6) Filtrage par onglet
  tabsEl.addEventListener('click', (e)=>{
    const btn = e.target.closest('.nb-tab');
    if(!btn) return;
    tabsEl.querySelectorAll('.nb-tab').forEach(b=>b.classList.remove('is-active'));
    btn.classList.add('is-active');

    const cat = btn.getAttribute('data-cat');
    const cards = grid.querySelectorAll('.nb-item');
    cards.forEach(c=>{
      if(cat === 'all'){
        c.classList.remove('nb-item-hidden');
        return;
      }
      const cats = (c.getAttribute('data-cats') || '').split(/\s+/);
      if(cats.includes(cat)){
        c.classList.remove('nb-item-hidden');
      } else {
        c.classList.add('nb-item-hidden');
      }
    });
  });
})();

// ===== Gestion de la modale popup pour les détails de la carte =====
(function(){
  const grid = document.querySelector('.nb-carte-grid');
  if(!grid) return;

  let modal = null;

  grid.addEventListener('click', e => {
    const card = e.target.closest('.nb-card');
    if(!card) return;

    const title = card.getAttribute('data-nb-title') || 'Titre';
    const desc = card.getAttribute('data-nb-desc') || '';
    const price = card.getAttribute('data-nb-price') || '—';
    const image = card.getAttribute('data-nb-image') || '';
    const spices = parseInt(card.getAttribute('data-nb-spices') || 0);
    const alls = card.getAttribute('data-nb-alls') || '';

    modal = document.createElement('div');
    modal.className = 'nb-modal';
    modal.innerHTML = `
      <div class="nb-modal-content">
        <button class="nb-modal-close" aria-label="Fermer">&times;</button>
        ${image ? `<img src="${image}" alt="${nbEsc(title)}" class="nb-modal-image" />` : ''}
        <h2 class="nb-modal-title">${nbEsc(title)}</h2>
        ${desc ? `<p class="nb-modal-desc">${nbEsc(desc)}</p>` : ''}
        <div class="nb-modal-price">${nbEsc(price)}</div>
        ${spices > 0 ? `<div class="nb-modal-spices" title="Épices ${spices} sur 3"><span aria-hidden="true">${'🌶️'.repeat(spices)}</span></div>` : ''}
        ${alls ? `<div class="nb-modal-alls"><span class="nb-chip">${nbEsc(alls)}</span></div>` : ''}
      </div>
    `;
    document.body.appendChild(modal);

    // Animation d'ouverture
    setTimeout(() => modal.classList.add('is-open'), 10);

    // Fermeture
    const closeModal = () => {
      modal.classList.remove('is-open');
      setTimeout(() => {
        if(modal && modal.parentNode) modal.parentNode.removeChild(modal);
        modal = null;
      }, 300);
    };

    modal.addEventListener('click', (e) => {
      if(e.target === modal || e.target.classList.contains('nb-modal-close')) {
        closeModal();
      }
    });

    // Fermeture avec Échap
    document.addEventListener('keydown', function escHandler(e) {
      if(e.key === 'Escape') {
        closeModal();
        document.removeEventListener('keydown', escHandler);
      }
    });
  });

  // Hover to open/close (ouvre seulement celle survolée, ferme les autres)
  grid.addEventListener('mouseenter', (e)=>{
    const card = e.target.closest('.nb-card');
    if(!card) return;
    const item = card.closest('.nb-item');
    // Fermer tous les autres
    grid.querySelectorAll('.nb-item.is-open').forEach(el => {
      if(el !== item) el.classList.remove('is-open');
    });
    // Ouvrir celle-ci
    item.classList.add('is-open');
  }, true);

  grid.addEventListener('mouseleave', (e)=>{
    const card = e.target.closest('.nb-card');
    if(!card) return;
    const item = card.closest('.nb-item');
    // Fermer au départ du survol
    item.classList.remove('is-open');
  }, true);

  // Click to toggle (ouvre/ferme celle cliquée, ferme les autres)
  grid.addEventListener('click', (e)=>{
    const card = e.target.closest('.nb-card');
    if(!card) return;
    const item = card.closest('.nb-item');
    const open = item.classList.contains('is-open');

    // Fermer tous les autres d'abord
    grid.querySelectorAll('.nb-item.is-open').forEach(el => {
      if(el !== item) el.classList.remove('is-open');
    });

    // Toggle uniquement celui cliqué
    item.classList.toggle('is-open', !open);
  });

  // Accessibilité clavier (Enter/Espace) — avec fermeture des autres aussi
  grid.addEventListener('keydown', (e)=>{
    const card = e.target.closest('.nb-card');
    if(!card) return;
    if(e.key === 'Enter' || e.key === ' '){
      e.preventDefault();
      const item = card.closest('.nb-item');
      const open = item.classList.contains('is-open');
      grid.querySelectorAll('.nb-item.is-open').forEach(el => {
        if(el !== item) el.classList.remove('is-open');
      });
      item.classList.toggle('is-open', !open);
    }
  });
})();
