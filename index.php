<?php
require_once __DIR__ . '/config/auth.php';

$page = $_GET['page'] ?? 'ramais';
$unit = $_GET['unit'] ?? 'todos';
$allowedPages = ['ramais', 'emails', 'treinamentos', 'links'];
$allowedUnits = ['todos', 'hotel1', 'hotel2', 'hotel3', 'grupo'];

if (!in_array($page, $allowedPages, true)) {
  $page = 'ramais';
}
if (!in_array($unit, $allowedUnits, true)) {
  $unit = 'todos';
}

function unit_label(string $slug): string
{
  return match ($slug) {
    'hotel1' => 'Hotel 1',
    'hotel2' => 'Hotel 2',
    'hotel3' => 'Hotel 3',
    'grupo' => 'Grupo Hoteleiro',
    default => 'Todos',
  };
}

$basePath = '';
$pageTitle = ucfirst($page);
include __DIR__ . '/partials/header.php';

$popupStmt = $pdo->query("SELECT * FROM popups WHERE is_active = 1 AND starts_at <= NOW() AND ends_at >= NOW() ORDER BY created_at DESC LIMIT 1");
$activePopup = $popupStmt->fetch();

$whereByUnit = '';
$params = [];
if ($unit !== 'todos') {
  $whereByUnit = ' WHERE unidade = ? ';
  $params[] = unit_label($unit);
}

$ramaisStmt = $pdo->prepare("SELECT * FROM ramais" . $whereByUnit . " ORDER BY unidade ASC, ramal ASC");
$ramaisStmt->execute($params);
$ramais = $ramaisStmt->fetchAll();

$emailsStmt = $pdo->prepare("SELECT * FROM emails" . $whereByUnit . " ORDER BY unidade ASC, setor ASC");
$emailsStmt->execute($params);
$emails = $emailsStmt->fetchAll();

$treinamentos = $pdo->query("SELECT * FROM treinamentos ORDER BY titulo ASC")->fetchAll();
$links = $pdo->query("SELECT * FROM links_uteis ORDER BY nome ASC")->fetchAll();
?>
<header class="topbar">
  <a href="/intranet_generica">
    <span class="topbar-logo logo-placeholder">LOGO AQUI</span>
  </a>
</header>

