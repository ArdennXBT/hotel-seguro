// Fade-in des sections au scroll
const sections = document.querySelectorAll('section');

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
    }
  });
});

sections.forEach(section => observer.observe(section));

// Fermeture du menu hamburger sur mobile
const burger = document.querySelector('.burger');
const nav = document.querySelector('.nav-menu');

if (burger) {
  burger.addEventListener('click', () => {
    nav.classList.toggle('open');
    burger.classList.toggle('active');
  });
}