<?php
require_once __DIR__ . '/config.php';

function is_logged_in(): bool
{
    return isset($_SESSION['admin_id']);
}

function admin_login_url(): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $baseDir = rtrim(str_replace('\\', '/', dirname(dirname($scriptName))), '/');
    if ($baseDir === '' || $baseDir === '.') {
        $baseDir = '';
    }
    return $baseDir . '/admin/login.php';
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . admin_login_url());
        exit;
    }
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
