<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternConnect - Notarized MOA</title>
    <link rel="shortcut icon" href="/images/final-puptg_logo-ojtims_nbg.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student_moa-responsive.css') }}">

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
            position: relative; /* Add this */
            z-index: 110;       /* Add this */
        }

        .menu-toggle:hover { background: #fee2e2; color: var(--red); }

        .darkmode-toggle {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            font-size: 16px;
            transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
            flex-shrink: 0;
            padding: 0;
            position: relative; /* Add this */
            z-index: 110;       /* Add this */
        }

        .darkmode-toggle:hover {
            background: #fee2e2; color: var(--red); border-color: #fecaca;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220,38,38,0.2);
        }

        .darkmode-toggle:active { transform: scale(0.95); }

        body.dark-mode .darkmode-toggle {
            background: #2a2a2a;
            border-color: #3a3a3a;
            color: #e8e8e8;
        }

        body.dark-mode .darkmode-toggle:hover {
            background: rgba(220,38,38,0.2);
            color: #ff6b6b;
            border-color: rgba(220,38,38,0.3);
            box-shadow: 0 6px 16px rgba(220,38,38,0.3);
            transform: translateY(-2px);
        }

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
            position: relative; /* Add this */
            z-index: 50;        /* Add this */
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

        .moa-table-wrap { overflow: visible; }
        #moaTable {
            min-width: 860px;
            table-layout: fixed;
        }
        #moaTable th,
        #moaTable td {
            white-space: normal !important;
            overflow-wrap: anywhere;
        }
        #moaTable th:nth-child(1), #moaTable td:nth-child(1) { width: 30%; }
        #moaTable th:nth-child(2), #moaTable td:nth-child(2) { width: 15%; }
        #moaTable th:nth-child(3), #moaTable td:nth-child(3) { width: 20%; }
        #moaTable th:nth-child(4), #moaTable td:nth-child(4) { width: 14%; }
        #moaTable th:nth-child(5), #moaTable td:nth-child(5) { width: 21%; }

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

        .table-card {
            overflow: hidden;
        }

        .table-card-body .dataTables_scroll {
            width: 100%;
        }

        .table-card-body .dataTables_scrollBody {
            overflow-x: auto !important;
            overflow-y: hidden !important;
            -webkit-overflow-scrolling: touch;
        }

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
            min-width: 0;
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

        .company-name-text { font-weight: 600; color: #1a1a1a; font-size: 13.5px; overflow-wrap: anywhere; }
        .company-sub { font-size: 11.5px; color: #aaa; margin-top: 1px; overflow-wrap: anywhere; }

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
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
            margin-bottom: 16px;
            width: 100%;
        }

        .school-year-row input {
            flex: none;
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
        }

        .school-year-row input:focus {
            border-color: var(--red);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.07);
        }

        .school-year-row .sep {
            display: none;
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
    pointer-events: none; /* Add this to prevent phantom blocking */
}

@media (max-width: 900px) {
    .sidebar {
        width: var(--sidebar-w);
        transform: translateX(-100%);
        transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
        pointer-events: none; /* Disables clicks when sidebar is hidden */
    }
    .sidebar.mobile-open { 
        transform: translateX(0); 
        pointer-events: auto; /* Re-enables clicks when open */
    }
    .sidebar-overlay.active { 
        display: block; 
        pointer-events: auto; /* Re-enables clicks when open */
    }
    .main-content { margin-left: 0 !important; }
    .page-content { padding: 18px; }
    .topbar-title { display: none; }
    .info-banner { flex-direction: column; text-align: center; }
    .action-buttons { flex-direction: column; align-items: flex-start; }
}
.school-year-row {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
    margin-bottom: 16px;
    width: 100%;
}

.school-year-row input {
    flex: none;
    width: 100%;
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
    display: none;
    font-size: 16px;
    color: #aaa;
    font-weight: 600;
    flex-shrink: 0;
}

