// ===== Utilitaire échappement HTML =====
function nbEsc(s){
  return String(s)
    .replaceAll('&','&amp;')
    .replaceAll('<','<')
    .replaceAll('>','>')
    .replaceAll('"','"')
    .replaceAll("'",'&#039;');
}

// ===== Accordéon FAQ (fermeture auto des autres) =====
document.addEventListener('click', (e)=>{
  const t = e.target;
  if(t.matches('.faq summary')){
    document.querySelectorAll('.faq details[open]').forEach(d=>{
      if(d!==t.parentElement) d.removeAttribute('open');
    });
  }
});
