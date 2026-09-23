<?php include("./layouts/header.php") ?>

  <!-- ══════════════════════════════════════════
       HERO — Vidéo plage, style Singita
  ══════════════════════════════════════════ -->
  <section id="hero">
    <video class="hero-video" autoplay muted loop playsinline poster="">
      <source src="./assets/video/video.mp4" type="video/mp4">
      <div class="hero-video-fallback"></div>
    </video>

    <div class="hero-overlay"></div>

    <!-- Coins décoratifs dorés -->
    <div class="hero-corner tl">
      <svg width="50" height="50" viewBox="0 0 50 50" fill="none">
        <path d="M0 50 L0 0 L50 0" stroke="#c9a84c" stroke-width="1" fill="none"/>
        <rect x="3" y="3" width="10" height="10" stroke="#c9a84c" stroke-width="0.5" fill="none"/>
      </svg>
    </div>
    <div class="hero-corner tr">
      <svg width="50" height="50" viewBox="0 0 50 50" fill="none">
        <path d="M0 50 L0 0 L50 0" stroke="#c9a84c" stroke-width="1" fill="none"/>
        <rect x="3" y="3" width="10" height="10" stroke="#c9a84c" stroke-width="0.5" fill="none"/>
      </svg>
    </div>
    <div class="hero-corner bl">
      <svg width="50" height="50" viewBox="0 0 50 50" fill="none">
        <path d="M0 50 L0 0 L50 0" stroke="#c9a84c" stroke-width="1" fill="none"/>
        <rect x="3" y="3" width="10" height="10" stroke="#c9a84c" stroke-width="0.5" fill="none"/>
      </svg>
    </div>
    <div class="hero-corner br">
      <svg width="50" height="50" viewBox="0 0 50 50" fill="none">
        <path d="M0 50 L0 0 L50 0" stroke="#c9a84c" stroke-width="1" fill="none"/>
        <rect x="3" y="3" width="10" height="10" stroke="#c9a84c" stroke-width="0.5" fill="none"/>
      </svg>
    </div>

    <div class="hero-content">
      <div class="hero-ornament">
        <span class="hero-orn-line"></span>
        <span class="hero-orn-diamond"></span>
        <span class="hero-orn-line right"></span>
      </div>

      <h1 class="hero-title">Hôtel<br><em>Seguro</em></h1>

      <p class="hero-subtitle">
        La nature est là. Le calme est là.<br>Rien n'a été sacrifié sur l'autel du confort.
      </p>

      <div class="hero-cta-wrap">
        <a href="pages/reservation-system.php" class="cta-primary">Réserver un séjour</a>
        <a href="chambres.php" class="cta-secondary">Découvrir les chambres</a>
      </div>
    </div>

    <div class="scroll-indicator">
      <span class="scroll-text">Découvrir</span>
      <span class="scroll-line"></span>
    </div>
  </section>

  <!-- ══════════════════════════════════════════
       PRÉSENTATION — L'expérience Seguro
  ══════════════════════════════════════════ -->
  <section style="padding: 120px 60px; background: var(--blanc);">
    <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
      <div style="margin-bottom: 32px;">
        <span style="display: inline-block; width: 60px; height: 1px; background: var(--or); vertical-align: middle;"></span>
        <span style="display: inline-block; width: 8px; height: 8px; background: var(--or); transform: rotate(45deg); margin: 0 16px; vertical-align: middle;"></span>
        <span style="display: inline-block; width: 60px; height: 1px; background: var(--or); vertical-align: middle;"></span>
      </div>
      
      <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2.8rem; color: var(--vert); margin-bottom: 24px; font-weight: 300;">
        Une parenthèse de sérénité
      </h2>
      
      <p style="font-size: 1.1rem; color: #555; line-height: 1.8; max-width: 700px; margin: 0 auto 48px;">
        Niché sur les rives paisibles d'Agbodrafo, l'Hôtel Seguro vous invite à découvrir 
        l'harmonie parfaite entre nature préservée et confort raffiné. Notre établissement 
        de charme vous offre un refuge hors du temps où chaque détail a été pensé pour 
        sublimer votre séjour au Togo.
      </p>
      
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 48px; margin-top: 64px;">
        <div style="text-align: center;">
          <div style="width: 60px; height: 60px; margin: 0 auto 20px; border: 1px solid var(--or); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">🌿</div>
          <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.3rem; color: var(--vert); margin-bottom: 12px;">Nature Préservée</h3>
          <p style="font-size: 0.95rem; color: #666; line-height: 1.6;">Un cadre verdoyant à quelques pas de l'océan Atlantique</p>
        </div>
        <div style="text-align: center;">
          <div style="width: 60px; height: 60px; margin: 0 auto 20px; border: 1px solid var(--or); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">✨</div>
          <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.3rem; color: var(--vert); margin-bottom: 12px;">Luxueux & Authentique</h3>
          <p style="font-size: 0.95rem; color: #666; line-height: 1.6;">Des espaces raffinés célébrant l'art de vivre togolais</p>
        </div>
        <div style="text-align: center;">
          <div style="width: 60px; height: 60px; margin: 0 auto 20px; border: 1px solid var(--or); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">🍽️</div>
          <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.3rem; color: var(--vert); margin-bottom: 12px;">Gastronomie Locale</h3>
          <p style="font-size: 0.95rem; color: #666; line-height: 1.6;">Une cuisine fusion entre saveurs africaines et techniques modernes</p>
        </div>
        <div style="text-align: center;">
          <div style="width: 60px; height: 60px; margin: 0 auto 20px; border: 1px solid var(--or); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">🌅</div>
          <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.3rem; color: var(--vert); margin-bottom: 12px;">Couchers de Soleil</h3>
          <p style="font-size: 0.95rem; color: #666; line-height: 1.6;">Des vues imprenables sur l'océan depuis votre terrasse privée</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ══════════════════════════════════════════
       CHAMBRES EN VEDETTE
  ══════════════════════════════════════════ -->
  <section style="padding: 120px 60px; background: linear-gradient(to bottom, #f8f6f1, var(--blanc));">
    <div style="max-width: 1200px; margin: 0 auto;">
      <div style="text-align: center; margin-bottom: 64px;">
        <span style="font-family: 'Jost', sans-serif; font-size: 0.75rem; letter-spacing: 0.3em; text-transform: uppercase; color: var(--or);">Nos Hébergements</span>
        <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2.4rem; color: var(--vert); margin-top: 16px; font-weight: 300;">
          Des espaces pensés pour votre confort
        </h2>
      </div>
      
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 32px;">
        <!-- Suite Royale -->
        <div style="background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
          <div style="height: 280px; overflow: hidden;">
            <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?w=600&h=400&fit=crop" 
                 alt="Suite Royale - Vue océan" 
                 style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
          </div>
          <div style="padding: 32px;">
            <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: var(--vert); margin-bottom: 8px;">Suite Royale</h3>
            <p style="font-size: 0.9rem; color: #888; margin-bottom: 16px;">80 m² • Vue océan • Jacuzzi privé</p>
            <p style="font-size: 0.95rem; color: #555; line-height: 1.6; margin-bottom: 24px;">Notre suite d'exception avec terrasse panoramique, jacuzzi extérieur et service de majordome.</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.3rem; color: var(--or); font-weight: 600;">185 000 FCFA<span style="font-size: 0.8rem; color: #888; font-weight: 300;">/nuit</span></span>
              <a href="pages/chambres.php" style="font-size: 0.8rem; color: var(--vert); text-decoration: none; border-bottom: 1px solid var(--or); padding-bottom: 2px;">Découvrir →</a>
            </div>
          </div>
        </div>
        
        <!-- Villa Privée -->
        <div style="background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
          <div style="height: 280px; overflow: hidden;">
            <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600&h=400&fit=crop" 
                 alt="Villa Privée - Piscine privée" 
                 style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
          </div>
          <div style="padding: 32px;">
            <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: var(--vert); margin-bottom: 8px;">Villa Privée</h3>
            <p style="font-size: 0.9rem; color: #888; margin-bottom: 16px;">120 m² • Jardin privatif • Piscine</p>
            <p style="font-size: 0.95rem; color: #555; line-height: 1.6; margin-bottom: 24px;">Une villa indépendante avec piscine privée, jardin tropical et espace de réception pour 4 personnes.</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.3rem; color: var(--or); font-weight: 600;">320 000 FCFA<span style="font-size: 0.8rem; color: #888; font-weight: 300;">/nuit</span></span>
              <a href="pages/chambres.php" style="font-size: 0.8rem; color: var(--vert); text-decoration: none; border-bottom: 1px solid var(--or); padding-bottom: 2px;">Découvrir →</a>
            </div>
          </div>
        </div>
        
        <!-- Chambre Supérieure -->
        <div style="background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
          <div style="height: 280px; overflow: hidden;">
            <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=600&h=400&fit=crop" 
                 alt="Chambre Supérieure - Vue jardin" 
                 style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
          </div>
          <div style="padding: 32px;">
            <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: var(--vert); margin-bottom: 8px;">Chambre Supérieure</h3>
            <p style="font-size: 0.9rem; color: #888; margin-bottom: 16px;">40 m² • Vue jardin • Terrasse</p>
            <p style="font-size: 0.95rem; color: #555; line-height: 1.6; margin-bottom: 24px;">Confort élégant avec terrasse privée donnant sur notre jardin tropical et accès direct à la piscine.</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.3rem; color: var(--or); font-weight: 600;">85 000 FCFA<span style="font-size: 0.8rem; color: #888; font-weight: 300;">/nuit</span></span>
              <a href="pages/chambres.php" style="font-size: 0.8rem; color: var(--vert); text-decoration: none; border-bottom: 1px solid var(--or); padding-bottom: 2px;">Découvrir →</a>
            </div>
          </div>
        </div>
      </div>
      
      <div style="text-align: center; margin-top: 48px;">
        <a href="pages/chambres.php" class="cta-primary" style="display: inline-block; background: var(--vert); color: var(--or-pale); padding: 16px 40px; text-decoration: none; font-size: 0.75rem; letter-spacing: 0.2em; text-transform: uppercase; border-radius: 2px;">Voir toutes les chambres</a>
      </div>
    </div>
  </section>

  <!-- ══════════════════════════════════════════
       EXPÉRIENCES
  ══════════════════════════════════════════ -->
  <section style="padding: 120px 60px; background: var(--vert); color: var(--blanc);">
    <div style="max-width: 1200px; margin: 0 auto;">
      <div style="text-align: center; margin-bottom: 64px;">
        <span style="font-family: 'Jost', sans-serif; font-size: 0.75rem; letter-spacing: 0.3em; text-transform: uppercase; color: var(--or);">Expériences</span>
        <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2.4rem; margin-top: 16px; font-weight: 300;">S'évader, se ressourcer, se découvrir</h2>
      </div>
      
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 32px;">
        <div style="border: 1px solid rgba(201,168,76,0.3); padding: 40px; border-radius: 4px;">
          <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.4rem; color: var(--or); margin-bottom: 16px;">🧘 Détente & Bien-être</h3>
          <p style="font-size: 0.95rem; line-height: 1.7; color: rgba(250,248,243,0.8);">Spa ouvert sur la nature, massages traditionnels aux huiles locales, yoga au lever du soleil sur la plage.</p>
        </div>
        <div style="border: 1px solid rgba(201,168,76,0.3); padding: 40px; border-radius: 4px;">
          <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.4rem; color: var(--or); margin-bottom: 16px;">🍹 Gastronomie & Mixologie</h3>
          <p style="font-size: 0.95rem; line-height: 1.7; color: rgba(250,248,243,0.8);">Restaurant gastronomique, bar à cocktails avec vue océan, dégustations de vins et accords mets-vins.</p>
        </div>
        <div style="border: 1px solid rgba(201,168,76,0.3); padding: 40px; border-radius: 4px;">
          <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.4rem; color: var(--or); margin-bottom: 16px;">🏄 Activités Nautiques</h3>
          <p style="font-size: 0.95rem; line-height: 1.7; color: rgba(250,248,243,0.8);">Plongée sous-marine, pêche sportive, excursions en bateau traditionnel, paddle et kayak dans la mangrove.</p>
        </div>
        <div style="border: 1px solid rgba(201,168,76,0.3); padding: 40px; border-radius: 4px;">
          <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.4rem; color: var(--or); margin-bottom: 16px;">🎭 Culture & Découverte</h3>
          <p style="font-size: 0.95rem; line-height: 1.7; color: rgba(250,248,243,0.8);">Visites des villages voisins, ateliers de tissage traditionnel, marchés locaux et rencontres avec les artisans.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ══════════════════════════════════════════
       TÉMOIGNAGES
  ══════════════════════════════════════════ -->
  <section style="padding: 120px 60px; background: var(--blanc);">
    <div style="max-width: 900px; margin: 0 auto; text-align: center;">
      <div style="margin-bottom: 32px;">
        <span style="display: inline-block; width: 40px; height: 1px; background: var(--or); vertical-align: middle;"></span>
        <span style="display: inline-block; font-size: 1.5rem; margin: 0 16px; vertical-align: middle;">❝</span>
        <span style="display: inline-block; width: 40px; height: 1px; background: var(--or); vertical-align: middle;"></span>
      </div>
      
      <blockquote style="font-family: 'Cormorant Garamond', serif; font-size: 1.6rem; color: var(--vert); font-style: italic; line-height: 1.6; margin-bottom: 32px;">
        "Un havre de paix où le temps semble s'arrêter. L'accueil chaleureux, les paysages à couper le souffle et le raffinement des lieux ont fait de notre séjour une expérience inoubliable."
      </blockquote>
      
      <div style="font-size: 0.9rem; color: #666;">
        <strong style="color: var(--vert); font-weight: 400;">Marie & Jean-Pierre L.</strong> — Paris, France
      </div>
      <div style="margin-top: 8px; color: var(--or); font-size: 1.2rem;">★★★★★</div>
    </div>
  </section>

  <!-- ══════════════════════════════════════════
       CALL TO ACTION
  ══════════════════════════════════════════ -->
  <section style="padding: 100px 60px; background: linear-gradient(135deg, #2d5c40 0%, #1a3a2a 100%); text-align: center;">
    <div style="max-width: 600px; margin: 0 auto;">
      <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2.2rem; color: var(--or-pale); margin-bottom: 24px; font-weight: 300;">Réservez votre évasion</h2>
      <p style="font-size: 1rem; color: rgba(250,248,243,0.8); margin-bottom: 40px; line-height: 1.7;">Profitez de nos offres spéciales pour les séjours de 3 nuits ou plus. Petit-déjeuner gastronomique inclus.</p>
      <a href="pages/reservation-system.php" style="display: inline-block; background: var(--or); color: var(--vert); padding: 18px 48px; text-decoration: none; font-size: 0.75rem; letter-spacing: 0.2em; text-transform: uppercase; border-radius: 2px; font-weight: 400;">Réserver maintenant</a>
    </div>
  </section>

<?php include("./layouts/footer.php") ?>