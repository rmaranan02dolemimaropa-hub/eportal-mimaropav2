<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DOLE MIMAROPA E-Portal | Set Up Two-Step Verification</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
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
      <h1 id="mfaTitle" class="portal-title">SET UP TWO-STEP VERIFICATION</h1>
      <p class="portal-subtitle">Required for all accounts</p>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

      <ol class="small text-start ps-3 mb-3">
        <li>Install an authenticator app (Google Authenticator, Microsoft Authenticator, Authy…).</li>
        <li>Scan the QR code, or enter the key manually.</li>
        <li>Type the 6-digit code the app shows to finish.</li>
      </ol>

      <div class="text-center mb-3">
        <div class="d-inline-block bg-white p-2 border rounded"><?= $qrSvg /* generated SVG, not user input */ ?></div>
        <div class="small text-muted mt-2">Can't scan? Enter this key:</div>
        <code class="user-select-all"><?= esc($secret) ?></code>
      </div>

      <form action="<?= base_url('mfa/setup') ?>" method="post" autocomplete="off">
        <?= csrf_field() ?>
        <div class="mb-3">
          <label for="code" class="form-label">6-digit code</label>
          <input type="text" id="code" name="code" class="form-control custom-input text-center" inputmode="numeric"
            pattern="\d{6}" maxlength="6" autocomplete="one-time-code" placeholder="123456" required autofocus />
        </div>
        <button type="submit" class="btn login-btn w-100">Verify &amp; Continue</button>
      </form>

      <div class="text-center mt-3">
        <a href="<?= base_url('mfa/cancel') ?>" class="small">Cancel and return to login</a>
      </div>
    </section>
  </main>
</body>

</html>
