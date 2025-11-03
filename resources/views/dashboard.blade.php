<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Better Clothes</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #0a0e27;
            color: #ffffff;
            overflow-x: hidden;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* sidebar */
        .sidebar {
            width: 55px;
            background: #0a0e27;
            border-right: 1px solid #1a1f3a;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
            gap: 30px;
        }

        .logo {
            width: 32px;
            height: 32px;
            background: #4d7cff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
        }

        .sidebar-icon {
            width: 24px;
            height: 24px;
            color: #6b7280;
            cursor: pointer;
            transition: color 0.3s;
        }

        .sidebar-icon:hover {
            color: #ffffff;
        }

        .sidebar-icon.active {
            color: #4d7cff;
        }

        /* main content */
        .main-content {
            flex: 1;
            padding: 20px 30px;
            overflow-y: auto;
        }

        /* header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background: #13182e;
            border-radius: 8px;
            padding: 10px 15px;
            width: 300px;
        }

        .search-bar input {
            background: none;
            border: none;
            color: #ffffff;
            outline: none;
            margin-left: 10px;
            width: 100%;
            font-size: 14px;
        }

        .search-bar input::placeholder {
            color: #6b7280;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            background: #13182e;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: background 0.3s;
        }

        .icon-btn:hover {
            background: #1a1f3a;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 18px;
            height: 18px;
            background: #ef4444;
            border-radius: 50%;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
        }

        .user-role {
            font-size: 11px;
            color: #6b7280;
        }

        /* cartas stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #4d7cff 0%, #5b8fff 100%);
            border-radius: 16px;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .stat-label {
            font-size: 14px;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-change {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 13px;
            opacity: 0.9;
        }

        .stat-subtitle {
            font-size: 13px;
            opacity: 0.9;
            margin-top: 5px;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-card {
            background: #13182e;
            border-radius: 16px;
            padding: 24px;
        }

        .chart-card.full-width {
            grid-column: 1 / -1;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
        }

        .chart-filter {
            background: #0a0e27;
            border: 1px solid #1a1f3a;
            color: #ffffff;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            outline: none;
        }

        /* Canvas Containers */
        .chart-card canvas {
            max-height: 300px;
            width: 100% !important;
            height: auto !important;
        }

        #medicalActivityChart {
            max-height: 300px;
        }

        #examsChart {
            max-height: 250px;
        }

        #pieChart {
            max-height: 280px;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            text-align: left;
            padding: 12px;
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 1px solid #1a1f3a;
        }

        tbody td {
            padding: 16px 12px;
            font-size: 14px;
            border-bottom: 1px solid #1a1f3a;
        }

        .product-name {
            font-weight: 500;
        }

        .product-desc {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .star {
            color: #fbbf24;
        }

        /* grid */
        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            gap: 20px;
        }

        .sales-cards {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sales-card {
            background: linear-gradient(135deg, #4d7cff 0%, #5b8fff 100%);
            border-radius: 16px;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        .sales-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .sales-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .sales-value {
            font-size: 28px;
            font-weight: 700;
        }

        /* botones export */
        .export-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }

        .export-btn {
            background: #10b981;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s;
        }

        .export-btn:hover {
            background: #059669;
        }

        .export-btn.excel {
            background: #22c55e;
        }

        .export-btn.excel:hover {
            background: #16a34a;
        }

        .week-selector {
            background: #13182e;
            border: 1px solid #1a1f3a;
            color: #ffffff;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            outline: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* responsivo */
        @media (max-width: 1400px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- sidebar -->
        <aside class="sidebar">
            <div class="logo">🔷</div>
            <svg class="sidebar-icon active" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
            </svg>
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
            </svg>
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
            </svg>
        </aside>

        <!-- main content -->
        <main class="main-content">
            <!-- header -->
            <header class="header">
                <div class="search-bar">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="Busca aquí...">
                </div>
                <div class="header-actions">
                    <div class="icon-btn">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="notification-badge">3</span>
                    </div>
                    <!--DB button-->
                    <a href="{{ route('db.index') }}" class="icon-btn" title="Inspector DB" style="margin-right:8px">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4m8-8h-4M4 12H0m16.24-6.24l-2.83 2.83M6.59 17.41l-2.83 2.83M6.59 6.59L3.76 3.76M19.41 17.41l2.83 2.83"/>
                        </svg>
                    </a>
                    <a href="{{ route('settings') }}" class="icon-btn" title="Configuración">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </a>
                    <div class="user-profile">
                        <div class="user-info">
                            <div class="user-name">Leo Causa</div>
                            <div class="user-role">CEO BETTER CLOTHES</div>
                        </div>
                        <div class="user-avatar"></div>
                    </div>

                    <!-- Logout form/button -->
                    <form method="POST" action="{{ route('logout') }}" style="display:inline"> 
                        @csrf
                        <button type="submit" class="icon-btn" title="Cerrar sesión" style="margin-left:8px">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </header>
            
            <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
                <div class="export-buttons">
                    <button class="week-selector">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Semana actual
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <button class="export-btn">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 2a.5.5 0 01.5.5V4h3v-.5a.5.5 0 011 0V4h1.5A1.5 1.5 0 0115.5 5.5v9a1.5 1.5 0 01-1.5 1.5h-9A1.5 1.5 0 013.5 14.5v-9A1.5 1.5 0 015 4h1.5v-.5A.5.5 0 018 2z"></path>
                        </svg>
                        Exportar PDF
                    </button>
                    <button class="export-btn excel">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"></path>
                            <path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"></path>
                        </svg>
                        Exportar Excel
                    </button>
                </div>
            </div>

            <!-- cartas stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Cantidad de Pacientes</div>
                    <div class="stat-value" id="pacientes-value">340290</div>
                    <div class="stat-change">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span id="pacientes-change">15%</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Indicador</div>
                    <div class="stat-value" id="indicador-value">342224</div>
                    <div class="stat-change">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span id="indicador-change">15%</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Indicador2</div>
                    <div class="stat-value" id="indicador2-value">23432</div>
                    <div class="stat-subtitle" id="indicador2-subtitle">131 Hug × 48 Hug</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">ACTIVIDAD DE VENTAS</div>
                    <div class="stat-value" id="ventas-value">$8954.57</div>
                    <div class="stat-change">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span id="ventas-change">15%</span>
                    </div>
                </div>
            </div>

            <!-- grid content -->
            <div class="content-grid">
                <!-- grafico actividad medica -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">ACTIVIDAD MÉDICA</h3>
                        <select class="chart-filter" id="medical-filter">
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                    <div style="height: 300px; position: relative;">
                        <canvas id="medicalActivityChart"></canvas>
                    </div>
                </div>

                <!-- tabla top centros medicos -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Top de centros médicos asistidos</h3>
                        <select class="chart-filter" id="centers-filter">
                            <option value="30">Últimos 30 días</option>
                            <option value="60">Últimos 60 días</option>
                            <option value="90">Últimos 90 días</option>
                        </select>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody id="top-centers-table">
                                <tr>
                                    <td>
                                        <div class="product-name">VESTIDO MUJER STAR CAROLINA</div>
                                        <div class="product-desc">Color verde, corte europeo</div>
                                    </td>
                                    <td>$11.57</td>
                                    <td>
                                        <div class="rating">
                                            <span class="star">⭐</span>
                                            <span>4.2</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="product-name">POLERÓN MUJER BREAKING BAD</div>
                                        <div class="product-desc">Color amarillo, oversize</div>
                                    </td>
                                    <td>$22.99</td>
                                    <td>
                                        <div class="rating">
                                            <span class="star">⭐</span>
                                            <span>5.0</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="product-name">GORRO B&W</div>
                                        <div class="product-desc">Color negro, línea premium W</div>
                                    </td>
                                    <td>$15.57</td>
                                    <td>
                                        <div class="rating">
                                            <span class="star">⭐</span>
                                            <span>4.8</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- grid  -->
            <div class="bottom-grid">
                <!-- grafico pie -->
                <div class="chart-card">
                    <div style="height: 280px; position: relative;">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>

                <!-- grafico examenes -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Exámenes más realizados</h3>
                        <select class="chart-filter" id="exams-filter">
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                    <div style="height: 250px; position: relative;">
                        <canvas id="examsChart"></canvas>
                    </div>
                </div>

                <!-- cards ventas -->
                <div class="sales-cards">
                    <div class="sales-card">
                        <div class="sales-label">ACTIVIDAD DE VENTAS</div>
                        <div class="sales-value" id="sales1-value">$8954.57</div>
                    </div>
                    <div class="sales-card">
                        <div class="sales-label">ACTIVIDAD DE VENTAS</div>
                        <div class="sales-value" id="sales2-value">$8954.57</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Variables de datos - reemplazar con laravel
        const dashboardData = {
            pacientes: {
                value: 340290,
                change: 15
            },
            indicador: {
                value: 342224,
                change: 15
            },
            indicador2: {
                value: 23432,
                subtitle: '131 Hug × 48 Hug'
            },
            ventas: {
                value: 8954.57,
                change: 15
            },
            actividadMedica: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                data: [5000, 6000, 3000, 4000, 2000, 5000, 8000, 7000, 6000, 5000, 3000, 5000]
            },
            topCentros: [
                {
                    nombre: 'VESTIDO MUJER STAR CAROLINA',
                    descripcion: 'Color verde, corte europeo',
                    precio: 11.57,
                    rating: 4.2
                },
                {
                    nombre: 'POLERÓN MUJER BREAKING BAD',
                    descripcion: 'Color amarillo, oversize',
                    precio: 22.99,
                    rating: 5.0
                },
                {
                    nombre: 'GORRO B&W',
                    descripcion: 'Color negro, línea premium W',
                    precio: 15.57,
                    rating: 4.8
                }
            ],
            pieChart: {
                labels: ['Categoría 1', 'Categoría 2', 'Categoría 3', 'Categoría 4'],
                data: [35, 30, 25, 10],
                colors: ['#5b8fff', '#ff8c42', '#4ecdc4', '#3d5a80']
            },
            examenes: {
                labels: ['Extra Large', 'Large', 'Medium', 'Small'],
                data: [60, 45, 30, 15]
            }
        };

        // config Chart.js
        Chart.defaults.color = '#9ca3af';
        Chart.defaults.borderColor = '#1a1f3a';

        // grafico barra actividad medica
        const medicalCtx = document.getElementById('medicalActivityChart').getContext('2d');
        const medicalActivityChart = new Chart(medicalCtx, {
            type: 'bar',
            data: {
                labels: dashboardData.actividadMedica.labels,
                datasets: [{
                    label: 'Actividad',
                    data: dashboardData.actividadMedica.data,
                    backgroundColor: '#4d7cff',
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1a1f3a',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toLocaleString() + 'K';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#1a1f3a'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + (value / 1000) + 'K';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // grafico pie
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: dashboardData.pieChart.labels,
                datasets: [{
                    data: dashboardData.pieChart.data,
                    backgroundColor: dashboardData.pieChart.colors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1a1f3a',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                cutout: '65%'
            }
        });

        // grafico barra examenes
        const examsCtx = document.getElementById('examsChart').getContext('2d');
        const examsChart = new Chart(examsCtx, {
            type: 'bar',
            data: {
                labels: dashboardData.examenes.labels,
                datasets: [{
                    label: 'Exámenes',
                    data: dashboardData.examenes.data,
                    backgroundColor: '#4d7cff',
                    borderRadius: 8,
                    barThickness: 20
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1a1f3a',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: '#1a1f3a'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // funcion actualizar datos del dashboard
        function updateDashboard(newData) {
            // actualizar valores de las tarjetas
            if (newData.pacientes) {
                document.getElementById('pacientes-value').textContent = newData.pacientes.value.toLocaleString();
                document.getElementById('pacientes-change').textContent = newData.pacientes.change + '%';
            }
            
            if (newData.indicador) {
                document.getElementById('indicador-value').textContent = newData.indicador.value.toLocaleString();
                document.getElementById('indicador-change').textContent = newData.indicador.change + '%';
            }
            
            if (newData.indicador2) {
                document.getElementById('indicador2-value').textContent = newData.indicador2.value.toLocaleString();
                document.getElementById('indicador2-subtitle').textContent = newData.indicador2.subtitle;
            }
            
            if (newData.ventas) {
                document.getElementById('ventas-value').textContent = '$' + newData.ventas.value.toFixed(2);
                document.getElementById('ventas-change').textContent = newData.ventas.change + '%';
                document.getElementById('sales1-value').textContent = '$' + newData.ventas.value.toFixed(2);
                document.getElementById('sales2-value').textContent = '$' + newData.ventas.value.toFixed(2);
            }
            
            // actualizar grafico de actividad medica
            if (newData.actividadMedica) {
                medicalActivityChart.data.labels = newData.actividadMedica.labels;
                medicalActivityChart.data.datasets[0].data = newData.actividadMedica.data;
                medicalActivityChart.update();
            }
            
            // actualizar tabla de centros
            if (newData.topCentros) {
                const tableBody = document.getElementById('top-centers-table');
                tableBody.innerHTML = '';
                newData.topCentros.forEach(centro => {
                    const row = `
                        <tr>
                            <td>
                                <div class="product-name">${centro.nombre}</div>
                                <div class="product-desc">${centro.descripcion}</div>
                            </td>
                            <td>$${centro.precio.toFixed(2)}</td>
                            <td>
                                <div class="rating">
                                    <span class="star">⭐</span>
                                    <span>${centro.rating}</span>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            }
            
            // actualizar grafico circular
            if (newData.pieChart) {
                pieChart.data.labels = newData.pieChart.labels;
                pieChart.data.datasets[0].data = newData.pieChart.data;
                if (newData.pieChart.colors) {
                    pieChart.data.datasets[0].backgroundColor = newData.pieChart.colors;
                }
                pieChart.update();
            }
            
            // Actualizar gráfico de exámenes
            if (newData.examenes) {
                examsChart.data.labels = newData.examenes.labels;
                examsChart.data.datasets[0].data = newData.examenes.data;
                examsChart.update();
            }
        }

        // event listeners para filtros
        document.getElementById('medical-filter').addEventListener('change', function(e) {
            console.log('Filtro de año seleccionado:', e.target.value);
        });

        document.getElementById('centers-filter').addEventListener('change', function(e) {
            console.log('Filtro de días seleccionado:', e.target.value);
        });

        document.getElementById('exams-filter').addEventListener('change', function(e) {
            console.log('Filtro de exámenes seleccionado:', e.target.value);
        });
    </script>
</body>
</html>
