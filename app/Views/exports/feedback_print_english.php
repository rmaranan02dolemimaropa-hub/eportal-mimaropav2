<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CSM Questionnaire &ndash; English (Print Version)</title>
  <style>
    :root {
      --dole-blue: #10058C;
      --ink: #000;
      --line: #000;
    }

    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    body {
      margin: 0;
      background: #5a5c66;
      font-family: Arial, Helvetica, sans-serif;
      color: #111;
    }

    /* ===================== TOOLBAR (screen only) ===================== */
    .toolbar {
      position: sticky;
      top: 0;
      z-index: 20;
      background: #1c1d21;
      color: #fff;
      padding: 14px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, .25);
    }

    .toolbar h1 {
      font-size: 1rem;
      margin: 0;
      font-weight: 700;
    }

    .toolbar .sub {
      font-size: .76rem;
      color: #b8b9c4;
      margin-top: 2px;
    }

    .toolbar-actions {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .tbtn {
      border: 1.5px solid #3a3b45;
      background: #26272f;
      color: #fff;
      padding: 9px 16px;
      border-radius: 8px;
      font-size: .85rem;
      font-weight: 600;
      cursor: pointer;
      font-family: inherit;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      transition: background .15s, border-color .15s;
    }

    .tbtn:hover {
      border-color: #5a5cff;
    }

    .tbtn.primary {
      background: var(--dole-blue);
      border-color: var(--dole-blue);
    }

    .tbtn.primary:hover {
      background: #2a1fb0;
    }

    /* ===================== SCREEN LAYOUT (sheet preview) ===================== */
    .sheets {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 32px;
      padding: 32px 16px 64px;
    }

    .sheet-label {
      color: #e7e7ee;
      font-size: .8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .08em;
      margin-bottom: -14px;
    }

    .page {
      background: #fff;
      width: min(816px, calc(100vw - 32px));
      max-width: 816px;
      /* 8.5in @ 96dpi */
      min-height: 1056px;
      /* 11in @ 96dpi */
      padding: 34px 40px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, .35);
      font-size: 10.4px;
      line-height: 1.35;
    }

    /* ===================== FORM CONTENT (shared) ===================== */
    .ctrl-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-bottom: 6px;
      font-size: 11px;
    }

    .ctrl-row .ver {
      font-weight: 700;
    }

    table.top-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 8px;
    }

    table.top-table td {
      border: 1px solid var(--line);
      padding: 5px 8px;
      vertical-align: middle;
      font-size: 10.2px;
    }

    table.top-table td.dole-cell {
      background: #000;
      color: #fff;
      font-weight: 700;
      width: 120px;
      text-align: left;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
      color-adjust: exact;
      /* older Firefox */
    }

    .brand-row {
      text-align: center;
      margin: 6px 0 10px;
    }

    .brand-row .logos {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 14px;
    }

    .brand-logo {
      width: 42px;
      height: 42px;
      object-fit: contain;
      flex-shrink: 0;
    }

    .brand-badge.alt {
      background: #c8102e;
    }

    .brand-text .agency {
      font-size: 10.5px;
    }

    .brand-text .tagline {
      font-size: 12.5px;
      font-weight: 800;
      margin-top: 1px;
    }

    .intro-text {
      font-size: 9.6px;
      margin: 0 0 8px;
      text-align: justify;
    }

    .intro-text u {
      text-underline-offset: 2px;
    }

    .inline-field {
      font-size: 10px;
      margin-bottom: 6px;
    }

    .inline-field strong {
      font-weight: 700;
    }

    .box {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 10px;
      height: 10px;
      border: 1px solid #000;
      margin: 0 4px 0 10px;
      position: relative;
      top: 1px;
      font-size: 9px;
      line-height: 1;
      font-weight: 800;
      color: #000;
      overflow: hidden;
      flex-shrink: 0;
    }

    .box:first-of-type {
      margin-left: 6px;
    }

    .fill-line {
      display: inline-block;
      border-bottom: 1px solid #000;
      min-width: 90px;
      height: 10px;
    }

    hr.divider {
      border: none;
      border-top: 1px solid #000;
      margin: 8px 0;
    }

    .instructions {
      font-size: 9.6px;
      margin: 6px 0 6px;
    }

    .instructions strong {
      font-weight: 700;
    }

    table.cc-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
      font-size: 9.6px;
    }

    table.cc-table td {
      border: 1px solid var(--line);
      padding: 6px 8px;
      vertical-align: top;
    }

    table.cc-table td.cc-code {
      width: 34px;
      font-weight: 700;
      text-align: left;
    }

    table.cc-table .cc-opt {
      margin: 2px 0;
    }

    table.cc-table .cc-two-col {
      display: grid;
      grid-template-columns: 1fr 1fr;
      column-gap: 14px;
    }

    table.sqd-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 9.2px;
      margin-bottom: 10px;
    }

    table.sqd-table th,
    table.sqd-table td {
      border: 1px solid var(--line);
      padding: 4px 6px;
    }

    table.sqd-table thead th {
      text-align: center;
      font-weight: 700;
      vertical-align: bottom;
      font-size: 8.6px;
      padding: 5px 4px;
    }

    table.sqd-table thead th:first-child {
      width: 34%;
    }

    table.sqd-table tbody td:not(:first-child) {
      width: 9.5%;
      text-align: center;
      vertical-align: middle;
      font-size: 11px;
      font-weight: 700;
      color: #000;
    }

    table.sqd-table .emoji {
      font-size: 15px;
      display: block;
      line-height: 1;
      margin-bottom: 2px;
    }

    table.sqd-table tbody td:first-child {
      text-align: left;
    }

    table.sqd-table tbody td:not(:first-child) {
      width: 9.5%;
    }

    .suggestions-block {
      font-size: 9.8px;
      margin-top: 4px;
    }

    .suggest-line {
      border-bottom: 1px solid #000;
      height: 16px;
      margin-bottom: 2px;
    }

    .email-line {
      font-size: 9.8px;
      margin-top: 8px;
    }

    .thank-you {
      text-align: center;
      font-weight: 700;
      margin-top: 12px;
      font-size: 11px;
    }


    /* ===================== RESPONSIVE SCREEN RULES ===================== */

    /* Keep the paper preview responsive without changing the print layout */
    .page {
      width: min(816px, 100%);
      max-width: 816px;
    }

    .page img {
      max-width: 100%;
    }

    /* Prevent long text/values from forcing horizontal overflow */
    .page,
    .page table,
    .page td,
    .page th {
      overflow-wrap: anywhere;
      word-break: normal;
    }

    @media screen and (max-width: 850px) {
      body {
        background: #eef0f4;
      }

      .toolbar {
        position: sticky;
        padding: 12px 14px;
      }

      .toolbar>div:first-child {
        min-width: 0;
        flex: 1 1 100%;
      }

      .toolbar h1 {
        font-size: .95rem;
      }

      .toolbar .sub {
        font-size: .7rem;
        line-height: 1.35;
      }

      .toolbar-actions {
        width: 100%;
      }

      .tbtn {
        flex: 1 1 0;
        justify-content: center;
        min-height: 42px;
      }

      .sheets {
        padding: 16px 8px 40px;
        gap: 18px;
      }

      .sheet-label {
        margin-bottom: -8px;
      }

      .page {
        padding: 24px;
        font-size: 10px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, .18);
      }

      .ctrl-row {
        font-size: 10px;
        gap: 10px;
      }

      table.top-table td {
        padding: 5px 6px;
        font-size: 9.5px;
      }

      table.top-table td.dole-cell {
        width: 95px;
      }

      .brand-logo {
        width: 38px;
        height: 38px;
      }

      .brand-row .logos {
        gap: 10px;
      }

      .brand-text .agency {
        font-size: 9.5px;
      }

      .brand-text .tagline {
        font-size: 11px;
      }

      .intro-text,
      .instructions {
        font-size: 9px;
        line-height: 1.35;
      }

      table.cc-table {
        font-size: 9px;
      }

      table.cc-table td {
        padding: 5px 6px;
      }

      table.sqd-table {
        font-size: 8px;
        table-layout: fixed;
      }

      table.sqd-table th,
      table.sqd-table td {
        padding: 3px 3px;
      }

      table.sqd-table thead th {
        font-size: 7.2px;
        line-height: 1.15;
      }

      table.sqd-table tbody td:not(:first-child) {
        font-size: 9px;
      }

      table.sqd-table .emoji {
        font-size: 12px;
      }
    }

    @media screen and (max-width: 600px) {
      .toolbar {
        align-items: stretch;
      }

      .toolbar-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 7px;
      }

      .tbtn {
        width: 100%;
        padding: 8px 10px;
        font-size: .78rem;
      }

      .sheets {
        padding: 10px 4px 30px;
      }

      .page {
        padding: 16px 12px;
        font-size: 9px;
        border-radius: 2px;
      }

      .ctrl-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 3px;
        margin-bottom: 7px;
      }

      .ctrl-row .ver {
        align-self: flex-end;
      }

      table.top-table {
        table-layout: fixed;
      }

      table.top-table td {
        padding: 4px;
        font-size: 8.2px;
        line-height: 1.25;
      }

      table.top-table td.dole-cell {
        width: 78px;
        font-size: 7.8px;
      }

      .brand-row {
        margin: 5px 0 8px;
      }

      .brand-row .logos {
        gap: 7px;
      }

      .brand-logo {
        width: 32px;
        height: 32px;
      }

      .brand-text .agency {
        font-size: 8px;
      }

      .brand-text .tagline {
        font-size: 9.5px;
      }

      .intro-text,
      .instructions,
      .inline-field {
        font-size: 8.2px;
        line-height: 1.3;
      }

      .inline-field {
        margin-bottom: 5px;
      }

      .fill-line {
        min-width: 55px;
      }

      .box {
        width: 9px;
        height: 9px;
        margin-left: 5px;
        margin-right: 2px;
        font-size: 7px;
      }

      .cc-two-col {
        grid-template-columns: 1fr !important;
        row-gap: 2px;
      }

      table.cc-table {
        font-size: 8px;
        table-layout: fixed;
      }

      table.cc-table td {
        padding: 4px;
        line-height: 1.25;
      }

      table.cc-table td.cc-code {
        width: 28px;
      }

      table.sqd-table {
        font-size: 7px;
      }

      table.sqd-table thead th {
        font-size: 6.2px;
        padding: 3px 1px;
      }

      table.sqd-table tbody td {
        line-height: 1.2;
      }

      table.sqd-table tbody td:not(:first-child) {
        font-size: 8px;
      }

      table.sqd-table .emoji {
        font-size: 10px;
        margin-bottom: 1px;
      }

      .suggestions-block,
      .email-line,
      .thank-you {
        font-size: 8.2px;
      }

      .suggest-line {
        min-height: 18px;
      }
    }

    @media screen and (max-width: 380px) {
      .toolbar-actions {
        grid-template-columns: 1fr;
      }

      .page {
        padding: 12px 8px;
      }

      table.sqd-table thead th {
        font-size: 5.6px;
      }

      table.sqd-table tbody td:not(:first-child) {
        font-size: 7px;
      }

      table.sqd-table .emoji {
        font-size: 9px;
      }
    }

    /* ===================== PRINT RULES ===================== */
    @media print {
      body {
        background: #fff;
      }

      .toolbar {
        display: none !important;
      }

      .sheets {
        padding: 0;
        gap: 0;
      }

      .sheet-label {
        display: none;
      }

      .page {
        box-shadow: none;
        width: auto;
        min-height: 0;
        padding: 0.35in 0.45in;
      }
    }

    @page {
      size: letter portrait;
      margin: 0;
    }
  </style>
