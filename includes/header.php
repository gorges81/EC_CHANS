<?php
// /ec_chans/includes/header.php

// (El código PHP para iniciar sesión y cargar config.php se ha eliminado de aquí)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EC_CHANS - Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="/ec_chans/css/style.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <?php include_once PROJECT_ROOT . '/includes/sidebar.php'; ?>
        <div id="page-content-wrapper" class="flex-grow-1 d-flex flex-column">
            <nav class="navbar navbar-expand-lg border-bottom">
                 <div class="container-fluid">
                    <h1 class="navbar-brand mb-0 h1 text-dark-blue ms-3">ECOMMERCE CHANNELS ANALYTICS</h1>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="/ec_chans/app/configuracion/main_configuracion.php">
                                    <i class="bi bi-gear-fill me-1"></i>Configuración
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/ec_chans/app/ayuda/main_ayuda.php">
                                     <i class="bi bi-question-circle-fill me-1"></i>Ayuda
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/ec_chans/logout.php">
                                     <i class="bi bi-box-arrow-right me-1"></i>Salir
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <main class="container-fluid py-4 flex-grow-1">