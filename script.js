// Mobile nav toggle
const toggle = document.querySelector('.nav-toggle');
toggle.addEventListener('click', () => {
  const open = document.body.classList.toggle('nav-open');
  toggle.setAttribute('aria-expanded', open);
});

// Close menu when a nav link is tapped (mobile)
document.querySelectorAll('.nav--right a').forEach(link => {
  link.addEventListener('click', () => {
    document.body.classList.remove('nav-open');
    toggle.setAttribute('aria-expanded', false);
  });
});

// Collections carousel arrows
const track = document.querySelector('.carousel__track');
if (track) {
  const step = () => track.querySelector('.slide').offsetWidth + 24;
  // RTL: "next" scrolls toward the left (negative), "prev" toward the right
  document.querySelector('.carousel__btn--next')
    .addEventListener('click', () => track.scrollBy({ left: -step(), behavior: 'smooth' }));
  document.querySelector('.carousel__btn--prev')
    .addEventListener('click', () => track.scrollBy({ left: step(), behavior: 'smooth' }));
}

// Subtle reveal on scroll
const io = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.style.opacity = 1; e.target.style.transform = 'none'; io.unobserve(e.target); }
  });
}, { threshold: 0.12 });

document.querySelectorAll('.card, .slide, .brides__item, .intro__text, .intro__img, .insta__item')
  .forEach(el => {
    el.style.opacity = 0;
    el.style.transform = 'translateY(24px)';
    el.style.transition = 'opacity .8s ease, transform .8s ease';
    io.observe(el);
  });