@media (min-width: 577px) {
    .school-year-row {
        flex-direction: row;
        align-items: center;
        gap: 8px;
    }

    .school-year-row input {
        flex: 1;
        width: auto;
    }

    .school-year-row .sep {
        display: inline;
    }
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
            <span class="sidebar-brand-sub">OJTIMS</span>
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
            <span class="nav-label">Notarized MOA</span>
            <span class="tooltip-label">Notarized MOA</span>
        </a>
        <a href="{{ url('/student/requirements') }}" class="nav-item">
            <span class="nav-icon"><i class="fa fa-cloud-upload-alt"></i></span>
            <span class="nav-label">Requirements</span>
            <span class="tooltip-label">Requirements</span>
        </a>
            <a href="{{ url('/student/evaluation') }}" class="nav-item{{ request()->is('student/evaluation*') ? ' active' : '' }}">
            <span class="nav-icon"><i class="fa fa-star-half-alt"></i></span>
            <span class="nav-label">Evaluation</span>
            <span class="tooltip-label">Evaluation</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ url('/logout') }}" class="nav-item">
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
            <button class="darkmode-toggle" id="darkmodeToggle" title="Toggle Dark Mode">
                <i class="fa fa-moon" id="darkmodeIcon"></i>
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
                <h1>Notarized <span>MOA</span></h1>
                <div class="breadcrumb">
                    <a href="{{ url('/student/home') }}"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right"></i>
                    <span>Notarized MOA</span>
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
                <h3>Notarized MOA Submission</h3>
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

            <div class="table-card-body moa-table-wrap">

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
                        @php $isOwner = $company->uploader_name === $user->full_name; @endphp
                        <tr>
                            <td>
                                <div class="company-cell">
                                    <div class="company-avatar">
                                        {{ strtoupper(substr($company->company_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="company-name-text">{{ $company->company_name }}</div>
                                        <div class="company-sub">
                                            {{ $company->company_address }}
                                            @if(!$isOwner)
                                                <span style="display:inline-flex; align-items:center; gap:5px; margin-left:8px; padding:3px 8px; border-radius:999px; background:#eff6ff; color:#2563eb; font-size:11px; font-weight:700;">
                                                    <i class="fa fa-link"></i> Linked
                                                </span>
                                            @endif
                                        </div>
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
                                    <button type="button"
                                        class="btn-action btn-voucher"
                                        onclick="openVoucherModal('{{ route('voucher', $company->id) }}')">
                                        <i class="fa fa-ticket-alt"></i> Voucher
                                    </button>
                                    <button class="btn-action btn-print"
                                        onclick="openPdfPreview('{{ asset('assets/' . $company->file) }}')">
                                        <i class="fa fa-print"></i> Print PDF
                                    </button>
                                    @if($isOwner)
                                        <button type="button"
                                            class="btn-action"
                                            style="background:#eff6ff; border-color:#bfdbfe; color:#2563eb;"
                                            data-update-url="{{ route('student.moa.update', $company->id) }}"
                                            data-company-name="{{ e($company->company_name) }}"
                                            data-company-address="{{ e($company->company_address) }}"
                                            data-company-rep="{{ e($company->company_rep) }}"
                                            data-company-no="{{ e($company->companyNo) }}"
                                            data-company-email="{{ e($company->company_email) }}"
                                            data-school-year="{{ e($company->school_year) }}"
                                            data-valid-until="{{ $company->valid_until ? \Carbon\Carbon::parse($company->valid_until)->format('Y-m-d') : '' }}"
                                            data-file-name="{{ e($company->file) }}"
                                            onclick="openEditMoaModal(this)">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                    @endif
                                    <button type="button" class="btn-action" style="border:1.5px solid #fecaca; color:#dc2626; background:#fff;"
                                        onclick="confirmStudentRemove({{ $company->id }}, '{{ addslashes($company->company_name) }}', {{ $isOwner ? 'true' : 'false' }})">
                                        <i class="fa fa-trash"></i> {{ $isOwner ? 'Remove' : 'Unlink' }}
                                    </button>
                                    <form id="student-remove-form-{{ $company->id }}" action="{{ route('student.moa.remove', $company->id) }}" method="POST" style="display:none;">
                                        @csrf
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                <script>
                    $(document).ready(function () {
                        $('#moaTable').DataTable({
                            scrollX: true,
                            autoWidth: false,
                            order: [[0, 'asc']]
                        });
                    });
                </script>
                @endif

            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="dashboard-footer" style="justify-content: center; flex-direction: column; align-items: center; text-align: center; gap: 6px;">
    <div style="display:flex; align-items:center; gap:8px;">
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

@php
    $schoolYearBase = now()->year;
    $schoolYearOptions = range($schoolYearBase - 5, $schoolYearBase + 5);
    $selectedCreateStartYear = old('school_year_start', $schoolYearBase);
    $selectedCreateEndYear = old('school_year_end', $selectedCreateStartYear + 1);
@endphp

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

            <form id="linkExistingMoaForm" action="{{ route('student.moa.link') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="company_id" id="linkExistingMoaCompanyId">
            </form>

            <form id="studentMoaForm" action="{{ url('/companyCreate') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div style="margin-bottom: 20px; padding: 16px; border: 1px solid #fde2e2; border-radius: 14px; background: linear-gradient(180deg, #fffefe 0%, #fff7f7 100%);">
                        <div style="display:flex; align-items:flex-start; gap:12px; margin-bottom: 12px;">
                            <div style="width:42px; height:42px; border-radius:12px; background:#fee2e2; color:var(--red); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="fa fa-link"></i>
                            </div>
                            <div>
                                <div style="font-size:16px; font-weight:800; color:#111827;">Use Existing MOA First</div>
                                <div style="font-size:12.5px; color:#6b7280; line-height:1.6;">
                                    Search the company name below. If the notarized MOA is already in the system, you can link it to your account instead of uploading a duplicate file.
                                </div>
                            </div>
                        </div>

                        <input type="text" id="existingMoaSearch" class="modal-field-input" placeholder="Search company name...">

                        <div id="existingMoaList" style="margin-top: 12px; max-height: 220px; overflow-y: auto; display: grid; gap: 10px;">
                            @forelse ($availableLinkableCompanies as $linkableCompany)
                                <div class="existing-moa-item" data-company-name="{{ strtolower($linkableCompany->company_name) }}">
                                    <div style="display:flex; justify-content:space-between; gap:14px; align-items:center; padding:14px; border:1px solid #f1d5d5; border-radius:12px; background:#fff;">
                                        <div style="min-width:0;">
                                            <div style="font-size:14px; font-weight:800; color:#111827;">{{ $linkableCompany->company_name }}</div>
                                            <div style="font-size:12px; color:#6b7280; margin-top:4px;">{{ $linkableCompany->company_address }}</div>
                                            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:8px;">
                                                <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 8px; border-radius:999px; background:#fef3c7; color:#92400e; font-size:11px; font-weight:700;">
                                                    <i class="fa fa-calendar-alt"></i> {{ $linkableCompany->school_year }}
                                                </span>
                                                @if(!empty($linkableCompany->course))
                                                    <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 8px; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-size:11px; font-weight:700;">
                                                        <i class="fa fa-graduation-cap"></i> {{ $linkableCompany->course }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div style="display:flex; flex-direction:column; gap:8px; align-items:stretch; min-width: 150px;">
                                            <button type="button"
                                                class="btn-modal-close view-btn"
                                                data-url="{{ asset('assets/' . $linkableCompany->file) }}"
                                                style="justify-content:center; padding-inline: 16px; white-space: nowrap;">
                                                <i class="fa fa-eye me-1"></i> View MOA
                                            </button>
                                            <button type="button"
                                                class="btn-modal-submit existing-moa-link-btn"
                                                data-company-id="{{ $linkableCompany->id }}"
                                                style="justify-content:center; padding-inline: 16px; white-space: nowrap;">
                                                <i class="fa fa-link me-1"></i> Use This MOA
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div id="existingMoaEmptyState" style="padding:14px; border:1px dashed #f3b3b3; border-radius:12px; color:#6b7280; font-size:12.5px; background:#fff;">
                                    No existing MOA is available to link right now. You can continue with a new upload below.
                                </div>
                            @endforelse
                        </div>

                        @if ($availableLinkableCompanies->isNotEmpty())
                            <div id="existingMoaNoResults" style="display:none; margin-top:12px; padding:14px; border:1px dashed #f3b3b3; border-radius:12px; color:#6b7280; font-size:12.5px; background:#fff;">
                                No matching company found. You can continue with a new upload below.
                            </div>
                        @endif
                    </div>

                    <div style="display:flex; align-items:center; gap:10px; margin: 0 0 18px;">
                        <div style="flex:1; height:1px; background:#ececec;"></div>
                        <span style="font-size:11px; font-weight:800; color:#9ca3af; letter-spacing:0.12em;">OR UPLOAD A NEW MOA</span>
                        <div style="flex:1; height:1px; background:#ececec;"></div>
                    </div>

                    <!-- Two-column grid -->
                    <div class="moa-form-grid">

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
                                placeholder="e.g. 09XX-XXX-XXXX or N/A">
                        </div>

                        <!-- RIGHT COLUMN (3 fields) -->
                        <div>
                            <label class="modal-field-label">
                                <i class="fa fa-envelope"></i> Company Email
                            </label>
                            <input class="modal-field-input" type="text" name="company_email"
                                placeholder="e.g. info@company.com" required>

                            <label class="modal-field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
                                <span><i class="fa fa-calendar-alt"></i> School Year</span>
                                <span style="font-size: 11.5px; color: #777; font-weight: 400;">
                                    Select the current school year, example: <strong>2025-2026</strong>.
                                </span>
                            </label>
                            <div class="school-year-row">
                                <select name="school_year_start" id="schoolYearStart" class="modal-field-input" required>
                                    @foreach ($schoolYearOptions as $year)
                                        <option value="{{ $year }}" {{ (string) $selectedCreateStartYear === (string) $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="sep">–</span>
                                <select name="school_year_end" id="schoolYearEnd" class="modal-field-input" required>
                                    <option value="{{ $selectedCreateEndYear }}" selected>{{ $selectedCreateEndYear }}</option>
                                </select>
                            </div>

                            <label class="modal-field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap; margin-top: 14px;">
                                <span><i class="fa fa-hourglass-end"></i> Validity Period</span>
                                <span style="font-size: 11.5px; color: #777; font-weight: 400;">
                                    Select the MOA expiry date.
                                </span>
                            </label>
                            <input class="modal-field-input" type="date" name="valid_until" required>

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
                                    Accepted format: <strong>PDF only</strong>.
                                    Max file size: <strong>2 MB</strong>.
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
                        <input type="file" name="file" id="moaFileInput" data-max-size-mb="30" accept=".pdf,application/pdf" required>
                        <i class="fa fa-cloud-upload-alt upload-icon"></i>
                        <p id="moaFileLabel">Click or drag your notarized MOA file here</p>
                        <span>Supported: PDF only | Max file size: 30 MB</span>
                        <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>
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

<!-- =============== EDIT MOA MODAL =============== -->
<div class="modal fade" id="editMoaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-edit"></i> Edit Notarized MOA
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editMoaForm" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="moa-form-grid">
                        <div>
                            <label class="modal-field-label">
                                <i class="fa fa-building"></i> Company Name
                            </label>
                            <input class="modal-field-input" type="text" name="company_name" id="editCompanyName" required>

                            <label class="modal-field-label">
                                <i class="fa fa-map-marker-alt"></i> Company Address
                            </label>
                            <input class="modal-field-input" type="text" name="company_address" id="editCompanyAddress" required>

                            <label class="modal-field-label">
                                <i class="fa fa-user-tie"></i> Company Representative
                            </label>
                            <input class="modal-field-input" type="text" name="company_rep" id="editCompanyRep" required>

                            <label class="modal-field-label">
                                <i class="fa fa-phone"></i> Company Number
                            </label>
                            <input class="modal-field-input" type="text" name="companyNo" id="editCompanyNo" placeholder="e.g. 09XX-XXX-XXXX or N/A">
                        </div>

                        <div>
                            <label class="modal-field-label">
                                <i class="fa fa-envelope"></i> Company Email
                            </label>
                            <input class="modal-field-input" type="text" name="company_email" id="editCompanyEmail" required>

                            <label class="modal-field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
                                <span><i class="fa fa-calendar-alt"></i> School Year</span>
                                <span style="font-size: 11.5px; color: #777; font-weight: 400;">
                                    Select the current school year, example: <strong>2025-2026</strong>.
                                </span>
                            </label>
                            <div class="school-year-row">
                                <select name="school_year_start" id="editSchoolYearStart" class="modal-field-input" required>
                                    @foreach ($schoolYearOptions as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                                <span class="sep">-</span>
                                <select name="school_year_end" id="editSchoolYearEnd" class="modal-field-input" required></select>
                            </div>

                            <label class="modal-field-label" style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap; margin-top: 14px;">
                                <span><i class="fa fa-hourglass-end"></i> Validity Period</span>
                                <span style="font-size: 11.5px; color: #777; font-weight: 400;">
                                    Select the MOA expiry date.
                                </span>
                            </label>
                            <input class="modal-field-input" type="date" name="valid_until" id="editValidUntil" required>

                            <div style="
                                background: #eff6ff;
                                border: 1px solid #bfdbfe;
                                border-left: 3px solid #2563eb;
                                border-radius: 10px;
                                padding: 12px 14px;
                                margin-top: 6px;
                            ">
                                <div style="font-size: 12px; font-weight: 700; color: #2563eb; margin-bottom: 5px;">
                                    <i class="fa fa-info-circle"></i> Optional PDF Replacement
                                </div>
                                <div style="font-size: 11.5px; color: #555; line-height: 1.6;" id="editMoaCurrentFile">
                                    Leave the file empty if you only need to update the company details.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="height: 1px; background: #f0f0f0; margin: 20px 0;"></div>

                    <label class="modal-field-label">
                        <i class="fa fa-paperclip"></i> Replace MOA Document
                    </label>
                    <div class="file-upload-zone" id="editMoaDropZone">
                        <input type="file" name="file" id="editMoaFileInput" data-max-size-mb="30" accept=".pdf,application/pdf">
                        <i class="fa fa-cloud-upload-alt upload-icon"></i>
                        <p id="editMoaFileLabel">Leave empty to keep the current notarized MOA PDF</p>
                        <span>Supported: PDF only | Max file size: 30 MB</span>
                        <div class="file-size-error" style="display:none; margin-top:6px; color:#b91c1c; font-size:12px; font-weight:600;"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fa fa-save me-1"></i> Save Changes
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

<!-- =============== VOUCHER MODAL =============== -->
<div class="modal fade" id="voucherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-ticket-alt"></i> Voucher Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 0; height: 70vh; background:#f8fafc;">
                <iframe id="voucherIframe" style="width:100%; height:100%; border:none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Close
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
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle = document.getElementById('menuToggle');
    const overlay = document.getElementById('sidebarOverlay');

    function closeMobileSidebar() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    }

    function openMobileSidebar() {
        sidebar.classList.add('mobile-open');
        overlay.classList.add('active');
    }

    menuToggle.addEventListener('click', function (event) {
        const isMobile = window.innerWidth <= 900;

        if (isMobile) {
            event.stopPropagation();

            if (sidebar.classList.contains('mobile-open')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    });

    overlay.addEventListener('click', closeMobileSidebar);

    document.addEventListener('click', function (event) {
        if (window.innerWidth > 900 || !sidebar.classList.contains('mobile-open')) {
            return;
        }

        if (sidebar.contains(event.target) || menuToggle.contains(event.target)) {
            return;
        }

        closeMobileSidebar();
    });

// ✅ ADD THIS (IMPORTANT FIX)
    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) {
            closeMobileSidebar();
        }
    });

    function confirmStudentRemove(companyId, companyName, isOwner) {
        const title = isOwner ? 'Remove MOA?' : 'Unlink MOA?';
        const html = isOwner
            ? 'This will remove your notarized MOA record for <strong>' + companyName + '</strong>.'
            : 'This will unlink <strong>' + companyName + '</strong> from your account.';
        const confirmText = isOwner ? 'Yes, remove it' : 'Yes, unlink it';

        Swal.fire({
            title: title,
            html: html,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: confirmText,
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed) {
                document.getElementById('student-remove-form-' + companyId).submit();
            }
        });
    }

    // PDF preview / print modal
    function openPdfPreview(url) {
        document.getElementById('viewIframe').src = url;
        new bootstrap.Modal(document.getElementById('viewModal')).show();
    }

    function openVoucherModal(url) {
        document.getElementById('voucherIframe').src = url;
        new bootstrap.Modal(document.getElementById('voucherModal')).show();
    }

    function syncSchoolYearEnd(startId, endId, selectedEndYear = null) {
        const startSelect = document.getElementById(startId);
        const endSelect = document.getElementById(endId);

        if (!startSelect || !endSelect || !startSelect.value) {
            return;
        }

        const startYear = parseInt(startSelect.value, 10);

        if (Number.isNaN(startYear)) {
            return;
        }

        const endYear = selectedEndYear ? parseInt(selectedEndYear, 10) : startYear + 1;
        endSelect.innerHTML = '';

        const option = document.createElement('option');
        option.value = String(endYear);
        option.textContent = String(endYear);
        option.selected = true;
        endSelect.appendChild(option);
        endSelect.value = String(endYear);
    }

    function openEditMoaModal(button) {
        const form = document.getElementById('editMoaForm');
        const schoolYear = (button.dataset.schoolYear || '').split('-');
        const currentFile = button.dataset.fileName || '';

        form.action = button.dataset.updateUrl;
        document.getElementById('editCompanyName').value = button.dataset.companyName || '';
        document.getElementById('editCompanyAddress').value = button.dataset.companyAddress || '';
        document.getElementById('editCompanyRep').value = button.dataset.companyRep || '';
        document.getElementById('editCompanyNo').value = button.dataset.companyNo || '';
        document.getElementById('editCompanyEmail').value = button.dataset.companyEmail || '';
        document.getElementById('editSchoolYearStart').value = schoolYear[0] || '';
        syncSchoolYearEnd('editSchoolYearStart', 'editSchoolYearEnd', schoolYear[1] || '');
        document.getElementById('editValidUntil').value = button.dataset.validUntil || '';
        document.getElementById('editMoaFileInput').value = '';
        document.getElementById('editMoaFileLabel').textContent = 'Leave empty to keep the current notarized MOA PDF';
        document.getElementById('editMoaCurrentFile').textContent = currentFile
            ? 'Current file: ' + currentFile + '. Leave the file empty if you only need to update the company details.'
            : 'Leave the file empty if you only need to update the company details.';

        new bootstrap.Modal(document.getElementById('editMoaModal')).show();
    }

    function printRegularPreview() {
        document.getElementById('viewIframe').contentWindow.print();
    }

    function bindPdfInputValidation(inputId, labelId, emptyLabel) {
        const input = document.getElementById(inputId);
        if (!input) {
            return;
        }

        input.addEventListener('change', function () {
            const label = document.getElementById(labelId);
            const file = this.files.length > 0 ? this.files[0] : null;

            if (file && !file.name.toLowerCase().endsWith('.pdf')) {
                this.value = '';
                label.textContent = emptyLabel;
                Swal.fire({
                    icon: 'error',
                    title: 'PDF only',
                    text: 'Please upload the notarized MOA as a PDF file.',
                    confirmButtonColor: '#d32f2f',
                });
                return;
            }

            label.textContent = file ? file.name : emptyLabel;
        });
    }

    // Form validation
    $(document).ready(function () {
        function validateForm($form) {
            let valid = true;
            $form.find('input[required]').each(function () {
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

        ['#studentMoaForm', '#editMoaForm'].forEach(function (selector) {
            $(selector).on('submit', function (e) {
                if (!validateForm($(this))) {
                    e.preventDefault();
                    return;
                }

                if (this.dataset.submitting === 'true') {
                    e.preventDefault();
                    return;
                }

                this.dataset.submitting = 'true';

                const submitButton = this.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';
                }
            });
        });

        bindPdfInputValidation('moaFileInput', 'moaFileLabel', 'Click or drag your notarized MOA file here');
        bindPdfInputValidation('editMoaFileInput', 'editMoaFileLabel', 'Leave empty to keep the current notarized MOA PDF');

        syncSchoolYearEnd('schoolYearStart', 'schoolYearEnd', @json($selectedCreateEndYear));
        syncSchoolYearEnd('editSchoolYearStart', 'editSchoolYearEnd');

        $('#schoolYearStart').on('change', function () {
            syncSchoolYearEnd('schoolYearStart', 'schoolYearEnd');
        });

        $('#editSchoolYearStart').on('change', function () {
            syncSchoolYearEnd('editSchoolYearStart', 'editSchoolYearEnd');
        });

        const existingMoaSearch = document.getElementById('existingMoaSearch');
        if (existingMoaSearch) {
            existingMoaSearch.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                const items = Array.from(document.querySelectorAll('.existing-moa-item'));
                let visibleCount = 0;

                items.forEach(function (item) {
                    const companyName = item.dataset.companyName || '';
                    const matches = companyName.includes(query);
                    item.style.display = matches ? '' : 'none';
                    if (matches) {
                        visibleCount += 1;
                    }
                });

                const noResults = document.getElementById('existingMoaNoResults');
                if (noResults) {
                    noResults.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            });
        }

        document.querySelectorAll('.existing-moa-link-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const companyIdInput = document.getElementById('linkExistingMoaCompanyId');
                const linkForm = document.getElementById('linkExistingMoaForm');

                if (!companyIdInput || !linkForm) {
                    return;
                }

                companyIdInput.value = this.dataset.companyId || '';
                this.disabled = true;
                this.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Linking...';
                linkForm.submit();
            });
        });
    });
    document.addEventListener('click', function(e) {
    const btn = e.target.closest('.view-btn');
    if (btn) {
        const url = btn.getAttribute('data-url');
        openPdfPreview(url);
    }
});

@if(session('showVoucherModal'))
    window.addEventListener('load', function () {
        openVoucherModal(@json(session('showVoucherModal')));
    });
@endif
</script>
<script src="{{ url('/assets/js/dark-mode.js') }}"></script>
<script src="{{ asset('assets/js/upload-size-guard.js') }}"></script>
<script src="{{ asset('assets/js/voice-input.js') }}"></script>
<script src="{{ url('/js/mobile-utils.js') }}"></script>
</body>
</html>
