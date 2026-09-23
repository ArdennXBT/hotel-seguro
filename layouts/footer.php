<!-- FOOTER -->
  <footer id="footer">
    <div class="container-fluid px-5">

      <!-- Logo centré -->
      <div class="footer-logo-area">
        <div class="footer-divider">
          <span></span><i></i><span class="right"></span>
        </div>
        <p class="footer-logo-name">SEGURO</p>
        <p class="footer-logo-tagline">La Sérénité · La Qualité · La Confiance</p>
        <div class="footer-divider" style="margin-top:16px;">
          <span></span><i></i><span class="right"></span>
        </div>
      </div>

      <!-- Colonnes footer -->
      <div class="row g-5 pb-5">

        <!-- Navigation -->
        <div class="col-lg-3 col-md-6">
          <div class="footer-nav-col">
            <h6>Navigation</h6>
            <ul>
              <li><a href="/acathon/index.php">Accueil</a></li>
              <li><a href="/acathon/pages/chambres.php">Chambres &amp; Suites</a></li>
              <li><a href="/acathon/pages/services.php">Services</a></li>
              <li><a href="/acathon/pages/reservation.php">Réservation</a></li>
            </ul>
          </div>
        </div>

        <!-- L'Hôtel -->
        <div class="col-lg-3 col-md-6">
          <div class="footer-nav-col">
            <h6>L'Hôtel SEGURO</h6>
            <ul>
              <li><a href="/acathon/pages/about.php">Notre Histoire</a></li>
              <li><a href="/acathon/pages/about.php#vision">Notre Vision</a></li>
              <li><a href="/acathon/pages/about.php#vision">La Promesse SEGURO</a></li>
              <li><a href="/acathon/pages/contact.php">Presse &amp; Partenariats</a></li>
            </ul>
          </div>
        </div>

        <!-- Contact -->
        <div class="col-lg-3 col-md-6">
          <div class="footer-nav-col">
            <h6>Contact</h6>
            <div class="footer-contact-text">
              <p>
                Hôtel SEGURO<br>
                Agbodrafo, entrée de Aného<br>
                Togo, Afrique de l'Ouest
              </p>
              <br>
              <p><a href="tel:+22800000000">+228 00 00 00 00</a></p>
              <p><a href="mailto:reservations@hotelseguro.com">reservations@hotelseguro.com</a></p>
              <p><a href="mailto:contact@hotelseguro.com">contact@hotelseguro.com</a></p>
            </div>
            <div class="footer-social mt-4">
              <a href="#" title="Instagram">IG</a>
              <a href="#" title="Facebook">FB</a>
              <a href="#" title="LinkedIn">LI</a>
            </div>
          </div>
        </div>

        <!-- Newsletter -->
        <div class="col-lg-3 col-md-6">
          <div class="footer-nav-col">
            <h6>Restez Informé</h6>
            <p class="footer-contact-text" style="margin-bottom:24px;">
              Offres exclusives, événements et instants de vie
              à l'Hôtel SEGURO — directement dans votre boîte mail.
            </p>
            <div class="footer-newsletter">
              <input type="email" placeholder="Votre adresse e-mail">
              <button>S'abonner</button>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Footer bottom -->
    <div class="footer-bottom">
      <p>&copy; 2025 Hôtel SEGURO · Agbodrafo, Togo · Tous droits réservés</p>
      <div class="footer-bottom-links">
        <a href="/acathon/pages/contact.php">Politique de confidentialité</a>
        <a href="/acathon/pages/contact.php">Mentions légales</a>
        <a href="/acathon/pages/contact.php">CGV</a>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // ══════════════════════════════════════════
    // HEADER ADAPTATIF
    // — Sur le hero  : transparent + texte blanc
    // — Après scroll : fond noir + texte blanc
    // ══════════════════════════════════════════
    const header = document.getElementById('header');
    const hero   = document.getElementById('hero');

    function updateHeader() {
      if (hero) {
        const threshold = hero.offsetHeight - 80;
        if (window.scrollY > threshold) {
          header.classList.add('scrolled');
        } else {
          header.classList.remove('scrolled');
        }
      } else {
        // Page sans hero → header scrolled dès le départ
        header.classList.add('scrolled');
      }
    }

    updateHeader();
    window.addEventListener('scroll', updateHeader);

    // ── Mobile menu ────────────────────────
    function toggleMenu() {
      document.getElementById('mobileMenu').classList.toggle('active');
      document.getElementById('hamburger').classList.toggle('open');
    }
    function closeMenu() {
      document.getElementById('mobileMenu').classList.remove('active');
      document.getElementById('hamburger').classList.remove('open');
    }
  </script>
</body>
</html>