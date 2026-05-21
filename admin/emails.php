<?php
require_once __DIR__ . '/../config/auth.php';
require_login();
$flash = '';
$type = 'success';
$edit = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? 'create';
  $id = (int) ($_POST['id'] ?? 0);
  $unidade = trim($_POST['unidade'] ?? '');
  $setor = trim($_POST['setor'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $grupo_email = trim($_POST['grupo_email'] ?? '');
  $responsavel = trim($_POST['responsavel'] ?? '');

  if ($unidade === '' || $setor === '' || $email === '') {
    $flash = 'Preencha unidade, setor e email.';
    $type = 'error';
  } else {
    if ($action === 'update' && $id > 0) {
      $stmt = $pdo->prepare('UPDATE emails SET unidade=?, setor=?, email=?, grupo_email=?, responsavel=? WHERE id=?');
      $stmt->execute([$unidade, $setor, $email, $grupo_email, $responsavel, $id]);
      $flash = 'Email atualizado com sucesso.';
    } else {
      $stmt = $pdo->prepare('INSERT INTO emails (unidade, setor, email, grupo_email, responsavel) VALUES (?, ?, ?, ?, ?)');
      $stmt->execute([$unidade, $setor, $email, $grupo_email, $responsavel]);
      $flash = 'Email cadastrado com sucesso.';
    }
  }
}
if (isset($_GET['delete'])) {
  $id = (int) $_GET['delete'];
  $stmt = $pdo->prepare('DELETE FROM emails WHERE id=?');
  $stmt->execute([$id]);
  header('Location: emails.php?ok=deleted');
  exit;
}
if (isset($_GET['edit'])) {
  $id = (int) $_GET['edit'];
  $stmt = $pdo->prepare('SELECT * FROM emails WHERE id=?');
  $stmt->execute([$id]);
  $edit = $stmt->fetch();
}
if (isset($_GET['ok']) && $_GET['ok'] === 'deleted') {
  $flash = 'Email removido com sucesso.';
}
$items = $pdo->query('SELECT * FROM emails ORDER BY unidade ASC, setor ASC')->fetchAll();
$pageTitle = 'Gerenciar Emails';
$basePath = '../';
include __DIR__ . '/../partials/header.php';
?>
<div class="topbar topbar-admin admin-topbar">
  <div class="admin-brand-wrap">
    <a href="/intranet_generica">
    <span class="topbar-logo logo-placeholder">LOGO AQUI</span>
  </a>
  </div>
  <div class="actions admin-actions"><a class="btn secondary" href="dashboard.php">Dashboard</a><a class="btn"
      href="logout.php">Sair</a></div>
</div>
<div class="content content-admin">
  <div class="header-row">
    <h1 class="section-title" style="margin:0;">GERENCIADOR DE EMAILS</h1><a class="btn secondary"
      href="../index.php?page=emails">Ver página pública</a>
  </div>
  <?php if ($flash): ?>
    <div class="flash <?= e($type) ?>"><?= e($flash) ?></div><?php endif; ?>
  <div style="display:grid;grid-template-columns:340px 1fr;gap:18px;align-items:start;">
    <div class="form-card">
      <h3><?= $edit ? 'Editar email' : 'Novo email' ?></h3>
      <form method="post">
        <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
        <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
        <div style="display:flex;flex-direction:column;gap:0;">
          <div>
            <label>Unidade</label>
            <select class="input" name="unidade" required>
              <?php foreach (['Hotel 1', 'Hotel 2', 'Hotel 3', 'Grupo Hoteleiro'] as $u): ?>
                <option value="<?= e($u) ?>" <?= (($edit['unidade'] ?? '') === $u) ? 'selected' : '' ?>><?= e($u) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label>Setor</label>
            <input class="input" type="text" name="setor" value="<?= e($edit['setor'] ?? '') ?>" required>
          </div>
          <div>
            <label>Email</label>
            <input class="input" type="email" name="email" value="<?= e($edit['email'] ?? '') ?>" required>
          </div>
          <div>
            <label>Grupo de Email</label>
            <input class="input" type="email" name="grupo_email" value="<?= e($edit['grupo_email'] ?? '') ?>">
          </div>
          <div>
            <label>Responsável</label>
            <input class="input" type="text" name="responsavel" value="<?= e($edit['responsavel'] ?? '') ?>">
          </div>
        </div>
        <div style="margin-top:8px;">
          <button class="btn" type="submit"><?= $edit ? 'Salvar alterações' : 'Cadastrar email' ?></button>
          <?php if ($edit): ?><a class="btn secondary" href="emails.php"
              style="display:inline-block;margin-left:8px;">Cancelar</a><?php endif; ?>
        </div>
      </form>
    </div>
    <div class="form-card">
      <div class="toolbar">
        <h3 style="margin:0;">Lista de emails</h3>
        <div class="search-box" style="flex:1;"><input id="searchAdminEmails" type="text" placeholder="Pesquisar..."
            onkeyup="filterTable('searchAdminEmails','adminEmailsTable')"></div>
      </div>
      <div id="topScrollBar" style="overflow-x:auto;height:12px;margin-bottom:2px;">
        <div id="topScrollInner" style="height:1px;"></div>
      </div>
      <div id="tableScroll" class="card" style="overflow-x:auto;">
        <table id="adminEmailsTable" style="min-width:700px;">
          <thead>
            <tr>
              <th>Unidade</th>
              <th>Setor</th>
              <th>Email</th>
              <th>Grupo de Email</th>
              <th>Responsável</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td><?= e($item['unidade']) ?></td>
                <td><?= e($item['setor']) ?></td>
                <td><?= e($item['email']) ?></td>
                <td><?= e($item['grupo_email']) ?></td>
                <td><?= e($item['responsavel']) ?></td>
                <td class="actions"><a class="btn secondary" href="?edit=<?= $item['id'] ?>">Editar</a><a
                    class="btn danger" onclick="return confirm('Deseja excluir este email?')"
                    href="?delete=<?= $item['id'] ?>">Excluir</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <script>
        (function(){
          var top = document.getElementById('topScrollBar');
          var inner = document.getElementById('topScrollInner');
          var box = document.getElementById('tableScroll');
          function syncWidth(){ inner.style.width = box.scrollWidth + 'px'; }
          syncWidth();
          new ResizeObserver(syncWidth).observe(box);
          top.addEventListener('scroll', function(){ box.scrollLeft = top.scrollLeft; });
          box.addEventListener('scroll', function(){ top.scrollLeft = box.scrollLeft; });
        })();
      </script>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>