<?php if ($activePopup): ?>
  <div class="popup-overlay" id="portalPopup" data-popup-id="<?= e((string) $activePopup['id']) ?>">
    <div class="popup-card <?= e($activePopup['style_type']) ?>">
      <button type="button" class="popup-close" onclick="closePopup()">×</button>
      <?php if (!empty($activePopup['title'])): ?>
        <h2><?= e($activePopup['title']) ?></h2>
      <?php endif; ?>
      <div class="popup-body"><?= nl2br(e($activePopup['message'])) ?></div>
      <?php if (!empty($activePopup['button_label']) && !empty($activePopup['button_url'])): ?>
        <a class="btn" href="<?= e($activePopup['button_url']) ?>" target="_blank"
          rel="noopener noreferrer"><?= e($activePopup['button_label']) ?></a>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-logo-wrap">
      <span class="sidebar-brand">INTRANET</span>
    </div>
    <nav class="menu">
      <a class="<?= $page === 'ramais' ? 'active' : '' ?>" href="?page=ramais">Ramais</a>
      <a class="<?= $page === 'emails' ? 'active' : '' ?>" href="?page=emails">Emails</a>
      <a class="<?= $page === 'treinamentos' ? 'active' : '' ?>" href="?page=treinamentos">Treinamentos</a>
      <a class="<?= $page === 'links' ? 'active' : '' ?>" href="?page=links">Acesso GLPI / Links</a>
      <a href="admin/login.php">Área Admin</a>
    </nav>
  </aside>

  <main class="content">

    <?php if ($page === 'ramais'): ?>
      <div class="section-header">
        <h1 class="section-title">RAMAIS</h1>
        <div class="subnav">
          <a class="<?= $unit === 'todos' ? 'active' : '' ?>" href="?page=<?= e($page) ?>&unit=todos">Todos</a>
          <a class="<?= $unit === 'hotel1' ? 'active' : '' ?>" href="?page=<?= e($page) ?>&unit=hotel1">Hotel 1</a>
          <a class="<?= $unit === 'hotel2' ? 'active' : '' ?>"
            href="?page=<?= e($page) ?>&unit=hotel2">Hotel 2</a>
          <a class="<?= $unit === 'hotel3' ? 'active' : '' ?>" href="?page=<?= e($page) ?>&unit=hotel3">Hotel 3</a>
          <a class="<?= $unit === 'grupo' ? 'active' : '' ?>" href="?page=<?= e($page) ?>&unit=grupo">Grupo Hoteleiro</a>

        </div>
      </div>
      <div class="toolbar search-box"><input id="searchRamais" type="text"
          placeholder="Pesquisar ramal, setor ou unidade..." onkeyup="filterTable('searchRamais','tableRamais')"></div>
      <div class="card">
        <table id="tableRamais">
          <thead>
            <tr>
              <th>UNIDADE</th>
              <th>RAMAL</th>
              <th>SETOR</th>
              <th>RESPONSÁVEL</th>
              <th>OBSERVAÇÃO</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ramais as $item): ?>
              <tr>
                <td><?= e($item['unidade']) ?></td>
                <td><?= e($item['ramal']) ?></td>
                <td><?= e($item['setor']) ?></td>
                <td><?= e($item['responsavel']) ?></td>
                <td><?= e($item['observacao']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php elseif ($page === 'emails'): ?>
      <div class="section-header">
        <h1 class="section-title">EMAILS</h1>
        <div class="subnav">
          <a class="<?= $unit === 'todos' ? 'active' : '' ?>" href="?page=<?= e($page) ?>&unit=todos">Todos</a>
          <a class="<?= $unit === 'hotel1' ? 'active' : '' ?>" href="?page=<?= e($page) ?>&unit=hotel1">Hotel 1</a>
          <a class="<?= $unit === 'hotel2' ? 'active' : '' ?>"
            href="?page=<?= e($page) ?>&unit=hotel2">Hotel 2</a>
          <a class="<?= $unit === 'hotel3' ? 'active' : '' ?>" href="?page=<?= e($page) ?>&unit=hotel3">Hotel 3</a>
          <a class="<?= $unit === 'grupo' ? 'active' : '' ?>" href="?page=<?= e($page) ?>&unit=grupo">Grupo Hoteleiro</a>

        </div>
      </div>
      <div class="toolbar search-box"><input id="searchEmails" type="text"
          placeholder="Pesquisar email, setor ou unidade..." onkeyup="filterTable('searchEmails','tableEmails')"></div>
      <div class="card">
        <table id="tableEmails">
          <thead>
            <tr>
              <th>UNIDADE</th>
              <th>SETOR</th>
              <th>EMAIL</th>
              <th>GRUPO DE EMAIL</th>
              <th>RESPONSÁVEL</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($emails as $item): ?>
              <tr>
                <td><?= e($item['unidade']) ?></td>
                <td><?= e($item['setor']) ?></td>
                <td><a href="mailto:<?= e($item['email']) ?>"><?= e($item['email']) ?></a></td>
                <td><?= e($item['grupo_email']) ?></td>
                <td><?= e($item['responsavel']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php elseif ($page === 'treinamentos'): ?>
      <h1 class="section-title">TREINAMENTOS</h1>
      <div class="toolbar search-box"><input id="searchTreinamentos" type="text" placeholder="Pesquisar treinamento..."
          onkeyup="filterTable('searchTreinamentos','tableTreinamentos')"></div>
      <div class="card">
        <table id="tableTreinamentos">
          <thead>
            <tr>
              <th>TÍTULO</th>
              <th>CATEGORIA</th>
              <th>DESCRIÇÃO</th>
              <th>LINK</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($treinamentos as $item): ?>
              <tr>
                <td><?= e($item['titulo']) ?></td>
                <td><?= e($item['categoria']) ?></td>
                <td><?= e($item['descricao']) ?></td>
                <td><?php if ($item['link']): ?><a href="<?= e($item['link']) ?>" target="_blank"
                      rel="noopener noreferrer">Abrir</a><?php endif; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <h1 class="section-title">LINKS ÚTEIS</h1>
      <div class="toolbar search-box"><input id="searchLinks" type="text" placeholder="Pesquisar link..."
          onkeyup="filterTable('searchLinks','tableLinks')"></div>
      <div class="card">
        <table id="tableLinks">
          <thead>
            <tr>
              <th>NOME</th>
              <th>CATEGORIA</th>
              <th>URL</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($links as $item): ?>
              <tr>
                <td><?= e($item['nome']) ?></td>
                <td><?= e($item['categoria']) ?></td>
                <td><a href="<?= e($item['url']) ?>" target="_blank" rel="noopener noreferrer"><?= e($item['url']) ?></a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </main>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>