</head>

<body>

  <!-- ===================== TOOLBAR ===================== -->
  <div class="toolbar no-print">
    <div>
      <h1>CSM Questionnaire &mdash; English</h1>
      <div class="sub">Official-layout replica of the DOLE MIMAROPA paper form, ready for printing.</div>
    </div>
    <div class="toolbar-actions">
      <button type="button" class="tbtn primary" onclick="window.location.href='<?= base_url('feedback') ?>'">
        Back
      </button>
      <button class="tbtn primary" onclick="window.print()">🖨️ Print Form</button>
    </div>
  </div>

  <div class="sheets">

    <!-- ===================== ENGLISH SHEET ===================== -->
    <div class="sheet-label">English &middot; Preview</div>
    <div class="page" id="formEnglish">

      <div class="ctrl-row">
        <div>Control No: <span class="fill-line" style="min-width:110px;"></span></div>
        <div class="ver">(Online Version)</div>
      </div>

      <table class="top-table">
        <tr>
          <td class="dole-cell" rowspan="2">To be accomplished<br>by DOLE Personnel</td>
          <td colspan="2">Name of Office/Operating Unit: <?= esc($feedback['office_name']) ?></td>
        </tr>
        <tr>
          <td style="width:60%;">Name of Service Provided: <?= esc($feedback['service_availed']) ?></td>
          <td>Number of Transaction: <?= esc($feedback['transaction_no']) ?></td>
        </tr>
      </table>

      <div class="brand-row">
        <div class="logos">
          <img src="<?= base_url('assets/img/dole_mimaropa.png') ?>" alt="DOLE Logo" class="brand-logo">
          <div class="brand-text">
            <div class="agency">Department of Labor and Employment</div>
            <div class="tagline">HELP US SERVE YOU BETTER!</div>
          </div>
          <img src="<?= base_url('assets/img/bagong_pilipinas.png') ?>" alt="Bagong Pilipinas Logo" class="brand-logo">
        </div>
      </div>

      <p class="intro-text">This Client Satisfaction Measurement (CSM) tracks the customer experience of government
        offices. Your feedback on your <u>recently concluded transaction</u> will help this office provide a better
        service. Personal information shared will be kept confidential and you always have the option to not answer this
        form.</p>

      <div class="inline-field">
        <strong>Client type:</strong>
        <span class="box">
          <?= $feedback['client_type'] == 'Citizen' ? '✓' : '' ?>
        </span> Citizen

        <span class="box">
          <?= $feedback['client_type'] == 'Business' ? '✓' : '' ?>
        </span> Business

        <span class="box">
          <?= $feedback['client_type'] == 'Government' ? '✓' : '' ?>
        </span> Government (Employee or another agency)
      </div>

      <div class="inline-field">
        <strong>Date:</strong>
        <span style="display:inline-block; border-bottom:1px solid #000; min-width:100px; text-align: center;">
          <?= date('F d, Y', strtotime($feedback['date'])) ?>
        </span>
        &nbsp;&nbsp;<strong>Sex:</strong>
        <span class="box">
          <?= $feedback['sex'] == 'Male' ? '✓' : '' ?>
        </span> Male

        <span class="box">
          <?= $feedback['sex'] == 'Female' ? '✓' : '' ?>
        </span> Female
        &nbsp;&nbsp;<strong>Age:</strong>
        <span style="display:inline-block; border-bottom:1px solid #000; min-width:50px; text-align: center;">
          <?= esc($feedback['age']) ?>
        </span>
      </div>

      <div class="inline-field">
        <strong style="width:100%;">Name:</strong> (Optional)
        <span style="display:inline-block; border-bottom:1px solid #000; min-width:120px; text-align: center;">
          <?= esc($feedback['name']) ?>
        </span>
        &nbsp;&nbsp;<strong>Address</strong> (Optional):
        <span style="display:inline-block; border-bottom:1px solid #000; min-width:80px; text-align: center;">
          <?= esc($feedback['address']) ?>
        </span>
      </div>

      <hr class="divider">

      <p class="instructions">INSTRUCTIONS: Please place a <strong>Check mark (&#10003;)</strong> in the designated box
        that corresponds to your answer on the Citizen&rsquo;s Charter (CC) questions. The Citizen&rsquo;s Charter is an
        official document that reflects the services of a government agency/office including its requirements, fees, and
        processing times among others.</p>

      <table class="cc-table">
        <tr>
          <td class="cc-code">CC1</td>
          <td>
            Which of the following best describes your awareness of a CC?
            <div class="cc-opt">
              <span class="box">
                <?= $feedback['cc1'] == 1 ? '✓' : '' ?>
              </span>
              1. I know what a CC is and I saw this office&rsquo;s CC.
            </div>

            <div class="cc-opt">
              <span class="box">
                <?= $feedback['cc1'] == 2 ? '✓' : '' ?>
              </span>
              2. I know what a CC is but I did NOT see this office&rsquo;s CC.
            </div>

            <div class="cc-opt">
              <span class="box">
                <?= $feedback['cc1'] == 3 ? '✓' : '' ?>
              </span>
              3. I learned of the CC only when I saw this office&rsquo;s CC.
            </div>

            <div class="cc-opt">
              <span class="box">
                <?= $feedback['cc1'] == 4 ? '✓' : '' ?>
              </span>
              4. I do not know what a CC is and I did not see one in this
              office. (Answer &lsquo;N/A&rsquo; on CC2 and CC3)
            </div>
          </td>
        </tr>
        <tr>
          <td class="cc-code">CC2</td>
          <td>
            If aware of CC (answered 1-3 in CC1), would you say that the CC of this office was &hellip;?
            <div class="cc-two-col">
              <div>
                <div class="cc-opt"><span class="box"><?= $feedback['cc2'] == 1 ? '✓' : '' ?></span> 1. Easy to see
                </div>
                <div class="cc-opt"><span class="box"><?= $feedback['cc2'] == 2 ? '✓' : '' ?></span> 2. Somewhat easy to
                  see</div>
                <div class="cc-opt"><span class="box"><?= $feedback['cc2'] == 3 ? '✓' : '' ?></span> 3. Difficult to see
                </div>
              </div>
              <div>
                <div class="cc-opt"><span class="box"><?= $feedback['cc2'] == 4 ? '✓' : '' ?></span> 4. Not visible at
                  all</div>
                <div class="cc-opt"><span class="box"><?= $feedback['cc2'] == 5 ? '✓' : '' ?></span> 5. Not Applicable
                </div>
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <td class="cc-code">CC3</td>
          <td>
            If aware of CC (answered codes 1-3 in CC1), how much did the CC help you in your transaction?
            <div class="cc-two-col">
              <div>
                <div class="cc-opt"><span class="box"><?= $feedback['cc3'] == 1 ? '✓' : '' ?></span> 1. Helped very much
                </div>
                <div class="cc-opt"><span class="box"><?= $feedback['cc3'] == 2 ? '✓' : '' ?></span> 2. Somewhat helped
                </div>
              </div>
              <div>
                <div class="cc-opt"><span class="box"><?= $feedback['cc3'] == 3 ? '✓' : '' ?></span> 3. Did not help
                </div>
                <div class="cc-opt"><span class="box"><?= $feedback['cc3'] == 4 ? '✓' : '' ?></span> 4. Not Applicable
                </div>
              </div>
            </div>
          </td>
        </tr>
      </table>

      <p class="instructions"><strong>INSTRUCTIONS:</strong><br>For SQD 0-8, please put a <strong>check mark
          (&#10003;)</strong> on the column that best corresponds to your answer.</p>



      <table class="sqd-table">
        <thead>
          <tr>
            <th></th>
            <th><span class="emoji">&#128512;</span>Strongly Agree</th>
            <th><span class="emoji">&#128578;</span>Agree</th>
            <th><span class="emoji">&#128528;</span>Neither Agree nor Disagree</th>
            <th><span class="emoji">&#128577;</span>Disagree</th>
            <th><span class="emoji">&#128548;</span>Strongly Disagree</th>
            <th>N/A<br>Not Applicable</th>
          </tr>
        </thead>
        <tbody>
          <tr>

            <td><strong>SQD0.</strong> I am satisfied with the service that I availed.</td>
            <?php
            function checkSQD($value, $answer)
            {
              return (string) $value === (string) $answer ? '✓' : '';
            }
            ?>
            <td><?= checkSQD($feedback['sqd0'], 'strongly_agree') ?></td>
            <td><?= checkSQD($feedback['sqd0'], 'agree') ?></td>
            <td><?= checkSQD($feedback['sqd0'], 'neutral') ?></td>
            <td><?= checkSQD($feedback['sqd0'], 'disagree') ?></td>
            <td><?= checkSQD($feedback['sqd0'], 'strongly_disagree') ?></td>
            <td><?= checkSQD($feedback['sqd0'], 'na') ?></td>
          </tr>

          <tr>
            <td><strong>SQD1.</strong> I spent a reasonable amount of time for my transaction.</td>
            <td><?= checkSQD($feedback['sqd1'], 'strongly_agree') ?></td>
            <td><?= checkSQD($feedback['sqd1'], 'agree') ?></td>
            <td><?= checkSQD($feedback['sqd1'], 'neutral') ?></td>
            <td><?= checkSQD($feedback['sqd1'], 'disagree') ?></td>
            <td><?= checkSQD($feedback['sqd1'], 'strongly_disagree') ?></td>
            <td><?= checkSQD($feedback['sqd1'], 'na') ?></td>
          </tr>
          <tr>
            <td><strong>SQD2.</strong> The office followed the transaction&rsquo;s requirements and steps based on the
              information provided.</td>
            <td><?= checkSQD($feedback['sqd2'], 'strongly_agree') ?></td>
            <td><?= checkSQD($feedback['sqd2'], 'agree') ?></td>
            <td><?= checkSQD($feedback['sqd2'], 'neutral') ?></td>
            <td><?= checkSQD($feedback['sqd2'], 'disagree') ?></td>
            <td><?= checkSQD($feedback['sqd2'], 'strongly_disagree') ?></td>
            <td><?= checkSQD($feedback['sqd2'], 'na') ?></td>
          </tr>
          <tr>
            <td><strong>SQD3.</strong> The steps (including payment) I needed to do for my transaction were easy and
              simple.</td>
            <td><?= checkSQD($feedback['sqd3'], 'strongly_agree') ?></td>
            <td><?= checkSQD($feedback['sqd3'], 'agree') ?></td>
            <td><?= checkSQD($feedback['sqd3'], 'neutral') ?></td>
            <td><?= checkSQD($feedback['sqd3'], 'disagree') ?></td>
            <td><?= checkSQD($feedback['sqd3'], 'strongly_disagree') ?></td>
            <td><?= checkSQD($feedback['sqd3'], 'na') ?></td>
          </tr>
          <tr>
            <td><strong>SQD4.</strong> I easily found information about my transaction from the office&rsquo;s website.
            </td>
            <td><?= checkSQD($feedback['sqd4'], 'strongly_agree') ?></td>
            <td><?= checkSQD($feedback['sqd4'], 'agree') ?></td>
            <td><?= checkSQD($feedback['sqd4'], 'neutral') ?></td>
            <td><?= checkSQD($feedback['sqd4'], 'disagree') ?></td>
            <td><?= checkSQD($feedback['sqd4'], 'strongly_disagree') ?></td>
            <td><?= checkSQD($feedback['sqd4'], 'na') ?></td>
          </tr>
          <tr>
            <td><strong>SQD5.</strong> I paid a reasonable amount of fees for my transaction. <em>(If service was free,
                mark the &lsquo;N/A&rsquo; column)</em></td>
            <td><?= checkSQD($feedback['sqd5'], 'strongly_agree') ?></td>
            <td><?= checkSQD($feedback['sqd5'], 'agree') ?></td>
            <td><?= checkSQD($feedback['sqd5'], 'neutral') ?></td>
            <td><?= checkSQD($feedback['sqd5'], 'disagree') ?></td>
            <td><?= checkSQD($feedback['sqd5'], 'strongly_disagree') ?></td>
            <td><?= checkSQD($feedback['sqd5'], 'na') ?></td>
          </tr>
          <tr>
            <td><strong>SQD6.</strong> I am confident my online transaction was secure.</td>
            <td><?= checkSQD($feedback['sqd6'], 'strongly_agree') ?></td>
            <td><?= checkSQD($feedback['sqd6'], 'agree') ?></td>
            <td><?= checkSQD($feedback['sqd6'], 'neutral') ?></td>
            <td><?= checkSQD($feedback['sqd6'], 'disagree') ?></td>
            <td><?= checkSQD($feedback['sqd6'], 'strongly_disagree') ?></td>
            <td><?= checkSQD($feedback['sqd6'], 'na') ?></td>
          </tr>
          <tr>
            <td><strong>SQD7.</strong> The office&rsquo;s online support was available, and (if asked questions) online
              support was quick to respond.</td>
            <td><?= checkSQD($feedback['sqd7'], 'strongly_agree') ?></td>
            <td><?= checkSQD($feedback['sqd7'], 'agree') ?></td>
            <td><?= checkSQD($feedback['sqd7'], 'neutral') ?></td>
            <td><?= checkSQD($feedback['sqd7'], 'disagree') ?></td>
            <td><?= checkSQD($feedback['sqd7'], 'strongly_disagree') ?></td>
            <td><?= checkSQD($feedback['sqd7'], 'na') ?></td>
          </tr>
          <tr>
            <td><strong>SQD8.</strong> I got what I needed from the government office, or (if denied) denial of request
              was sufficiently explained to me.</td>
            <td><?= checkSQD($feedback['sqd8'], 'strongly_agree') ?></td>
            <td><?= checkSQD($feedback['sqd8'], 'agree') ?></td>
            <td><?= checkSQD($feedback['sqd8'], 'neutral') ?></td>
            <td><?= checkSQD($feedback['sqd8'], 'disagree') ?></td>
            <td><?= checkSQD($feedback['sqd8'], 'strongly_disagree') ?></td>
            <td><?= checkSQD($feedback['sqd8'], 'na') ?></td>
          </tr>
        </tbody>
      </table>

      <div class="suggestions-block">
        Suggestions on how we can further improve our services (optional):
        <div class="suggest-line">
          <?= nl2br(esc($feedback['suggestions'])) ?>
        </div>
      </div>

      <div class="email-line">Email address (optional):
        <?= esc($feedback['email']) ?>
      </div>

      <div class="thank-you">THANK YOU!</div>

    </div>

  </div>

  <script>

  </script>

</body>

</html>