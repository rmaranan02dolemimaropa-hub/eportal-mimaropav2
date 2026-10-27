<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DOLE MIMAROPA E-Portal | Verify Your Identity</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
    rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>" />
</head>

<body>
  <div class="bg-decoration" aria-hidden="true">
    <span class="blob blob-one"></span><span class="blob blob-two"></span><span class="blob blob-three"></span>
  </div>

  <main class="login-wrapper">
    <section class="login-card" aria-labelledby="mfaTitle">
      <h1 id="mfaTitle" class="portal-title">TWO-STEP VERIFICATION</h1>
      <p class="portal-subtitle">Enter the code from your authenticator app</p>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

      <form action="<?= base_url('mfa/verify') ?>" method="post" autocomplete="off">
        <?= csrf_field() ?>
        <div class="mb-3">
          <label for="code" class="form-label">6-digit code</label>
          <input type="text" id="code" name="code" class="form-control custom-input text-center" inputmode="numeric"
            pattern="[0-9 ]{6,7}" maxlength="7" autocomplete="one-time-code" placeholder="123456" required autofocus />
        </div>
        <button type="submit" class="btn login-btn w-100">Verify</button>
      </form>

      <div class="text-center mt-3">
        <a href="<?= base_url('mfa/cancel') ?>" class="small">Cancel and return to login</a>
      </div>
      <p class="small text-muted text-center mt-3 mb-0">
        Lost your phone? Contact your IT Administrator to reset your two-step verification.
      </p>
    </section>
  </main>
</body>

</html>