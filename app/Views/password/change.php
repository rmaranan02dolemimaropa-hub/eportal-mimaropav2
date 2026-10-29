<?php
// Put this file at app/Views/password/change.php
$titles = [
    'first' => 'CREATE YOUR NEW PASSWORD',
    'expired' => 'YOUR PASSWORD HAS EXPIRED',
    'voluntary' => 'CHANGE PASSWORD',
];
$notes = [
    'first' => 'For your security, choose your own password before you continue.',
    'expired' => 'Your password is more than 3 months old. Create a new one to continue.',
    'voluntary' => 'Enter your current password, then choose a new one.',
];
$reason = isset($titles[$reason ?? '']) ? $reason : 'voluntary';
$email = $email ?? (session()->get('email') ?? '');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DOLE MIMAROPA E-Portal | Change Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>" />
    <style>
        /* Layout-only additions; colors/fonts still come from login.css */
        .pw-account {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 600;
            font-size: .95rem;
            margin-bottom: .75rem;
        }

        .pw-account i {
            font-size: 1.4rem;
        }

        .pw-heading {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0 0 .25rem;
        }

        .portal-subtitle {
            margin-bottom: 1.75rem;
        }

        /* Form is left-aligned even though the card header is centered */
        #pwForm {
            text-align: left;
        }

        #pwForm .form-label {
            display: block;
            text-align: left;
            font-weight: 600;
            font-size: .9rem;
            margin-bottom: .4rem;
        }

        #pwForm .form-text {
            text-align: left;
            margin-top: .4rem;
            font-size: .8rem;
        }

        /* Checkbox and its label sit together on the left */
        #pwForm .form-check {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: .6rem;
            padding-left: 0;
            margin: .25rem 0 0;
        }

        #pwForm .form-check-input {
            float: none;
            margin: 0;
            width: 1.1rem;
            height: 1.1rem;
            flex-shrink: 0;
            cursor: pointer;
        }

        #pwForm .form-check-label {
            margin: 0;
            text-align: left;
            font-size: .92rem;
            cursor: pointer;
        }

        .pw-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1.75rem;
        }

        .pw-actions a {
            font-weight: 600;
            font-size: .9rem;
            text-decoration: none;
        }

        .pw-actions a:hover {
            text-decoration: underline;
        }

        .pw-actions .pw-logout {
            background: none;
            border: 0;
            padding: 0;
            font: inherit;
            font-weight: 600;
            font-size: .9rem;
            color: inherit;
            cursor: pointer;
        }

        .pw-actions .pw-logout:hover {
            text-decoration: underline;
        }

        .pw-actions .login-btn {
            width: auto;
            padding-left: 1.75rem;
            padding-right: 1.75rem;
        }
    </style>
</head>

<body>
    <div class="bg-decoration" aria-hidden="true">
        <span class="blob blob-one"></span><span class="blob blob-two"></span><span class="blob blob-three"></span>
    </div>

    <main class="login-wrapper">
        <section class="login-card" aria-labelledby="pwTitle">
            <h1 id="pwTitle" class="portal-title"><?= esc($titles[$reason]) ?></h1>

            <?php if ($email !== ''): ?>
                <div class="pw-account">
                    <i class="bi bi-person-circle" aria-hidden="true"></i>
                    <span><?= esc($email) ?></span>
                </div>
            <?php endif; ?>

            <h2 class="pw-heading">Create a strong password</h2>
            <p class="portal-subtitle"><?= esc($notes[$reason]) ?></p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" role="alert"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <form id="pwForm" action="<?= base_url('password/update') ?>" method="post" autocomplete="off">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="current_password" class="form-label">Current password</label>
                    <input type="password" id="current_password" name="current_password"
                        class="form-control custom-input" autocomplete="current-password" required autofocus />
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">Create password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control custom-input"
                        autocomplete="new-password" minlength="8" maxlength="72" required />
                    <div class="form-text">At least 8 characters, with a letter and a number.</div>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                        class="form-control custom-input" autocomplete="new-password" minlength="8" maxlength="72"
                        required />
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="showPw" />
                    <label class="form-check-label" for="showPw">Show password</label>
                </div>

                <div class="pw-actions">
                    <?php if ($reason === 'voluntary'): ?>
                        <a href="<?= base_url('dashboard') ?>">Cancel</a>
                    <?php else: ?>
                        <button type="submit" formaction="<?= base_url('logout') ?>" formmethod="post" formnovalidate
                            class="pw-logout">Log out</button>
                    <?php endif; ?>
                    <button type="submit" class="btn login-btn">Save password</button>
                </div>
            </form>
        </section>
    </main>

    <script>
        document.getElementById('showPw').addEventListener('change', function () {
            ['current_password', 'new_password', 'confirm_password'].forEach(function (id) {
                document.getElementById(id).type = this.checked ? 'text' : 'password';
            }, this);
        });
    </script>
</body>

</html>