<?php
echo "Hello World";
echo "Welcome to Secert Club";
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Secert Club — Join the Club</title>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <div class="brand">
        <div class="logo" aria-hidden="true">SC</div>
        <span class="brand-name">Secert Club</span>
      </div>
      <nav class="nav">
        <a href="#" class="nav-link">Home</a>
        <a href="#" class="nav-link">About</a>
        <a href="#" class="nav-link">Membership</a>
        <button id="signupBtn" class="btn btn-primary">Join</button>
      </nav>
      <button id="menuToggle" class="menu-toggle" aria-label="Open menu">☰</button>
    </div>
  </header>

  <main>
    <section class="hero">
      <div class="container hero-inner">
        <h1>Welcome to Secert Club</h1>
        <p class="lead">An exclusive place for creators, thinkers, and doers. Private events, curated resources, and a community that cares.</p>
        <div class="hero-ctas">
          <button id="heroJoin" class="btn btn-primary">Get an invite</button>
          <a href="#" class="btn btn-ghost">Learn more</a>
        </div>
      </div>
    </section>

    <section class="features container">
      <div class="feature">
        <div class="icon">★</div>
        <h3>Exclusive Events</h3>
        <p>Member-only gatherings, talks, and workshops—both online and offline.</p>
      </div>
      <div class="feature">
        <div class="icon">🔒</div>
        <h3>Private Community</h3>
        <p>A safe, moderated space for deep conversations and serendipity.</p>
      </div>
      <div class="feature">
        <div class="icon">⚡</div>
        <h3>Curated Resources</h3>
        <p>Tools, discounts, and reading lists chosen by the community.</p>
      </div>
    </section>

    <section class="cta-strip">
      <div class="container cta-inner">
        <div>
          <h2>Ready to join Secert Club?</h2>
          <p>Apply for membership and start connecting.</p>
        </div>
        <div>
          <button id="ctaJoin" class="btn btn-accent">Apply now</button>
        </div>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="container">
      <p>&copy; <span id="year"></span> Secert Club — All rights reserved.</p>
    </div>
  </footer>

  <!-- Modal -->
  <div id="modal" class="modal" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal-panel" role="document">
      <button id="closeModal" class="modal-close" aria-label="Close">×</button>
      <h2>Request an invite</h2>
      <p>Enter your email and we'll be in touch.</p>
      <form id="inviteForm">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required placeholder="you@example.com">
        <button type="submit" class="btn btn-primary">Send request</button>
      </form>
      <p class="muted">We’ll never share your email.</p>
    </div>
  </div>

  <script>
    // tiny JS for modal + year
    const yearEl = document.getElementById('year');
    yearEl.textContent = new Date().getFullYear();

    const modal = document.getElementById('modal');
    const openBtns = [document.getElementById('signupBtn'), document.getElementById('heroJoin'), document.getElementById('ctaJoin')];
    const closeBtn = document.getElementById('closeModal');
    const menuToggle = document.getElementById('menuToggle');
    const nav = document.querySelector('.nav');

    openBtns.forEach(b => b && b.addEventListener('click', (e) => {
      e.preventDefault();
      modal.style.display = 'block';
      modal.setAttribute('aria-hidden','false');
      document.getElementById('email').focus();
    }));

    closeBtn.addEventListener('click', () => {
      modal.style.display = 'none';
      modal.setAttribute('aria-hidden','true');
    });

    window.addEventListener('click', (e) => {
      if (e.target === modal) { closeBtn.click(); }
    });

    menuToggle.addEventListener('click', () => {
      nav.classList.toggle('open');
    });

    document.getElementById('inviteForm').addEventListener('submit', (e) => {
      e.preventDefault();
      alert('Thanks! We received your request.');
      closeBtn.click();
      e.target.reset();
    });
  </script>
</body>
</html>