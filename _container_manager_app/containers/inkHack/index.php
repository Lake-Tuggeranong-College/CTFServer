<?php include "template.php"; ?>

<style>
/* Decoration for home page */
.hero {
  background: linear-gradient(135deg,#4f9ccf 0%,#7bd389 100%);
  color: #fff;
  border-radius: .5rem;
  padding: 2rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 6px 18px rgba(15,23,42,0.08);
}
.hero .lead { color: rgba(255,255,255,0.95); }
.feature-card { transition: transform .15s ease, box-shadow .15s ease; }
.feature-card:hover { transform: translateY(-6px); box-shadow: 0 10px 30px rgba(2,6,23,0.08); }
.sample-creds code { background:#f8f9fa; padding:.15rem .4rem; border-radius:.25rem; }
.small-note { color:#6c757d; font-size:.95rem; }
</style>

<div class="container mt-4">
  <div class="row">
    <div class="col-12 col-md-8">
      <div class="hero">
        <h1 class="display-6 mb-2">Welcome to Secert Club</h1>
        <p class="lead mb-3">
          A simple portal to manage Member and VIP resources. Use the menu at the top-right to sign in or register.
        </p>

        <p class="mb-3">
          <a href="login.php" class="btn btn-light btn-sm me-2">Log In</a>
          <a href="register.php" class="btn btn-outline-light btn-sm">Create an account</a>
        </p>

        <div class="sample-creds mt-3">
          <strong>Try the demo accounts:</strong>
          <ul class="mb-0 mt-2">
            <li>VIP — Email: <code>VIP@VIP.com</code> | Password: <code>VIP</code></li>
            <li>Member — Email: <code>Member@Member.com</code> | Password: <code>Member</code></li>
          </ul>
        </div>
      </div>

      <h2 class="h5">Getting started</h2>
      <ol class="mb-3">
        <li>Click <strong>Log In</strong> (top-right) to access your account.</li>
        <li>Members: use <em>My Data</em> to view or update personal information.</li>
        <li>VIP: administrative links such as <em>Member List</em>, <em>Organisation</em>, and registration pages are available after login.</li>
        <li>Don't have an account? Click <strong>Registration</strong> to sign up.</li>
      </ol>

      <p class="small-note">If you're not sure where to begin, creating an account is a good first step. Explore the features and return here anytime for guidance.</p>
    </div>

    <div class="col-12 col-md-4">
      <div class="card feature-card mb-3">
        <div class="card-body">
          <h5 class="card-title">Quick Links</h5>
          <p class="card-text mb-2">Useful actions for new users.</p>
          <div class="d-grid gap-2">
            <a href="register.php" class="btn btn-primary btn-sm">Register</a>
            <a href="login.php" class="btn btn-outline-primary btn-sm">Log In</a>
            <a href="passwordreset.php" class="btn btn-outline-secondary btn-sm">Reset Password</a>
          </div>
        </div>
      </div>

      <div class="card feature-card">
        <div class="card-body">
          <h6 class="card-title">What you'll find</h6>
          <ul class="mb-0">
            <li>Manage personal Member data (Members)</li>
            <li>Administrative tools (VIP)</li>
            <li>Registration for new users</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <hr class="my-4">

  <div class="row g-3">
    <div class="col-12 col-md-4">
      <div class="card feature-card h-100">
        <div class="card-body">
          <h5 class="card-title">My Data</h5>
          <p class="card-text">Members can edit their personal details and medical information.</p>
          <!-- data-role-required="Member" ensures only Member role can navigate directly -->
          <a href="Member_data.php" class="btn btn-sm btn-outline-primary" data-role-required="Member">Go to My Data</a>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="card feature-card h-100">
        <div class="card-body">
          <h5 class="card-title">Member List</h5>
          <p class="card-text">VIP can view and manage the list of Members and related records.</p>
          <a href="Member_list.php" class="btn btn-sm btn-outline-primary" data-role-required="VIP">View Member List</a>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="card feature-card h-100">
        <div class="card-body">
          <h5 class="card-title">Administration</h5>
          <p class="card-text">Organisation settings and registration pages for VIP and Members.</p>
          <a href="VIP.php" class="btn btn-sm btn-outline-primary me-1" data-role-required="VIP">VIP</a>
          <!-- <a href="register_VIP.php" class="btn btn-sm btn-outline-secondary" data-role-required="VIP">Register VIP</a>
          <a href="register_Member.php" class="btn btn-sm btn-outline-secondary mt-2" data-role-required="VIP">Register Member</a> -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Authorization modal -->
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="authModalLabel">Access required</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="authModalBody">
        <!-- message set by JS -->
      </div>
      <div class="modal-footer">
        <a href="login.php" class="btn btn-primary">Log In</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
/* Pass server-side session role info to JS */
const user = <?= json_encode([
    'loggedIn' => isset($_SESSION['email_address']),
    'isMember' => !empty($_SESSION['isMember']) && $_SESSION['isMember'] == 1,
    'isVIP' => !empty($_SESSION['isVIP']) && $_SESSION['isVIP'] == 1,
]); ?>;

document.addEventListener('DOMContentLoaded', function () {
  const authModalEl = document.getElementById('authModal');
  const authModal = new bootstrap.Modal(authModalEl);
  const authModalBody = document.getElementById('authModalBody');

  document.querySelectorAll('a[data-role-required]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      const required = this.getAttribute('data-role-required');

      if (required === 'Member') {
        if (!user.isMember) {
          e.preventDefault();
          authModalBody.textContent = 'Please log in as a Member first.';
          authModal.show();
        }
      } else if (required === 'VIP') {
        if (!user.isVIP) {
          e.preventDefault();
          authModalBody.textContent = 'Please log in as VIP first.';
          authModal.show();
        }
      }
      // If user has the role, link works normally.
    });
  });
});
</script>

</body>
</html>