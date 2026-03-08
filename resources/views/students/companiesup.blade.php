<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - MOA</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --red:        #dc2626;
            --red-dark:   #991b1b;
            --red-deeper: #7f0000;
            --sidebar-w:  260px;
            --sidebar-w-collapsed: 70px;
            --topbar-h:   64px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
            min-height: 100vh;
        }

        /* =============== SIDEBAR =============== */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(160deg, #1a0000 0%, #4a0000 50%, #7f0000 100%);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: width 0.35s cubic-bezier(0.4,0,0.2,1);
            overflow: hidden;
            box-shadow: 4px 0 24px rgba(0,0,0,0.18);
        }

        .sidebar.collapsed { width: var(--sidebar-w-collapsed); }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 22px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            text-decoration: none;
            flex-shrink: 0;
        }

        .sidebar-brand img {
            width: 36px; height: 36px;
            object-fit: contain;
            flex-shrink: 0;
            filter: drop-shadow(0 0 8px rgba(255,255,255,0.2));
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.25s, width 0.25s;
        }

        .sidebar-brand-name {
            font-size: 16px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.3px;
            line-height: 1;
        }

        .sidebar-brand-name span { color: #fca5a5; }

        .sidebar-brand-sub {
            font-size: 9px;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 3px;
        }

        .sidebar.collapsed .sidebar-brand-text { opacity: 0; width: 0; }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            text-decoration: none;
            flex-shrink: 0;
            transition: background 0.2s;
        }

        .sidebar-user:hover { background: rgba(255,255,255,0.05); }

        .user-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: rgba(239,68,68,0.25);
            border: 1.5px solid rgba(239,68,68,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fca5a5;
            font-size: 16px;
            flex-shrink: 0;
        }

        .user-info { overflow: hidden; white-space: nowrap; transition: opacity 0.25s, width 0.25s; }
        .user-name { font-size: 13px; font-weight: 600; color: #fff; display: block; text-overflow: ellipsis; overflow: hidden; }
        .user-role { font-size: 10px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; }
        .sidebar.collapsed .user-info { opacity: 0; width: 0; }

        .sidebar-nav { flex: 1; overflow-y: auto; padding: 12px 0; }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(239,68,68,0.3); border-radius: 10px; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 20px;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.25s;
            position: relative;
            white-space: nowrap;
            border-left: 3px solid transparent;
        }

        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(239,68,68,0.15); border-left-color: #ef4444; }
        .nav-item .nav-icon { font-size: 18px; flex-shrink: 0; width: 22px; text-align: center; }
        .nav-item .nav-label { transition: opacity 0.25s; overflow: hidden; }
        .sidebar.collapsed .nav-label { opacity: 0; width: 0; }

        .nav-sub-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 9px 20px 9px 56px;
            color: rgba(255,255,255,0.4);
            text-decoration: none;
            font-size: 13px;
            font-weight: 400;
            transition: all 0.25s;
            position: relative;
            white-space: nowrap;
            border-left: 3px solid transparent;
        }

        .nav-sub-item:hover { color: #fff; background: rgba(255,255,255,0.04); }
        .nav-sub-item.active { color: #fca5a5; border-left-color: rgba(239,68,68,0.5); }
        .sidebar.collapsed .nav-sub-item { display: none; }

        .nav-item .tooltip-label {
            position: absolute;
            left: calc(var(--sidebar-w-collapsed) + 8px);
            background: #1a0000;
            color: #fff;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 6px;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 9999;
        }

        .sidebar.collapsed .nav-item:hover .tooltip-label { opacity: 1; }
        .sidebar-footer { padding: 12px 0; border-top: 1px solid rgba(255,255,255,0.07); flex-shrink: 0; }

        /* =============== MAIN =============== */
        .main-content {
            margin-left: var(--sidebar-w);
            transition: margin-left 0.35s cubic-bezier(0.4,0,0.2,1);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content.expanded { margin-left: var(--sidebar-w-collapsed); }

        .topbar {
            height: var(--topbar-h);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .topbar-left { display: flex; align-items: center; gap: 16px; }

        .menu-toggle {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: #f5f5f5;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            font-size: 18px;
            transition: all 0.2s;
        }

        .menu-toggle:hover { background: #fee2e2; color: var(--red); }
        .topbar-title { font-size: 13.5px; font-weight: 500; color: #888; }
        .topbar-title span { color: var(--red); font-weight: 600; }

        .topbar-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff5f5;
            border: 1px solid #fecaca;
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--red);
        }

        /* =============== PAGE =============== */
        .page-content { padding: 28px; flex: 1; }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h1 { font-size: 24px; font-weight: 800; color: #1a1a1a; letter-spacing: -0.5px; }
        .page-header h1 span { color: var(--red); }

        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #888; margin-top: 6px; }
        .breadcrumb a { color: var(--red); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb i { font-size: 10px; }

        /* Add MOA button */
        .btn-add-moa {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(220,38,38,0.25);
        }

        .btn-add-moa:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220,38,38,0.35);
            color: #fff;
        }

        /* Info banner */
        .info-banner {
            background: linear-gradient(135deg, #7f0000 0%, #b91c1c 50%, #dc2626 100%);
            border-radius: 16px;
            padding: 22px 28px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 24px rgba(185,28,28,0.2);
        }

        .info-banner::before {
            content: '';
            position: absolute;
            top: -50px; right: -50px;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }

        .info-banner::after {
            content: '';
            position: absolute;
            bottom: -30px; right: 120px;
            width: 120px; height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
        }

        .info-banner-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 22px;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .info-banner-text { position: relative; z-index: 1; }
        .info-banner-text h3 { font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 4px; }
        .info-banner-text p { font-size: 13px; color: rgba(255,255,255,0.7); line-height: 1.5; }

        /* Table card */
        .table-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .table-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 24px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
            flex-wrap: wrap;
        }

        .table-card-header-left { display: flex; align-items: center; gap: 12px; }

        .header-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--red);
            font-size: 15px;
            flex-shrink: 0;
        }

        .table-card-header h2 { font-size: 16px; font-weight: 700; color: #1a1a1a; }
        .table-card-header p  { font-size: 12.5px; color: #888; margin-top: 2px; }

        .table-card-body { padding: 0; }

        /* DataTables */
        .table-card-body .dataTables_wrapper {
            padding: 16px 22px;
            font-family: 'Poppins', sans-serif;
            font-size: 13.5px;
        }

        .table-card-body table.dataTable { width: 100% !important; border-collapse: collapse; }

        .table-card-body table.dataTable thead th {
            background: #fafafa;
            color: #555;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            border-bottom: 1px solid #f0f0f0;
            border-top: none;
        }

        .table-card-body table.dataTable tbody td {
            padding: 14px;
            color: #333;
            border-bottom: 1px solid #f9f9f9;
            font-size: 13.5px;
            vertical-align: middle;
        }

        .table-card-body table.dataTable tbody tr:hover td { background: #fff5f5; }
        .table-card-body table.dataTable tbody tr:last-child td { border-bottom: none; }

        .dataTables_filter input {
            border: 1px solid #e5e5e5 !important;
            border-radius: 8px !important;
            padding: 6px 12px !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important;
            outline: none !important;
        }

        .dataTables_filter input:focus {
            border-color: var(--red) !important;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.08) !important;
        }

        .dataTables_length select {
            border: 1px solid #e5e5e5 !important;
            border-radius: 8px !important;
            padding: 4px 8px !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important;
        }

        .dataTables_paginate .paginate_button {
            border-radius: 6px !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important;
            padding: 4px 10px !important;
        }

        .dataTables_paginate .paginate_button.current {
            background: var(--red) !important;
            border-color: var(--red) !important;
            color: #fff !important;
        }

        .dataTables_paginate .paginate_button:hover {
            background: #fee2e2 !important;
            border-color: #fecaca !important;
            color: var(--red) !important;
        }

        /* Company cell */
        .company-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .company-avatar {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #dc2626, #991b1b);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .company-name-text { font-weight: 600; color: #1a1a1a; font-size: 13.5px; }
        .company-sub { font-size: 11.5px; color: #aaa; margin-top: 1px; }

        /* Year badge */
        .year-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #fff5f5;
            color: var(--red);
            border: 1px solid #fecaca;
        }

        /* Action buttons */
        .action-buttons {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 13px;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: 1.5px solid;
            text-decoration: none;
        }

        .btn-download {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #2563eb;
        }

        .btn-download:hover {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .btn-print {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #ea580c;
        }

        .btn-print:hover {
            background: #ea580c;
            border-color: #ea580c;
            color: #fff;
        }

        .btn-voucher {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #16a34a;
        }

        .btn-voucher:hover {
            background: #16a34a;
            border-color: #16a34a;
            color: #fff;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 24px;
        }

        .empty-state-icon {
            width: 72px; height: 72px;
            border-radius: 20px;
            background: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--red);
            font-size: 30px;
            margin: 0 auto 16px;
        }

        .empty-state h3 { font-size: 16px; font-weight: 700; color: #333; margin-bottom: 6px; }
        .empty-state p  { font-size: 13.5px; color: #888; }

        /* =============== MODAL =============== */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #7f0000 0%, #dc2626 100%);
            border-bottom: none;
            padding: 20px 24px;
        }

        .modal-title {
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-close { filter: brightness(0) invert(1); opacity: 0.8; }

        .modal-body { padding: 24px; background: #fff; }

        .modal-field-label {
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 7px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .modal-field-label i { color: var(--red); font-size: 12px; }

        .modal-field-input {
            width: 100%;
            background: #fafafa;
            border: 1.5px solid #e8e8e8;
            border-radius: 10px;
            color: #1a1a1a;
            font-family: 'Poppins', sans-serif;
            font-size: 13.5px;
            padding: 11px 14px;
            outline: none;
            transition: all 0.25s;
            margin-bottom: 16px;
        }

        .modal-field-input:focus {
            border-color: var(--red);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }

        .school-year-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .school-year-row input {
            flex: 1;
            background: #fafafa;
            border: 1.5px solid #e8e8e8;
            border-radius: 10px;
            color: #1a1a1a;
            font-family: 'Poppins', sans-serif;
            font-size: 13.5px;
            padding: 11px 14px;
            outline: none;
            transition: all 0.25s;
        }

        .school-year-row input:focus {
            border-color: var(--red);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }

        .school-year-row .sep {
            font-size: 16px;
            color: #aaa;
            font-weight: 600;
        }

        /* File upload zone */
        .file-upload-zone {
            border: 2px dashed #e8e8e8;
            border-radius: 12px;
            padding: 22px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s;
            margin-bottom: 16px;
            position: relative;
        }

        .file-upload-zone:hover { border-color: var(--red); background: #fff5f5; }

        .file-upload-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .file-upload-zone .upload-icon {
            font-size: 26px;
            color: #ccc;
            margin-bottom: 8px;
            transition: color 0.25s;
            display: block;
        }

        .file-upload-zone:hover .upload-icon { color: var(--red); }
        .file-upload-zone p { font-size: 13px; color: #888; margin: 0; }
        .file-upload-zone span { font-size: 12px; color: #bbb; }

        .error-msg {
            display: none;
            font-size: 12px;
            color: var(--red);
            margin-top: -12px;
            margin-bottom: 12px;
            display: none;
        }

        .modal-footer {
            background: #fafafa;
            border-top: 1px solid #f0f0f0;
            padding: 16px 24px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-modal-close {
            padding: 9px 20px;
            background: #f3f4f6;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            color: #555;
            font-family: 'Poppins', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-modal-close:hover { background: #fee2e2; border-color: #fecaca; color: var(--red); }

        .btn-modal-submit {
            padding: 9px 24px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 3px 12px rgba(220,38,38,0.2);
        }

        .btn-modal-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(220,38,38,0.3);
        }

        /* Footer */
        .dashboard-footer {
            background: #fff;
            border-top: 1px solid #f0f0f0;
            color: #888;
            padding: 18px 28px;
            font-size: 12.5px;
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        .dashboard-footer .footer-left { display: flex; align-items: center; gap: 8px; }
        .dashboard-footer .footer-logo { width: 22px; height: 22px; object-fit: contain; opacity: 0.6; }
        .dashboard-footer .footer-copy { font-size: 12.5px; color: #aaa; font-weight: 500; }
        .dashboard-footer .footer-copy span { color: var(--red); font-weight: 600; }
        .dashboard-footer .footer-links { display: flex; align-items: center; gap: 6px; }
        .dashboard-footer a { color: #888; text-decoration: none; font-weight: 500; font-size: 12.5px; transition: color 0.2s; }
        .dashboard-footer a:hover { color: var(--red); }
        .dashboard-footer .divider { color: #e5e5e5; margin: 0 2px; }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        @media (max-width: 900px) {
            .sidebar {
                width: var(--sidebar-w);
                transform: translateX(-100%);
                transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
            }
            .sidebar.mobile-open { transform: translateX(0); }
            .sidebar-overlay.active { display: block; }
            .main-content { margin-left: 0 !important; }
            .page-content { padding: 18px; }
            .topbar-title { display: none; }
            .info-banner { flex-direction: column; text-align: center; }
            .action-buttons { flex-direction: column; align-items: flex-start; }
        }
        .school-year-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
}

.school-year-row input {
    flex: 1;
    min-width: 0; /* prevents overflow in grid */
    background: #fafafa;
    border: 1.5px solid #e8e8e8;
    border-radius: 10px;
    color: #1a1a1a;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    padding: 11px 10px;
    outline: none;
    transition: all 0.25s;
}

.school-year-row input:focus {
    border-color: var(--red);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
}

.school-year-row .sep {
    font-size: 16px;
    color: #aaa;
    font-weight: 600;
    flex-shrink: 0;
}
    </style>
</head>

<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- =============== SIDEBAR =============== -->
<div class="sidebar" id="sidebar">

    <a href="#" class="sidebar-brand">
        <img src="/images/final-puptg_logo-ojtims_nbg.png" alt="InternConnect">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Intern<span>Connect</span></span>
            <span class="sidebar-brand-sub">OJT IMS</span>
        </div>
    </a>

    <a href="{{ url('/student/accountinfo') }}" class="sidebar-user">
        <div class="user-avatar"><i class="fa fa-user"></i></div>
        <div class="user-info">
            <span class="user-name">{{ $user->full_name }}</span>
            <span class="user-role">Student</span>
        </div>
    </a>

    <nav class="sidebar-nav">
        <a href="{{ url('/student/home') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-home"></i></span>
            <span class="nav-label">Home</span>
            <span class="tooltip-label">Home</span>
        </a>
        <a href="{{ url('/student/ojtinfo') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-layer-group"></i></span>
            <span class="nav-label">OJT Information</span>
            <span class="tooltip-label">OJT Information</span>
        </a>
        <a href="{{ url('/student/class') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-clipboard"></i></span>
            <span class="nav-label">Class</span>
            <span class="tooltip-label">Class</span>
        </a>
        <a href="{{ url('/student/files') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-download"></i></span>
            <span class="nav-label">Downloadable Files</span>
            <span class="tooltip-label">Downloadable Files</span>
        </a>
        <a href="{{ url('/student/MOA') }}" class="nav-item active">
            <span class="nav-icon"><i class="fa fa-file-contract"></i></span>
            <span class="nav-label">MOA</span>
            <span class="tooltip-label">MOA</span>
        </a>
        <a href="{{ url('/student/MOA') }}" class="nav-sub-item active">
            <i class="fa fa-circle" style="font-size:6px;"></i>
            Notarized MOA
        </a>
        <a href="{{ url('/student/requirements') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-cloud-upload-alt"></i></span>
            <span class="nav-label">Requirements</span>
            <span class="tooltip-label">Requirements</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ url('/login') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-sign-out-alt"></i></span>
            <span class="nav-label">Log Out</span>
            <span class="tooltip-label">Log Out</span>
        </a>
    </div>
</div>

<!-- =============== MAIN CONTENT =============== -->
<div class="main-content" id="mainContent">

    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle">
                <i class="fa fa-bars"></i>
            </button>
            <span class="topbar-title">
                On-the-Job Training <span>Information Management System</span>
            </span>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">
                <i class="fa fa-graduation-cap"></i>
                Student Portal
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Memorandum of <span>Agreement</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/student/home') }}"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>MOA</span>
                </div>
            </div>
            <button class="btn-add-moa" data-bs-toggle="modal" data-bs-target="#addMoaModal">
                <i class="fa fa-plus-circle"></i> Add Notarized MOA
            </button>
        </div>

        <!-- Info Banner -->
        <div class="info-banner">
            <div class="info-banner-icon">
                <i class="fa fa-file-contract"></i>
            </div>
            <div class="info-banner-text">
                <h3>Notarized MOA Submissions</h3>
                <p>Upload your company's Memorandum of Agreement here. Ensure the document is properly notarized before submission. You may download or print your MOA details anytime.</p>
            </div>
        </div>

        <!-- MOA Table Card -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-left">
                    <div class="header-icon"><i class="fa fa-building"></i></div>
                    <div>
                        <h2>Notarized MOA Records</h2>
                        <p>All submitted Memoranda of Agreement with partner companies</p>
                    </div>
                </div>
            </div>

            <div class="table-card-body">

                <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script>
                    $(document).ready(function () {
                        $('#moaTable').DataTable({
                            "order": [[0, 'asc']]
                        });
                    });
                </script>

                @if($companies->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fa fa-file-contract"></i>
                        </div>
                        <h3>No MOA Submitted Yet</h3>
                        <p>Click "Add Notarized MOA" to submit your first company MOA.</p>
                    </div>
                @else
                <table id="moaTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <th>School Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                        <tr>
                            <td>
                                <div class="company-cell">
                                    <div class="company-avatar">
                                        {{ strtoupper(substr($company->company_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="company-name-text">{{ $company->company_name }}</div>
                                        <div class="company-sub">{{ $company->company_address }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:6px; font-size:13px;">
                                    <i class="fa fa-phone" style="color:var(--red); font-size:11px;"></i>
                                    {{ $company->companyNo }}
                                </div>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:6px; font-size:13px;">
                                    <i class="fa fa-envelope" style="color:var(--red); font-size:11px;"></i>
                                    {{ $company->company_email }}
                                </div>
                            </td>
                            <td>
                                <span class="year-badge">
                                    <i class="fa fa-calendar-alt"></i>
                                    {{ $company->school_year }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ url('/moa/download', $company->file) }}" class="btn-action btn-download">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                    <button class="btn-action btn-print"
                                        onclick="openViewModal('{{ route('print-data', ['company' => $company->id]) }}')">
                                        <i class="fa fa-print"></i> Print Details
                                    </button>
                                    <button class="btn-action btn-voucher"
                                        onclick="openViewModal1('{{ route('voucher', ['company' => $company->id]) }}')">
                                        <i class="fa fa-receipt"></i> Voucher
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <div class="footer-left">
            <img src="/images/final-puptg_logo-ojtims_nbg.png" class="footer-logo" alt="PUP">
            <span class="footer-copy">
                © 1998–2026 <span>Polytechnic University of the Philippines</span>
            </span>
        </div>
        <div class="footer-links">
            <a href="https://www.pup.edu.ph/" target="_blank">
                <i class="fa fa-external-link-alt" style="font-size:10px; margin-right:3px;"></i>
                PUP Website
            </a>
            <span class="divider">|</span>
            <a href="{{ url('/terms') }}">Terms of Use</a>
            <span class="divider">|</span>
            <a href="{{ url('/privacy') }}">Privacy Statement</a>
        </div>
    </footer>

</div>

<!-- =============== ADD MOA MODAL =============== -->
<div class="modal fade" id="addMoaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-file-contract"></i> Submit Notarized MOA
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ url('/companyCreate') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <!-- Two-column grid -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0 20px;">

                        <!-- LEFT COLUMN (4 fields) -->
                        <div>
                            <label class="modal-field-label">
                                <i class="fa fa-building"></i> Company Name
                            </label>
                            <input class="modal-field-input" type="text" name="company_name"
                                placeholder="e.g. Acme Corporation" required>

                            <label class="modal-field-label">
                                <i class="fa fa-map-marker-alt"></i> Company Address
                            </label>
                            <input class="modal-field-input" type="text" name="company_address"
                                placeholder="e.g. 123 Main St, Manila" required>

                            <label class="modal-field-label">
                                <i class="fa fa-user-tie"></i> Company Representative
                            </label>
                            <input class="modal-field-input" type="text" name="company_rep"
                                placeholder="e.g. Juan dela Cruz" required>

                            <label class="modal-field-label">
                                <i class="fa fa-phone"></i> Company Number
                            </label>
                            <input class="modal-field-input" type="text" name="companyNo"
                                placeholder="e.g. 09XX-XXX-XXXX" required>
                        </div>

                        <!-- RIGHT COLUMN (3 fields) -->
                        <div>
                            <label class="modal-field-label">
                                <i class="fa fa-envelope"></i> Company Email
                            </label>
                            <input class="modal-field-input" type="text" name="company_email"
                                placeholder="e.g. info@company.com" required>

                            <label class="modal-field-label">
                                <i class="fa fa-calendar-alt"></i> School Year
                            </label>
                            <div class="school-year-row">
                                <input type="text" name="school_year_start"
                                    placeholder="Start (e.g. 2024)" required>
                                <span class="sep">–</span>
                                <input type="text" name="school_year_end"
                                    placeholder="End (e.g. 2025)" required>
                            </div>

                            <!-- Info notice card -->
                            <div style="
                                background: #fff5f5;
                                border: 1px solid #fecaca;
                                border-left: 3px solid var(--red);
                                border-radius: 10px;
                                padding: 12px 14px;
                                margin-top: 6px;
                            ">
                                <div style="font-size: 12px; font-weight: 700; color: var(--red); margin-bottom: 5px;">
                                    <i class="fa fa-info-circle"></i> Reminder
                                </div>
                                <div style="font-size: 11.5px; color: #777; line-height: 1.6;">
                                    Ensure your MOA is properly <strong>notarized</strong> before submitting.
                                    Accepted formats: <strong>PDF, DOC, DOCX, JPG, PNG</strong>.
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- DIVIDER -->
                    <div style="height: 1px; background: #f0f0f0; margin: 20px 0;"></div>

                    <!-- MOA DOCUMENT — full width below -->
                    <label class="modal-field-label">
                        <i class="fa fa-paperclip"></i> MOA Document
                    </label>
                    <div class="file-upload-zone" id="moaDropZone">
                        <input type="file" name="file" id="moaFileInput" required>
                        <i class="fa fa-cloud-upload-alt upload-icon"></i>
                        <p id="moaFileLabel">Click or drag your notarized MOA file here</p>
                        <span>Supported: PDF, DOC, DOCX, JPG, PNG</span>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-paper-plane me-1"></i> Submit MOA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =============== VIEW / PRINT MODAL =============== -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-eye"></i> Document Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 0; height: 75vh;">
                <iframe id="viewIframe" style="width:100%; height:100%; border:none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Close
                </button>
                <button type="button" class="btn-modal-submit" onclick="printRegularPreview()">
                    <i class="fa fa-print me-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<script>
    // Sidebar toggle
    const sidebar     = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle  = document.getElementById('menuToggle');
    const overlay     = document.getElementById('sidebarOverlay');

    menuToggle.addEventListener('click', function () {
        const isMobile = window.innerWidth <= 900;
        if (isMobile) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });

    // File label update
    document.getElementById('moaFileInput').addEventListener('change', function () {
        const label = document.getElementById('moaFileLabel');
        label.textContent = this.files.length > 0
            ? this.files[0].name
            : 'Click or drag your notarized MOA file here';
    });

    // View modal
    function openViewModal(url) {
        document.getElementById('viewIframe').src = url;
        new bootstrap.Modal(document.getElementById('viewModal')).show();
    }

    function openViewModal1(url) {
        document.getElementById('viewIframe').src = url;
        new bootstrap.Modal(document.getElementById('viewModal')).show();
    }

    function printRegularPreview() {
        document.getElementById('viewIframe').contentWindow.print();
    }

    // Form validation
    $(document).ready(function () {
        function validateForm() {
            let valid = true;
            $('input[required]').each(function () {
                const errorId = $(this).attr('name') + '-error';
                if ($(this).val() === '') {
                    valid = false;
                    $('#' + errorId).show();
                } else {
                    $('#' + errorId).hide();
                }
            });
            return valid;
        }

        $('.btn-modal-submit').click(function (e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    });
</script>

</body>
</html>