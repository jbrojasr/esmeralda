<?php
/**
 * Configuración general del sistema.
 * Ajuste estos valores según su servidor (XAMPP usa root sin clave por defecto).
 */
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'esmeralda');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

define('APP_NOMBRE', 'Escuela de Fútbol La Esmeralda');
define('APP_SIGLAS', 'EF La Esmeralda');
define('APP_UBICACION', 'Municipio Girardot, Maracay, estado Aragua');

define('FOTO_MAX_BYTES', 2 * 1024 * 1024);   // 2 MB
define('FOTO_DIR', __DIR__ . '/../uploads/fotos/');
define('FOTO_URL', 'uploads/fotos/');

date_default_timezone_set('America/Caracas');
