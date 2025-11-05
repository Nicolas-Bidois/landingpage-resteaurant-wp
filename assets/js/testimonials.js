// ===== SLIDER DES AVIS (testimonials) =====
(function(){
  const slider = document.querySelector('.testimonials-slider');
  if(!slider) return;

  const testimonials = slider.querySelectorAll('.testimonial');
  const dots = slider.querySelectorAll('.dot');
  const prevBtn = slider.querySelector('.testimonial-prev');
  const nextBtn = slider.querySelector('.testimonial-next');

  if(!testimonials.length) return;

  let currentIndex = 0;

  function showTestimonial(index) {
    testimonials.forEach((t, i) => {
      t.classList.toggle('active', i === index);
    });
    dots.forEach((d, i) => {
      d.classList.toggle('active', i === index);
    });
    currentIndex = index;
  }

  function nextTestimonial() {
    const nextIndex = (currentIndex + 1) % testimonials.length;
    showTestimonial(nextIndex);
  }

  function prevTestimonial() {
    const prevIndex = (currentIndex - 1 + testimonials.length) % testimonials.length;
    showTestimonial(prevIndex);
  }

  // Événements
  if(nextBtn) nextBtn.addEventListener('click', nextTestimonial);
  if(prevBtn) prevBtn.addEventListener('click', prevTestimonial);

  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => showTestimonial(index));
  });

  // Auto-slide toutes les 5 secondes
  setInterval(nextTestimonial, 5000);

  // Initialisation
  showTestimonial(0);
})();
