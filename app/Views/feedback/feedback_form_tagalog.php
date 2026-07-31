<!DOCTYPE html>
<html lang="fil">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Satisfaction Measurement (CSM) &ndash; Tagalog | DOLE MIMAROPA</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --dole-blue: #10058C;
      --dole-blue-light: #2a1fb0;
      --ink: #1c1d21;
      --muted: #6b6f7b;
      --line: #dfe1e8;
      --bg: #eef0f6;
      --card: #ffffff;
      --danger: #c0392b;
      --radius: 14px;
    }

    * {
      box-sizing: border-box;
    }

    html {
      -webkit-text-size-adjust: 100%;
    }

    body {
      margin: 0;
      font-family: "Manrope", "Segoe UI", "Helvetica Neue", Arial, sans-serif;
      background: var(--bg);
      color: var(--ink);
      line-height: 1.5;
      padding: clamp(16px, 5vw, 32px) clamp(12px, 4vw, 16px) 64px;
    }

    .page-wrap {
      max-width: 880px;
      margin: 0 auto;
      width: 100%;
    }

    img,
    svg {
      max-width: 100%;
    }

    /* ===== HEADER ===== */
    .header-card {
      background: linear-gradient(135deg, var(--dole-blue), var(--dole-blue-light));
      border-radius: var(--radius);
      padding: clamp(18px, 4vw, 26px) clamp(18px, 5vw, 30px);
      color: #fff;
      margin-bottom: 18px;
      box-shadow: 0 8px 24px rgba(16, 5, 140, .18);
    }

    .header-top {
      display: flex;
      align-items: center;
      gap: 14px;
      flex-wrap: wrap;
    }

    .logo-badge {
      width: 52px;
      height: 52px;
      background: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-weight: 800;
      color: var(--dole-blue);
      font-size: .72rem;
      text-align: center;
      line-height: 1.1;
    }

    .agency-line {
      text-transform: uppercase;
      letter-spacing: .08em;
      font-size: .7rem;
      opacity: .85;
      font-weight: 600;
    }

    .header-title {
      margin: 2px 0 0;
      font-size: clamp(1.15rem, 4vw, 1.5rem);
      font-weight: 700;
      line-height: 1.25;
    }

    .header-subtitle {
      margin: 14px 0 0;
      font-size: .86rem;
      opacity: .92;
      max-width: 640px;
    }

    .control-row {
      display: flex;
      justify-content: flex-start;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 18px;
      font-size: .78rem;
    }

    .control-row span.tag {
      background: rgba(255, 255, 255, .15);
      padding: 5px 11px;
      border-radius: 999px;
    }

    /* ===== CARD ===== */
    .form-card {
      background: var(--card);
      border-radius: var(--radius);
      padding: clamp(18px, 4vw, 28px) clamp(16px, 4vw, 30px) clamp(22px, 4vw, 34px);
      margin-bottom: 18px;
      box-shadow: 0 2px 10px rgba(20, 20, 43, .06);
      border: 1px solid var(--line);
    }

    .form-card+.form-card {
      margin-top: 18px;
    }

    .card-label {
      font-size: .68rem;
      font-weight: 700;
      letter-spacing: .09em;
      text-transform: uppercase;
      color: var(--dole-blue);
      margin: 0 0 4px;
    }

    .card-desc {
      font-size: .85rem;
      color: var(--muted);
      margin: 0 0 20px;
    }

    .required-mark {
      color: var(--danger);
    }

    /* office-use box */
    .office-use {
      border: 1px dashed #b9bdd6;
      border-radius: 10px;
      padding: 14px 16px;
      background: #f7f7fc;
      font-size: .85rem;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px 20px;
    }

    .office-use .full {
      grid-column: 1 / -1;
    }

    .office-use label {
      display: block;
      font-size: .68rem;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: var(--muted);
      margin-bottom: 4px;
      font-weight: 600;
    }

    .office-use input {
      width: 100%;
      border: none;
      border-bottom: 1px solid #c7cadd;
      background: transparent;
      padding: 6px 2px;
      font-size: 16px;
      color: var(--ink);
    }

    .office-use input:focus {
      outline: none;
      border-bottom-color: var(--dole-blue);
    }

    /* ===== GRID / FIELDS ===== */
    .row {
      display: grid;
      grid-template-columns: repeat(12, 1fr);
      gap: 16px;
      margin-bottom: 18px;
    }

    .row:last-child {
      margin-bottom: 0;
    }

    .col-3 {
      grid-column: span 3;
    }

    .col-4 {
      grid-column: span 4;
    }

    .col-6 {
      grid-column: span 6;
    }

    .col-8 {
      grid-column: span 8;
    }

    .col-12 {
      grid-column: span 12;
    }

    @media (max-width:640px) {

      .col-3,
      .col-4,
      .col-6,
      .col-8 {
        grid-column: span 12;
      }

      .office-use {
        grid-template-columns: 1fr;
      }

      .row {
        gap: 14px;
      }
    }

    .field label {
      display: block;
      font-size: .78rem;
      font-weight: 600;
      color: var(--ink);
      margin-bottom: 6px;
    }

    .field input[type="text"],
    .field input[type="number"],
    .field input[type="email"],
    .field input[type="date"],
    .field textarea {
      width: 100%;
      border: 1px solid var(--line);
      border-radius: 10px;
      padding: 11px 13px;
      font-size: 16px;
      font-family: inherit;
      color: var(--ink);
      background: #fbfbfd;
      transition: border-color .15s, box-shadow .15s;
    }

    .field input:focus,
    .field textarea:focus {
      outline: none;
      border-color: var(--dole-blue);
      box-shadow: 0 0 0 3px rgba(16, 5, 140, .1);
      background: #fff;
    }

    .field .hint {
      font-size: .76rem;
      color: var(--muted);
      margin-top: 5px;
    }

    /* ===== PILL / CHOICE GROUPS (client type, sex) ===== */
    .choice-group {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .choice-pill {
      position: relative;
    }

    .choice-pill input {
      position: absolute;
      opacity: 0;
      width: 0;
      height: 0;
    }

    .choice-pill label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 9px 16px;
      border: 1.5px solid var(--line);
      border-radius: 999px;
      font-size: .86rem;
      font-weight: 500;
      cursor: pointer;
      background: #fbfbfd;
      transition: all .15s;
      user-select: none;
      margin: 0;
    }

    .choice-pill input:checked+label {
      border-color: var(--dole-blue);
      background: rgba(16, 5, 140, .07);
      color: var(--dole-blue);
      font-weight: 700;
    }

    .choice-pill label::before {
      content: "";
      width: 15px;
      height: 15px;
      border-radius: 50%;
      border: 1.5px solid #c3c6d6;
      flex-shrink: 0;
      transition: all .15s;
    }

    .choice-pill input:checked+label::before {
      border-color: var(--dole-blue);
      background: radial-gradient(circle, var(--dole-blue) 0 45%, transparent 50%);
    }

    /* ===== CC QUESTION BLOCKS ===== */
    .cc-block {
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 16px 18px;
      margin-bottom: 14px;
      background: #fbfbfd;
    }

    .cc-block:last-child {
      margin-bottom: 0;
    }

    .cc-block .cc-tag {
      display: inline-block;
      background: var(--dole-blue);
      color: #fff;
      font-size: .7rem;
      font-weight: 700;
      padding: 2px 9px;
      border-radius: 6px;
      margin-bottom: 8px;
      letter-spacing: .03em;
    }

    .cc-block .cc-question {
      font-size: .9rem;
      font-weight: 600;
      margin: 0 0 12px;
    }

    .cc-options {
      display: flex;
      flex-direction: column;
      gap: 9px;
    }

    .cc-options.two-col {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 9px 20px;
    }

    @media (max-width:560px) {
      .cc-options.two-col {
        grid-template-columns: 1fr;
      }
    }

    .cc-option {
      display: flex;
      align-items: flex-start;
      gap: 9px;
      font-size: .86rem;
      cursor: pointer;
    }

    .cc-option input {
      margin-top: 3px;
      accent-color: var(--dole-blue);
      width: 16px;
      height: 16px;
      flex-shrink: 0;
      cursor: pointer;
    }

    /* ===== SQD SATISFACTION TABLE ===== */
    .sqd-table-wrap {
      overflow-x: auto;
      border: 1px solid var(--line);
      border-radius: 12px;
    }

    table.sqd-table {
      width: 100%;
      border-collapse: collapse;
      min-width: 680px;
      font-size: .85rem;
    }

    table.sqd-table thead th {
      background: var(--dole-blue);
      color: #fff;
      font-weight: 600;
      font-size: .74rem;
      text-align: center;
      padding: 12px 8px;
      line-height: 1.25;
    }

    table.sqd-table thead th:first-child {
      text-align: left;
      padding-left: 16px;
      min-width: 260px;
    }

    table.sqd-table tbody td {
      padding: 12px 8px;
      text-align: center;
      border-top: 1px solid var(--line);
    }

    table.sqd-table tbody td:first-child {
      text-align: left;
      padding-left: 16px;
      font-weight: 500;
    }

    table.sqd-table tbody tr:nth-child(even) {
      background: #fafaff;
    }

    .sqd-emoji {
      font-size: 1.15rem;
      display: block;
      margin-bottom: 2px;
    }

    table.sqd-table input[type="radio"] {
      width: 17px;
      height: 17px;
      accent-color: var(--dole-blue);
      cursor: pointer;
    }

    .sqd-code {
      display: inline-block;
      background: rgba(16, 5, 140, .08);
      color: var(--dole-blue);
      font-weight: 700;
      font-size: .72rem;
      border-radius: 5px;
      padding: 1px 6px;
      margin-right: 6px;
    }

    /* ---- SQD table -> stacked cards below 760px ---- */
    @media (max-width:760px) {
      .sqd-table-wrap {
        overflow-x: visible;
        border: none;
        border-radius: 0;
      }

      table.sqd-table {
        min-width: 0;
        font-size: .88rem;
      }

      table.sqd-table thead {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0 0 0 0);
        white-space: nowrap;
      }

      table.sqd-table,
      table.sqd-table tbody,
      table.sqd-table tr,
      table.sqd-table td {
        display: block;
        width: 100%;
      }

      table.sqd-table tbody tr {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 14px 14px 6px;
        margin-bottom: 12px;
      }

      table.sqd-table tbody tr:nth-child(even) {
        background: #fff;
      }

      table.sqd-table tbody td:first-child {
        padding: 0 0 12px;
        font-weight: 600;
        font-size: .92rem;
        border-top: none;
      }

      table.sqd-table tbody td:not(:first-child) {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 2px;
        border-top: 1px solid #eef0f6;
        text-align: left;
      }

      table.sqd-table tbody td:not(:first-child)::before {
        content: attr(data-answer-label);
        font-size: .84rem;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 8px;
      }

      table.sqd-table input[type="radio"] {
        width: 20px;
        height: 20px;
      }
    }

    /* ===== ACTIONS ===== */
    .action-row {
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      margin-top: 8px;
      flex-wrap: wrap;
    }

    .btn {
      border: none;
      border-radius: 10px;
      padding: 13px 26px;
      font-size: .92rem;
      font-weight: 600;
      cursor: pointer;
      font-family: inherit;
      transition: transform .1s, box-shadow .15s, background .15s;
      -webkit-tap-highlight-color: transparent;
    }

    @media (max-width:560px) {
      .action-row {
        flex-direction: column-reverse;
      }

      .action-row .btn {
        width: 100%;
      }
    }

    .btn-outline {
      background: #fff;
      border: 1.5px solid var(--line);
      color: var(--ink);
    }

    .btn-outline:hover {
      border-color: var(--dole-blue);
      color: var(--dole-blue);
    }

    .btn-primary {
      background: var(--dole-blue);
      color: #fff;
      box-shadow: 0 6px 16px rgba(16, 5, 140, .25);
    }

    .btn-primary:hover {
      background: #0c0470;
    }

    .btn:active {
      transform: translateY(1px);
    }

    .footer-note {
      text-align: center;
      font-size: .78rem;
      color: var(--muted);
      margin-top: 22px;
    }

    .section-divider-note {
      font-size: .78rem;
      color: var(--muted);
      background: #f4f6fb;
      border-left: 3px solid var(--dole-blue);
      padding: 9px 12px;
      border-radius: 6px;
      margin-bottom: 18px;
    }
  </style>
