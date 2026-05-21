<?php
require_once __DIR__ . '/../config/auth.php';
require_login();
$pageTitle = 'Dashboard';
$basePath = '../';
include __DIR__ . '/../partials/header.php';

$ramaisCount = (int) $pdo->query('SELECT COUNT(*) FROM ramais')->fetchColumn();
$emailsCount = (int) $pdo->query('SELECT COUNT(*) FROM emails')->fetchColumn();
$treinamentosCount = (int) $pdo->query('SELECT COUNT(*) FROM treinamentos')->fetchColumn();
$linksCount = (int) $pdo->query('SELECT COUNT(*) FROM links_uteis')->fetchColumn();
$popupCount = (int) $pdo->query('SELECT COUNT(*) FROM popups')->fetchColumn();
?>
<div class="topbar topbar-admin admin-topbar">
  <div class="admin-brand-wrap">
    <a href="/intranet_generica">
    <span class="topbar-logo logo-placeholder">LOGO AQUI</span>
  </a>
  </div>
  <div class="actions admin-actions">
    <span class="badge">Olá, <?= e($_SESSION['admin_name'] ?? 'Admin') ?></span>
    <a class="btn secondary" href="../index.php">Ver intranet</a>
    <a class="btn" href="logout.php">Sair</a>
  </div>
</div>
<div class="content content-admin">
  <div class="header-row">
    <div>
      <h1 class="section-title" style="margin-top:0;">PAINEL ADMIN</h1>
      <p class="muted"></p>
    </div>
  </div>

  <div class="dashboard-cards">
    <div class="stat-card">
      <div class="number"><?= $ramaisCount ?></div>
      <div class="label">Ramais</div>
    </div>
    <div class="stat-card">
      <div class="number"><?= $emailsCount ?></div>
      <div class="label">Emails</div>
    </div>
    <div class="stat-card">
      <div class="number"><?= $treinamentosCount ?></div>
      <div class="label">Treinamentos</div>
    </div>
    <div class="stat-card">
      <div class="number"><?= $linksCount ?></div>
      <div class="label">Links úteis</div>
    </div>
    <div class="stat-card">
      <div class="number"><?= $popupCount ?></div>
      <div class="label">Popups</div>
    </div>
  </div>

  <div class="grid-2">
    <a class="form-card" href="ramais.php">
      <h3>Gerenciar ramais</h3>
      <p class="muted">Cadastrar, editar e separar por unidade.</p>
    </a>
    <a class="form-card" href="emails.php">
      <h3>Gerenciar emails</h3>
      <p class="muted">Organizar emails por unidade e setor.</p>
    </a>
    <a class="form-card" href="treinamentos.php">
      <h3>Gerenciar treinamentos</h3>
      <p class="muted">Atualizar links e descrições.</p>
    </a>
    <a class="form-card" href="links.php">
      <h3>Gerenciar links úteis</h3>
      <p class="muted">GLPI, intranet e sistemas da empresa.</p>
    </a>
    <a class="form-card" href="popups.php">
      <h3>Gerenciar popups</h3>
      <p class="muted">Criar avisos com estilo e período de exibição.</p>
    </a>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>