<?php require_once __DIR__ . '/../config/auth.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?>Intranet Genérica</title>
  <link rel="stylesheet" href="<?= $basePath ?? '' ?>style.css">
</head>
<body>
