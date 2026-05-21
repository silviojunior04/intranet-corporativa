<?php
require_once __DIR__ . '/../config/auth.php';
require_login();
$flash = '';
$type = 'success';
$edit = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';
    $id = (int)($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $url = trim($_POST['url'] ?? '');

    if ($nome === '' || $url === '') {
        $flash = 'Preencha nome e URL.';
        $type = 'error';
    } else {
        if ($action === 'update' && $id > 0) {
            $stmt = $pdo->prepare('UPDATE links_uteis SET nome=?, categoria=?, url=? WHERE id=?');
            $stmt->execute([$nome, $categoria, $url, $id]);
            $flash = 'Link atualizado com sucesso.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO links_uteis (nome, categoria, url) VALUES (?, ?, ?)');
            $stmt->execute([$nome, $categoria, $url]);
            $flash = 'Link cadastrado com sucesso.';
        }
    }
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM links_uteis WHERE id=?');
    $stmt->execute([$id]);
    header('Location: links.php?ok=deleted');
    exit;
}
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM links_uteis WHERE id=?');
    $stmt->execute([$id]);
    $edit = $stmt->fetch();
}
if (isset($_GET['ok']) && $_GET['ok'] === 'deleted') {
    $flash = 'Link removido com sucesso.';
}
$items = $pdo->query('SELECT * FROM links_uteis ORDER BY nome ASC')->fetchAll();
$pageTitle = 'Gerenciar Links';
$basePath = '../';
include __DIR__ . '/../partials/header.php';
?>
<div class="topbar topbar-admin admin-topbar"><div class="admin-brand-wrap">
  <A href="/intranet_generica">
  <span class="topbar-logo logo-placeholder">LOGO AQUI</span></A>
  </div><div class="actions admin-actions"><a class="btn secondary" href="dashboard.php">Dashboard</a><a class="btn" href="logout.php">Sair</a></div></div>
<div class="content content-admin">
  <div class="header-row"><h1 class="section-title" style="margin:0;">GERENCIADOR DE LINKS</h1><a class="btn secondary" href="../index.php?page=links">Ver página pública</a></div>
  <?php if ($flash): ?><div class="flash <?= e($type) ?>"><?= e($flash) ?></div><?php endif; ?>
  <div class="grid-2">
    <div class="form-card">
      <h3><?= $edit ? 'Editar link' : 'Novo link' ?></h3>
      <form method="post">
        <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
        <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
        <label>Nome</label>
        <input class="input" type="text" name="nome" value="<?= e($edit['nome'] ?? '') ?>" required>
        <label>Categoria</label>
        <input class="input" type="text" name="categoria" value="<?= e($edit['categoria'] ?? '') ?>">
        <label>URL</label>
        <input class="input" type="url" name="url" value="<?= e($edit['url'] ?? '') ?>" required>
        <button class="btn" type="submit"><?= $edit ? 'Salvar alterações' : 'Cadastrar link' ?></button>
        <?php if ($edit): ?><a class="btn secondary" href="links.php" style="display:inline-block;margin-left:8px;">Cancelar</a><?php endif; ?>
      </form>
    </div>
    <div class="form-card">
      <div class="toolbar"><h3 style="margin:0;">Lista de links</h3><div class="search-box" style="flex:1;"><input id="searchAdminLinks" type="text" placeholder="Pesquisar..." onkeyup="filterTable('searchAdminLinks','adminLinksTable')"></div></div>
      <div class="card">
        <table id="adminLinksTable">
          <thead><tr><th>Nome</th><th>Categoria</th><th>Ações</th></tr></thead>
          <tbody>
          <?php foreach ($items as $item): ?>
            <tr>
              <td><?= e($item['nome']) ?></td>
              <td><?= e($item['categoria']) ?></td>
              <td class="actions"><a class="btn secondary" href="?edit=<?= $item['id'] ?>">Editar</a><a class="btn danger" onclick="return confirm('Deseja excluir este link?')" href="?delete=<?= $item['id'] ?>">Excluir</a></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
