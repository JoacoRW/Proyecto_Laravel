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
            background: var(--dashboard-primary);
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
            color: var(--dashboard-primary);
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
            background: linear-gradient(135deg, var(--dashboard-primary) 0%, var(--dashboard-secondary) 100%);
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
            background: rgba(12,16,28,0.65);
            border-radius: 12px;
            padding: 18px 20px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.03);
            box-shadow: 0 6px 18px rgba(2,6,23,0.45);
            color: #ffffff;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -10%;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 50%;
            pointer-events: none;
        }

        .stat-card .stat-label,
        .stat-card .stat-value,
        .stat-card .stat-subtitle,
        .stat-card .stat-change {
            color: #ffffff;
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
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: start;
        }

        .sales-cards {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sales-card {
            background: linear-gradient(135deg, var(--dashboard-primary) 0%, var(--dashboard-secondary) 100%);
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
                                @auth
                                    <div class="user-name">{{ auth()->user()->name ?? auth()->user()->nombre ?? auth()->user()->email }}</div>
                                    <div class="user-role">{{ strtoupper(auth()->user()->role ?? 'Usuario') }}</div>
                                @else
                                    <div class="user-name">Invitado</div>
                                    <div class="user-role">Usuario</div>
                                @endauth
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
            
            <div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 20px;">
                <a href="{{ route('consultas.index') }}" class="export-btn" style="background: var(--dashboard-primary); border: none; color: #fff; padding: 10px 16px; border-radius:8px; font-weight:600;">Ver Consultas</a>
            </div>

            <!-- cartas stats -->
            <div class="stats-grid">
                <div class="stat-card" style="background: linear-gradient(135deg, {{ auth()->check() && auth()->user()->dashboard_color_primary ? auth()->user()->dashboard_color_primary : 'var(--dashboard-primary)' }}, {{ auth()->check() && auth()->user()->dashboard_color_secondary ? auth()->user()->dashboard_color_secondary : 'var(--dashboard-secondary)' }});">
                    <div class="stat-label">Pacientes activos (30d)</div>
                    <div class="stat-value" id="pacientes-value">340290</div>
                    <div class="stat-change">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span id="pacientes-change">15%</span>
                    </div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, {{ auth()->check() && auth()->user()->dashboard_color_primary ? auth()->user()->dashboard_color_primary : 'var(--dashboard-primary)' }}, {{ auth()->check() && auth()->user()->dashboard_color_secondary ? auth()->user()->dashboard_color_secondary : 'var(--dashboard-secondary)' }});">
                    <div class="stat-label">Consultas (30d)</div>
                    <div class="stat-value" id="indicador-value">342224</div>
                    <div class="stat-change">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span id="indicador-change">15%</span>
                    </div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, {{ auth()->check() && auth()->user()->dashboard_color_primary ? auth()->user()->dashboard_color_primary : 'var(--dashboard-primary)' }}, {{ auth()->check() && auth()->user()->dashboard_color_secondary ? auth()->user()->dashboard_color_secondary : 'var(--dashboard-secondary)' }});">
                    <div class="stat-label">Nuevos pacientes (30d)</div>
                    <div class="stat-value" id="indicador2-value">23432</div>
                    <div class="stat-subtitle" id="indicador2-subtitle">131 Hug × 48 Hug</div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, {{ auth()->check() && auth()->user()->dashboard_color_primary ? auth()->user()->dashboard_color_primary : 'var(--dashboard-primary)' }}, {{ auth()->check() && auth()->user()->dashboard_color_secondary ? auth()->user()->dashboard_color_secondary : 'var(--dashboard-secondary)' }});">
                    <div class="stat-label">Exámenes (30d)</div>
                    <div class="stat-value" id="ventas-value">0</div>
                    <div class="stat-change">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span id="ventas-change">0%</span>
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
                            <option value="2024">2025</option>
                            <option value="2023">2024</option>
                        </select>
                    </div>
                    <div style="height: 300px; position: relative;">
                        <canvas id="medicalActivityChart"></canvas>
                    </div>
                </div>

                <!-- Top pacientes por consultas (reemplaza tabla de centros) -->
                <div class="chart-card" style="width:100%;">
                    <div class="chart-header">
                        <h3 class="chart-title">Top Pacientes por Consultas</h3>
                        <select class="chart-filter" id="centers-filter">
                            <option value="30">Últimos 30 días</option>
                            <option value="60">Últimos 60 días</option>
                            <option value="90">Últimos 90 días</option>
                        </select>
                    </div>
                    <div style="height: 420px; position: relative; width:100%;">
                        <canvas id="topPatientsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- grid  -->
            <div class="bottom-grid">
                <!-- grafico pie -->
                <div class="chart-card" style="width:100%;">
                    <div class="chart-header">
                        <h3 class="chart-title">Distribución por Tipo de Consulta</h3>
                    </div>
                    <div style="height: 420px; position: relative; width:100%; display:flex; align-items:center; justify-content:center;">
                        <canvas id="pieChart" style="max-width:420px; width:100%;"></canvas>
                    </div>
                </div>

                <!-- grafico examenes -->
                <div class="chart-card" style="width:100%;">
                    <div class="chart-header">
                        <h3 class="chart-title">Exámenes más realizados</h3>
                        <select class="chart-filter" id="exams-filter">
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>
                    <div style="height: 420px; position: relative; width:100%;">
                        <canvas id="examsChart"></canvas>
                    </div>
                </div>

                <!-- sales cards removed; bottom now has two centered charts -->
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
                value: 0,
                change: 0
            },
            actividadMedica: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                data: [5000, 6000, 3000, 4000, 2000, 5000, 8000, 7000, 6000, 5000, 3000, 5000]
            },
            topPacientes: {
                labels: [],
                data: []
            },
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

        // Read user colors from CSS variables
        const __rootStyle = getComputedStyle(document.documentElement);
        const __primaryColor = (__rootStyle.getPropertyValue('--dashboard-primary') || '#4d7cff').trim();
        const __secondaryColor = (__rootStyle.getPropertyValue('--dashboard-secondary') || '#5b8fff').trim();

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
                    backgroundColor: __primaryColor,
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
                    backgroundColor: __primaryColor,
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

        // grafico top pacientes (horizontal bar)
        const topPatientsCtx = document.getElementById('topPatientsChart').getContext('2d');
        const topPatientsChart = new Chart(topPatientsCtx, {
            type: 'bar',
            data: {
                labels: dashboardData.topPacientes.labels,
                datasets: [{
                    label: 'Consultas',
                    data: dashboardData.topPacientes.data,
                    backgroundColor: '#4ecdc4',
                    borderRadius: 8,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#1a1f3a' }
                },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#1a1f3a' } },
                    y: { grid: { display: false }, ticks: { autoSkip: false } }
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
                // ventas now represents número de exámenes
                document.getElementById('ventas-value').textContent = newData.ventas.value.toLocaleString();
                document.getElementById('ventas-change').textContent = newData.ventas.change + '%';
            }
            
            // actualizar grafico de actividad medica
            if (newData.actividadMedica) {
                medicalActivityChart.data.labels = newData.actividadMedica.labels;
                medicalActivityChart.data.datasets[0].data = newData.actividadMedica.data;
                medicalActivityChart.update();
            }
            
            // actualizar top pacientes
            if (newData.topPacientes) {
                topPatientsChart.data.labels = newData.topPacientes.labels;
                topPatientsChart.data.datasets[0].data = newData.topPacientes.data;
                topPatientsChart.update();
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

        // Fetch metrics from server and update charts
        async function loadDashboardMetrics() {
            try {
                const res = await fetch('{{ route('metrics.dashboard') }}');
                if (!res.ok) throw new Error('Network response was not ok');
                const data = await res.json();
                updateDashboard(data);
            } catch (err) {
                console.error('Failed to load dashboard metrics', err);
            }
        }

        // initial load and periodic refresh
        loadDashboardMetrics();
        setInterval(loadDashboardMetrics, 60 * 1000); // refresh every minute
    </script>
</body>
</html>
