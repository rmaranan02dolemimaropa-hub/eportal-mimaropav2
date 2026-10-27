<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DOLE MIMAROPA E-Portal | Login</title>

  <!-- Google Fonts: Manrope -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
    rel="stylesheet" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />

  <!-- Custom Stylesheet -->
  <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>" />
</head>

<body>

  <!-- Decorative background shapes (purely visual, hidden from assistive tech) -->
  <div class="bg-decoration" aria-hidden="true">
    <span class="blob blob-one"></span>
    <span class="blob blob-two"></span>
    <span class="blob blob-three"></span>
  </div>

  <!-- Main login section -->
  <main class="login-wrapper">
    <section class="login-card" aria-labelledby="portalTitle">

      <!-- Logo -->
      <div class="logo-circle">
        <img src="<?= base_url('assets/img/dole_blogo.png') ?>" alt="DOLE MIMAROPA Logo">
      </div>

      <!-- Titles -->
      <h1 id="portalTitle" class="portal-title">DOLE MIMAROPA E-PORTAL</h1>
      <p class="portal-subtitle">Employee &amp; Client Login</p>

      <?php if (session()->getFlashdata('error')): ?>

        <div class="alert alert-danger alert-dismissible fade show shadow-sm" id="loginAlert" role="alert">

          <i class="bi bi-exclamation-circle-fill me-2"></i>

          <?= esc(session()->getFlashdata('error')) ?>

          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          </button>

        </div>

      <?php endif; ?>

      <!-- Login form -->
      <form class="login-form" action="<?= base_url('login/login_process') ?>" method="post" novalidate>
        <?= csrf_field() ?>

        <!-- Username -->
        <div class="mb-3 input-group-custom">
          <label for="username" class="form-label">Username</label>
          <div class="input-icon-wrap">
            <i class="bi bi-person input-icon"></i>
            <input type="text" class="form-control custom-input" id="username" name="username"
              placeholder="Enter your username" autocomplete="username" required />
          </div>
        </div>

        <!-- Password -->
        <div class="mb-3 input-group-custom">
          <label for="password" class="form-label">
            Password <span class="text-danger">*</span>
          </label>

          <div class="input-icon-wrap">
            <i class="bi bi-lock input-icon"></i>

            <input type="password" class="form-control custom-input custom-input-with-toggle" id="password"
              name="password" placeholder="Enter your password" minlength="8"
              pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$"
              title="Minimum 8 characters with uppercase, lowercase, number, and special character."
              autocomplete="current-password" required>

            <!-- Show / Hide Password -->
            <button type="button" class="password-toggle-btn" id="passwordToggle" aria-label="Show password"
              aria-pressed="false" aria-controls="password">

              <i class="bi bi-eye" id="passwordToggleIcon"></i>
            </button>
          </div>

          <small class="text-muted">
            Minimum 8 characters with uppercase, lowercase, number and special character.
          </small>
        </div>

        <!-- Remember me -->
        <div class="d-flex justify-content-between align-items-center mb-4 form-utility-row">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe" />
            <label class="form-check-label" for="rememberMe">
              Remember me
            </label>
          </div>
        </div>

        <!-- Submit button -->
        <button type="submit" class="btn login-btn w-100">
          Log In
          <i class="bi bi-arrow-right-short"></i>
        </button>

      </form>

      <!-- Footer -->
      <footer class="portal-footer">
        &copy; 2026 Department of Labor and Employment - MIMAROPA Region
      </footer>

    </section>
  </main>

  <script>
    // Show / hide password
    (function () {
      const passwordInput = document.getElementById('password');
      const toggleButton = document.getElementById('passwordToggle');
      const toggleIcon = document.getElementById('passwordToggleIcon');

      toggleButton.addEventListener('click', function () {
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        toggleIcon.classList.toggle('bi-eye', !isHidden);
        toggleIcon.classList.toggle('bi-eye-slash', isHidden);
        this.setAttribute('aria-pressed', isHidden);
      });
    })();

    // Auto-dismiss alerts
    document.addEventListener('DOMContentLoaded', function () {
      ['loginAlert', 'loginSuccess'].forEach(function (id) {
        const alert = document.getElementById(id);
        if (!alert) return;

        setTimeout(function () {
          alert.style.transition = 'opacity .5s';
          alert.style.opacity = '0';
          setTimeout(function () { alert.remove(); }, 500);
        }, 5000);
      });
    });
  </script>

</body>

</html>