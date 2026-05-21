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
  $ramal = trim($_POST['ramal'] ?? '');
  $setor = trim($_POST['setor'] ?? '');
  $responsavel = trim($_POST['responsavel'] ?? '');
  $observacao = trim($_POST['observacao'] ?? '');

  if ($unidade === '' || $ramal === '' || $setor === '') {
    $flash = 'Preencha unidade, ramal e setor.';
    $type = 'error';
  } else {
    if ($action === 'update' && $id > 0) {
      $stmt = $pdo->prepare('UPDATE ramais SET unidade=?, ramal=?, setor=?, responsavel=?, observacao=? WHERE id=?');
      $stmt->execute([$unidade, $ramal, $setor, $responsavel, $observacao, $id]);
      $flash = 'Ramal atualizado com sucesso.';
    } else {
      $stmt = $pdo->prepare('INSERT INTO ramais (unidade, ramal, setor, responsavel, observacao) VALUES (?, ?, ?, ?, ?)');
      $stmt->execute([$unidade, $ramal, $setor, $responsavel, $observacao]);
      $flash = 'Ramal cadastrado com sucesso.';
    }
  }
}

if (isset($_GET['delete'])) {
  $id = (int) $_GET['delete'];
  $stmt = $pdo->prepare('DELETE FROM ramais WHERE id=?');
  $stmt->execute([$id]);
  header('Location: ramais.php?ok=deleted');
  exit;
}

if (isset($_GET['edit'])) {
  $id = (int) $_GET['edit'];
  $stmt = $pdo->prepare('SELECT * FROM ramais WHERE id=?');
  $stmt->execute([$id]);
  $edit = $stmt->fetch();
}

if (isset($_GET['ok']) && $_GET['ok'] === 'deleted') {
  $flash = 'Ramal removido com sucesso.';
}

$items = $pdo->query('SELECT * FROM ramais ORDER BY unidade ASC, ramal ASC')->fetchAll();
$pageTitle = 'Gerenciar Ramais';
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
    <h1 class="section-title" style="margin:0;">GERENCIADOR DE RAMAIS</h1><a class="btn secondary"
      href="../index.php?page=ramais">Ver página pública</a>
  </div>
  <?php if ($flash): ?>
    <div class="flash <?= e($type) ?>"><?= e($flash) ?></div><?php endif; ?>
  <div class="grid-2">
    <div class="form-card">
      <h3><?= $edit ? 'Editar ramal' : 'Novo ramal' ?></h3>
      <form method="post">
        <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
        <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
        <label>Unidade</label>
        <select class="input" name="unidade" required>
          <?php foreach (['Hotel 1', 'Hotel 2', 'Hotel 3', 'Grupo Hoteleiro'] as $u): ?>
            <option value="<?= e($u) ?>" <?= (($edit['unidade'] ?? '') === $u) ? 'selected' : '' ?>><?= e($u) ?></option>
          <?php endforeach; ?>
        </select>
        <label>Ramal</label>
        <input class="input" type="text" name="ramal" value="<?= e($edit['ramal'] ?? '') ?>" required>
        <label>Setor</label>
        <input class="input" type="text" name="setor" value="<?= e($edit['setor'] ?? '') ?>" required>
        <label>Responsável</label>
        <input class="input" type="text" name="responsavel" value="<?= e($edit['responsavel'] ?? '') ?>">
        <label>Observação</label>
        <input class="input" type="text" name="observacao" value="<?= e($edit['observacao'] ?? '') ?>">
        <button class="btn" type="submit"><?= $edit ? 'Salvar alterações' : 'Cadastrar ramal' ?></button>
        <?php if ($edit): ?><a class="btn secondary" href="ramais.php"
            style="display:inline-block;margin-left:8px;">Cancelar</a><?php endif; ?>
      </form>
    </div>
    <div class="form-card">
      <div class="toolbar">
        <h3 style="margin:0;">Lista de ramais</h3>
        <div class="search-box" style="flex:1;"><input id="searchAdminRamais" type="text" placeholder="Pesquisar..."
            onkeyup="filterTable('searchAdminRamais','adminRamaisTable')"></div>
      </div>
      <div class="card">
        <table id="adminRamaisTable">
          <thead>
            <tr>
              <th>Unidade</th>
              <th>Ramal</th>
              <th>Setor</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td><?= e($item['unidade']) ?></td>
                <td><?= e($item['ramal']) ?></td>
                <td><?= e($item['setor']) ?></td>
                <td class="actions"><a class="btn secondary" href="?edit=<?= $item['id'] ?>">Editar</a><a
                    class="btn danger" onclick="return confirm('Deseja excluir este ramal?')"
                    href="?delete=<?= $item['id'] ?>">Excluir</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>