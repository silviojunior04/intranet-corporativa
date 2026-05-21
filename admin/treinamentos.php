<?php
require_once __DIR__ . '/../config/auth.php';
require_login();
$flash = '';
$type = 'success';
$edit = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? 'create';
  $id = (int) ($_POST['id'] ?? 0);
  $titulo = trim($_POST['titulo'] ?? '');
  $categoria = trim($_POST['categoria'] ?? '');
  $descricao = trim($_POST['descricao'] ?? '');
  $link = trim($_POST['link'] ?? '');

  if ($titulo === '') {
    $flash = 'Preencha o título do treinamento.';
    $type = 'error';
  } else {
    if ($action === 'update' && $id > 0) {
      $stmt = $pdo->prepare('UPDATE treinamentos SET titulo=?, categoria=?, descricao=?, link=? WHERE id=?');
      $stmt->execute([$titulo, $categoria, $descricao, $link, $id]);
      $flash = 'Treinamento atualizado com sucesso.';
    } else {
      $stmt = $pdo->prepare('INSERT INTO treinamentos (titulo, categoria, descricao, link) VALUES (?, ?, ?, ?)');
      $stmt->execute([$titulo, $categoria, $descricao, $link]);
      $flash = 'Treinamento cadastrado com sucesso.';
    }
  }
}
if (isset($_GET['delete'])) {
  $id = (int) $_GET['delete'];
  $stmt = $pdo->prepare('DELETE FROM treinamentos WHERE id=?');
  $stmt->execute([$id]);
  header('Location: treinamentos.php?ok=deleted');
  exit;
}
if (isset($_GET['edit'])) {
  $id = (int) $_GET['edit'];
  $stmt = $pdo->prepare('SELECT * FROM treinamentos WHERE id=?');
  $stmt->execute([$id]);
  $edit = $stmt->fetch();
}
if (isset($_GET['ok']) && $_GET['ok'] === 'deleted') {
  $flash = 'Treinamento removido com sucesso.';
}
$items = $pdo->query('SELECT * FROM treinamentos ORDER BY titulo ASC')->fetchAll();
$pageTitle = 'Gerenciar Treinamentos';
$basePath = '../';
include __DIR__ . '/../partials/header.php';
?>
<div class="topbar topbar-admin admin-topbar">
  <div class="admin-brand-wrap">
    <span class="topbar-logo logo-placeholder">LOGO AQUI</span>
  </div>
  <div class="actions admin-actions"><a class="btn secondary" href="dashboard.php">Dashboard</a><a class="btn"
      href="logout.php">Sair</a></div>
</div>
<div class="content content-admin">
  <div class="header-row">
    <h1 class="section-title" style="margin:0;">GERENCIADOR DE TREINAMENTOS</h1><a class="btn secondary"
      href="../index.php?page=treinamentos">Ver página pública</a>
  </div>
  <?php if ($flash): ?>
    <div class="flash <?= e($type) ?>"><?= e($flash) ?></div><?php endif; ?>
  <div class="grid-2">
    <div class="form-card">
      <h3><?= $edit ? 'Editar treinamento' : 'Novo treinamento' ?></h3>
      <form method="post">
        <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
        <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
        <label>Título</label>
        <input class="input" type="text" name="titulo" value="<?= e($edit['titulo'] ?? '') ?>" required>
        <label>Categoria</label>
        <input class="input" type="text" name="categoria" value="<?= e($edit['categoria'] ?? '') ?>">
        <label>Descrição</label>
        <textarea name="descricao"><?= e($edit['descricao'] ?? '') ?></textarea>
        <label>Link</label>
        <input class="input" type="url" name="link" value="<?= e($edit['link'] ?? '') ?>">
        <button class="btn" type="submit"><?= $edit ? 'Salvar alterações' : 'Cadastrar treinamento' ?></button>
        <?php if ($edit): ?><a class="btn secondary" href="treinamentos.php"
            style="display:inline-block;margin-left:8px;">Cancelar</a><?php endif; ?>
      </form>
    </div>
    <div class="form-card">
      <div class="toolbar">
        <h3 style="margin:0;">Lista de treinamentos</h3>
        <div class="search-box" style="flex:1;"><input id="searchAdminTreinamentos" type="text"
            placeholder="Pesquisar..." onkeyup="filterTable('searchAdminTreinamentos','adminTreinamentosTable')"></div>
      </div>
      <div class="card">
        <table id="adminTreinamentosTable">
          <thead>
            <tr>
              <th>Título</th>
              <th>Categoria</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td><?= e($item['titulo']) ?></td>
                <td><?= e($item['categoria']) ?></td>
                <td class="actions"><a class="btn secondary" href="?edit=<?= $item['id'] ?>">Editar</a><a
                    class="btn danger" onclick="return confirm('Deseja excluir este treinamento?')"
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