</head>

<body>

  <main class="page-wrap">

    <!-- ===================== HEADER ===================== -->
    <div class="header-card">
      <div class="header-top">
        <div class="logo-badge">DOLE</div>
        <div>
          <div class="agency-line">Department of Labor and Employment &middot; MIMAROPA</div>
          <h1 class="header-title">Tulungan Ninyo Kaming Mas Mapabuti ang Aming Serbisyo!</h1>
        </div>
      </div>
      <p class="header-subtitle">Ang Client Satisfaction Measurement (CSM) ay naglalayong masubaybayan ang karanasan ng
        taumbayan hinggil sa kanilang pakikitransaksyon sa mga tanggapan ng gobyerno. Makatutulong ang inyong kasagutan
        ukol sa inyong naging karanasan sa kakatapos lamang na transaksyon, upang mas mapabuti at lalong mapahusay ang
        aming serbisyo publiko. Ang personal na impormasyon na iyong ibabahagi ay mananatiling kumpidensyal. Maaari ring
        piliin na hindi sagutan ang sarbey na ito.</p>
      <div class="control-row">
        <span class="tag">Online Version</span>
        <span class="tag">Control No. &mdash; para sa pagkumpleto ng mga kawani ng DOLE</span>
      </div>
    </div>

    <form method="post" action="<?= base_url('submit-csm') ?>" id="csmForm">
      <?= csrf_field() ?>
      <input type="hidden" name="reference_no" value="<?= $reference_no ?>">

      <!-- ===================== TO BE ACCOMPLISHED BY DOLE PERSONNEL ===================== -->
      <div class="form-card">
        <p class="card-label">Para sa Pagkumpleto ng mga Kawani ng DOLE</p>
        <p class="card-desc">Sasagutan ng kawani ng DOLE bago ibigay ang porma na ito sa kliyente.</p>
        <div class="office-use">
          <div>
            <label for="officeName">Pangalan ng Opisina / Yunit na Operatibo</label>
            <input type="text" id="officeName" name="office_name" value="DOLE MIMAROPA" readonly>
          </div>
          <div>
            <label for="transactionNo">Bilang ng Transaksyon</label>
            <input type="text" id="transactionNo" name="transaction_no" value="1" readonly>
          </div>
          <div class="full">
            <label for="serviceProvided">Pangalan ng Serbisyong Ibinigay</label>
            <input type="text" name="service_availed" value="<?= esc($service_availed) ?>" readonly>
          </div>
        </div>
      </div>

      <!-- ===================== CLIENT PROFILE ===================== -->
      <div class="form-card">
        <p class="card-label">Profile ng Kliyente</p>
        <p class="card-desc">Ang personal na impormasyon na iyong ibabahagi ay mananatiling kumpidensyal. Maaari ring
          piliin na hindi sagutan.</p>


        <div class="row">
          <div class="col-12 field">
            <label>Client Type <span class="required-mark">*</span></label>
            <div class="choice-group">
              <div class="choice-pill">
                <input type="radio" name="client_type" id="clientCitizen" value="Mamamayan">
                <label for="clientCitizen">Mamamayan</label>
              </div>
              <div class="choice-pill">
                <input type="radio" name="client_type" id="clientBusiness" value="Negosyo">
                <label for="clientBusiness">Negosyo</label>
              </div>
              <div class="choice-pill">
                <input type="radio" name="client_type" id="clientGovernment" value="Gobyerno">
                <label for="clientGovernment">Gobyerno (Empleyado o Ahensya)</label>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-4 field">
            <label for="csmDate">Petsa <span class="required-mark">*</span></label>
            <input type="date" id="csmDate" name="date">
          </div>
          <div class="col-4 field">
            <label>Kasarian <span class="required-mark">*</span></label>
            <div class="choice-group">
              <div class="choice-pill">
                <input type="radio" name="sex" id="sexMale" value="Lalaki">
                <label for="sexMale">Lalaki</label>
              </div>
              <div class="choice-pill">
                <input type="radio" name="sex" id="sexFemale" value="Babae">
                <label for="sexFemale">Babae</label>
              </div>
            </div>
          </div>
          <div class="col-4 field">
            <label for="csmAge">Edad <span class="required-mark">*</span></label>
            <input type="number" id="csmAge" name="age" min="0" max="120" placeholder="hal. 34">
          </div>
        </div>

        <div class="row">
          <div class="col-6 field">
            <label for="csmName">Pangalan <span class="hint"
                style="display:inline;font-weight:400;">(Opsyonal)</span></label>
            <input type="text" id="csmName" name="name" placeholder="Juan Dela Cruz">
          </div>
          <div class="col-6 field">
            <label for="csmAddress">Tirahan <span class="hint"
                style="display:inline;font-weight:400;">(Opsyonal)</span></label>
            <input type="text" id="csmAddress" name="address" placeholder="Lungsod / Munisipyo">
          </div>
        </div>
      </div>

      <!-- ===================== CITIZEN'S CHARTER ===================== -->
      <div class="form-card">
        <p class="card-label">Kaalaman sa Citizen's Charter (CC)</p>
        <p class="card-desc">Ang Citizen's Charter ay isang opisyal na dokumento na naglalaman ng mga serbisyo sa isang
          ahensya/opisina ng gobyerno, makikita rito ang mga kinakailangan na dokumento, kaukulang bayarin, at
          pangkabuuang oras ng pagproseso.</p>

        <div class="cc-block">
          <span class="cc-tag">CC1</span>
          <p class="cc-question">Alin sa mga sumusunod ang naglalarawan sa iyong kaalaman sa CC?</p>
          <div class="cc-options">
            <label class="cc-option"><input type="radio" name="cc1" value="1"> Alam ko ang CC at nakita ko ito sa
              napuntahang opisina.</label>
            <label class="cc-option"><input type="radio" name="cc1" value="2"> Alam ko ang CC pero hindi ko ito nakita
              sa napuntahang opisina.</label>
            <label class="cc-option"><input type="radio" name="cc1" value="3"> Nalaman ko ang CC nang makita ko ito sa
              napuntahang opisina.</label>
            <label class="cc-option"><input type="radio" name="cc1" value="4"> Hindi ko alam kung ano ang CC at wala
              akong nakita sa napuntahang opisina. <em>(Lagyan ng tsek ang 'N/A' sa CC2 at CC3 kapag ito ang iyong
                sagot)</em></label>
          </div>
        </div>

        <div class="cc-block">
          <span class="cc-tag">CC2</span>
          <p class="cc-question">Kung alam ang CC (nag-tsek sa opsyon 1&ndash;3 sa CC1), masasabi mo ba na ang CC ng
            napuntahang opisina ay&hellip;?</p>
          <div class="cc-options two-col">
            <label class="cc-option"><input type="radio" name="cc2" value="1"> Madaling makita</label>
            <label class="cc-option"><input type="radio" name="cc2" value="4"> Hindi makita</label>
            <label class="cc-option"><input type="radio" name="cc2" value="2"> Medyo madaling makita</label>
            <label class="cc-option"><input type="radio" name="cc2" value="5"> Hindi angkop</label>
            <label class="cc-option"><input type="radio" name="cc2" value="3"> Mahirap makita</label>
          </div>
        </div>

        <div class="cc-block">
          <span class="cc-tag">CC3</span>
          <p class="cc-question">Kung alam ang CC (nag-tsek sa opsyon 1&ndash;3 sa CC1), gaano nakatulong ang CC sa
            transaksyon mo?</p>
          <div class="cc-options two-col">
            <label class="cc-option"><input type="radio" name="cc3" value="1"> Sobrang nakatulong</label>
            <label class="cc-option"><input type="radio" name="cc3" value="3"> Hindi nakatulong</label>
            <label class="cc-option"><input type="radio" name="cc3" value="2"> Nakatulong naman</label>
            <label class="cc-option"><input type="radio" name="cc3" value="4"> Hindi angkop</label>
          </div>
        </div>
      </div>

      <!-- ===================== SQD SATISFACTION TABLE ===================== -->
      <div class="form-card">
        <p class="card-label">Service Quality Dimensions (SQD)</p>
        <p class="card-desc">Para sa SQD0&ndash;8, lagyan ng tsek ang hanay na pinakaangkop sa iyong sagot.</p>

        <div class="sqd-table-wrap">
          <table class="sqd-table">
            <thead>
              <tr>
                <th>Pahayag</th>
                <th><span class="sqd-emoji">😄</span>Labis na Sumasangayon</th>
                <th><span class="sqd-emoji">🙂</span>Sumasangayon</th>
                <th><span class="sqd-emoji">😐</span>Walang Kinikilingan</th>
                <th><span class="sqd-emoji">🙁</span>Hindi Sumasangayon</th>
                <th><span class="sqd-emoji">😣</span>Lubos na Hindi Sumasangayon</th>
                <th>N/A<br>Hindi Angkop</th>
              </tr>
            </thead>
            <tbody id="sqdTableBody">
              <!-- rows injected by JS -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- ===================== SUGGESTIONS ===================== -->
      <div class="form-card">
        <p class="card-label">Mga Suhestiyon</p>
        <p class="card-desc">Paano pa namin mapapabuti ang aming mga serbisyo? <span class="hint"
            style="display:inline;">(Opsyonal)</span></p>

        <div class="row">
          <div class="col-12 field">
            <textarea id="suggestions" name="suggestions" rows="4"
              placeholder="Ibahagi ang inyong mga suhestiyon dito..."></textarea>
          </div>
          <div class="col-6 field">
            <label for="csmEmail">Email address <span class="hint"
                style="display:inline;font-weight:400;">(Opsyonal)</span></label>
            <input type="email" id="csmEmail" name="email" placeholder="name@example.com">
          </div>
        </div>

        <div class="action-row">
          <button type="reset" class="btn btn-outline">I-clear ang Porma</button>
          <button type="submit" class="btn btn-primary">Isumite ang Sagot</button>
        </div>
      </div>

    </form>

    <p class="footer-note">DOLE MIMAROPA &middot; Client Satisfaction Measurement &middot; Maraming salamat sa pagtulong
      sa amin na mapabuti ang aming serbisyo.</p>

  </main>

  <script>
    (function () {
      'use strict';

      const sqdItems = [
        ["SQD0", "Nasiyahan ako sa serbisyo na aking natanggap sa napuntahan na tanggapan."],
        ["SQD1", "Makatwiran ang oras na aking ginugol para sa pagproseso ng aking transaksyon."],
        ["SQD2", "Ang opisina ay sumusunod sa mga kinakailangang dokumento at mga hakbang batay sa impormasyong ibinigay."],
        ["SQD3", "Ang mga hakbang sa pagproseso, kasama na ang pagbayad ay madali at simple lamang."],
        ["SQD4", "Mabilis at madali akong nakahanap ng impormasyon tungkol sa aking transaksyon mula sa opisina o sa website nito."],
        ["SQD5", "Nagbayad ako ng makatwirang halaga para sa aking transaksyon. (Kung ang sebisyo ay ibinigay ng libre, maglagay ng tsek sa hanay ng N/A.)"],
        ["SQD6", "Pakiramdam ko ay patas ang opisina sa lahat, o \u201cwalang palakasan\u201d, sa aking transaksyon."],
        ["SQD7", "Magalang akong trinato ng mga tauhan, at (kung sakali ako ay humingi ng tulong) alam ko na sila ay handang tumulong sa akin."],
        ["SQD8", "Nakuha ko ang kinakailangan ko mula sa tanggapan ng gobyerno, kung tinanggihan man, ito ay sapat na ipinaliwanag sa akin."]
      ];

      const answers = [
        { value: "strongly_agree", label: "😄 Labis na Sumasangayon" },
        { value: "agree", label: "🙂 Sumasangayon" },
        { value: "neutral", label: "😐 Walang Kinikilingan" },
        { value: "disagree", label: "🙁 Hindi Sumasangayon" },
        { value: "strongly_disagree", label: "😣 Lubos na Hindi Sumasangayon" },
        { value: "na", label: "— N/A" }
      ];
      const tbody = document.getElementById('sqdTableBody');

      sqdItems.forEach(function (item) {
        const code = item[0];
        const text = item[1];
        const tr = document.createElement('tr');

        const labelTd = document.createElement('td');
        labelTd.innerHTML = '<span class="sqd-code">' + code + '</span>' + text;
        tr.appendChild(labelTd);

        answers.forEach(function (ans) {
          const td = document.createElement('td');
          td.setAttribute('data-answer-label', ans.label);
          const input = document.createElement('input');
          input.type = 'radio';
          input.name = code.toLowerCase();
          input.value = ans.value;
          input.required = true;
          td.appendChild(input);
          tr.appendChild(td);
        });

        tbody.appendChild(tr);
      });

      // Default date to today
      const dateField = document.getElementById('csmDate');
      if (dateField) {
        const today = new Date();
        const iso = today.toISOString().slice(0, 10);
        dateField.value = iso;
      }

      const form = document.getElementById('csmForm');
      form.addEventListener('submit', function () {
        alert('Salamat! Naitala na ang inyong sagot.');
      });
    })();
  </script>

</body>

</html>