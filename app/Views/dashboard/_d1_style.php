<style>
    /* ---- screen ---- */
    .d1 table {
        font-size: .85rem;
    }

    .d1 th,
    .d1 td {
        vertical-align: middle;
    }

    .d1 .avg-col {
        background: #fdeede;
        font-weight: 600;
        text-align: center;
    }

    .d1 .overall {
        background: #fbd9a7;
        font-weight: 700;
    }

    .d1 h6 {
        font-weight: 700;
        margin: 22px 0 6px;
    }

    .d1 .sub {
        font-style: italic;
        margin: 10px 0 4px;
    }

    .d1 td.num {
        text-align: center;
    }

    /* ---- header banner ---- */
    .header-banner {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding-bottom: 10px;
        margin: 0 auto 14px;
        border-bottom: 1px solid #000;
        width: 45%;
    }

    .header-banner img {
        height: auto;
    }

    .dole-cell img,
    .iso-cell img,
    .bp-cell img {
        width: 50px;
        height: auto;
        vertical-align: middle;
        margin: 0;
    }

    .agency-title {
        text-align: center;
        padding: 0 8px;
        flex: 0;
        white-space: nowrap;
    }

    .agency-title .country {
        font-size: 10pt;
        margin: 0;
        line-height: 1.2;
    }

    .agency-title .dept {
        font-size: 10pt;
        font-weight: bold;
        margin: 0;
        line-height: 1.2;
    }

    .agency-title .region {
        font-size: 9.5pt;
        margin: 0;
        letter-spacing: .5px;
        line-height: 1.2;
    }

    /* ---- print: 2 pages, small text ---- */
    @page {
        size: A4 portrait;
        margin: 8mm;
    }

    @media print {
        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        html,
        body {
            font-size: 7.5pt !important;
            background: #fff !important;
        }

        .no-print,
        .sidebar,
        .navbar,
        .app-header,
        .sidebar-offcanvas {
            display: none !important;
        }

        .app-body {
            display: block !important;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .panel,
        .d1 {
            box-shadow: none !important;
            border: 0 !important;
            padding: 0 !important;
        }

        .d1 table {
            font-size: 7pt !important;
            margin-bottom: 2px !important;
        }

        .d1 th,
        .d1 td {
            padding: 1px 3px !important;
            line-height: 1.15 !important;
        }

        .d1 h5 {
            font-size: 10pt;
            margin: 2px 0 !important;
        }

        .d1 h6 {
            font-size: 8pt;
            margin: 6px 0 2px !important;
        }

        .d1 .sub {
            margin: 4px 0 2px !important;
        }

        .d1 .mb-3,
        .d1 .mt-3,
        .d1 .mt-4 {
            margin-top: 2px !important;
            margin-bottom: 2px !important;
        }

        .table-responsive {
            overflow: visible !important;
        }

        /* banner (print) */
        .header-banner {
            width: 70%;
            gap: 8px;
            padding-bottom: 4px;
            margin-bottom: 2mm !important;
        }

        .dole-cell img,
        .iso-cell img,
        .bp-cell img {
            width: 14mm !important;
        }

        .agency-title .country,
        .agency-title .dept {
            font-size: 8pt;
        }

        .agency-title .region {
            font-size: 7.5pt;
        }

        .d1 tr {
            break-inside: avoid;
        }

        .d1 .page-break {
            break-before: page;
            margin-top: 0 !important;
        }
    }
</style>