// ===== Burger mobile =====
(function(){
  const nav   = document.querySelector('.nb-nav');
  const btn   = document.querySelector('.nb-nav-toggle');
  const links = document.querySelectorAll('.nb-menu a');
  if(!nav || !btn) return;

  btn.addEventListener('click', () => {
    const open = nav.classList.toggle('is-open');
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });

  // Ferme le menu après clic sur un lien
  links.forEach(a => a.addEventListener('click', () => {
    nav.classList.remove('is-open');
    btn.setAttribute('aria-expanded','false');
  }));
})();

// ===== Scroll spy: surbrillance des liens =====
(function(){
  const links = Array.from(document.querySelectorAll('.nb-menu a[href^="#"]'));
  if(!links.length) return;

  const ids = links.map(a => a.getAttribute('href').slice(1)).filter(Boolean);
  const sections = ids.map(id => document.getElementById(id)).filter(Boolean);
  const byId = Object.fromEntries(links.map(a => [a.getAttribute('href').slice(1), a]));

  const io = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      const id = entry.target.id;
      const link = byId[id];
      if(!link) return;
      if(entry.isIntersecting){
        links.forEach(l => l.classList.remove('is-active'));
        link.classList.add('is-active');
      }
    });
  }, { rootMargin: "-40% 0px -55% 0px", threshold: 0 });

  sections.forEach(sec => io.observe(sec));
})();
