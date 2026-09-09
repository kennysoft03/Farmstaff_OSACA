<?php
// ============================================================
// FARMSTAFF REGISTRY — Environment Database Configuration
// This file overrides database.php and is loaded last.
// For local development with Laragon (default MySQL settings).
// ============================================================

$db['default'] = array(
    'dsn'         => '',
    'hostname'    => '127.0.0.1',
    'username'    => 'root',
    'password'    => '',          // Laragon default: empty password
    'database'    => 'farmstaff',  // Database created from farmstaff_db.sql
    'dbdriver'    => 'mysqli',
    'dbprefix'    => '',
    'pconnect'    => FALSE,
    'db_debug'    => TRUE,        // Show DB errors during development
    'cache_on'    => FALSE,
    'cachedir'    => '',
    'char_set'    => 'utf8mb4',
    'dbcollat'    => 'utf8mb4_unicode_ci',
    'swap_pre'    => '',
    'encrypt'     => FALSE,
    'compress'    => FALSE,
    'stricton'    => FALSE,
    'failover'    => array(),
    'save_queries'=> TRUE,
);
