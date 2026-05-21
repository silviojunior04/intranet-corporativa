<?php
require_once __DIR__ . '/../config/auth.php';
require_login();
$flash = '';
$type = 'success';
$edit = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? 'create';
  $id = (int) ($_POST['id'] ?? 0);
  $title = trim($_POST['title'] ?? '');
  $message = trim($_POST['message'] ?? '');
  $style = trim($_POST['style_type'] ?? 'info');
  $buttonLabel = trim($_POST['button_label'] ?? '');
  $buttonUrl = trim($_POST['button_url'] ?? '');
  $startsAt = trim($_POST['starts_at'] ?? '');
  $endsAt = trim($_POST['ends_at'] ?? '');
  $isActive = isset($_POST['is_active']) ? 1 : 0;

  if ($message === '' || $startsAt === '' || $endsAt === '') {
    $flash = 'Preencha mensagem, início e fim do aviso.';
    $type = 'error';
  } else {
    if ($action === 'update' && $id > 0) {
      $stmt = $pdo->prepare('UPDATE popups SET title=?, message=?, style_type=?, button_label=?, button_url=?, starts_at=?, ends_at=?, is_active=? WHERE id=?');
      $stmt->execute([$title, $message, $style, $buttonLabel, $buttonUrl, $startsAt, $endsAt, $isActive, $id]);
      $flash = 'Popup atualizado com sucesso.';
    } else {
      $stmt = $pdo->prepare('INSERT INTO popups (title, message, style_type, button_label, button_url, starts_at, ends_at, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
      $stmt->execute([$title, $message, $style, $buttonLabel, $buttonUrl, $startsAt, $endsAt, $isActive]);
      $flash = 'Popup cadastrado com sucesso.';
    }
  }
}
if (isset($_GET['delete'])) {
  $id = (int) $_GET['delete'];
  $stmt = $pdo->prepare('DELETE FROM popups WHERE id=?');
  $stmt->execute([$id]);
  header('Location: popups.php?ok=deleted');
  exit;
}
if (isset($_GET['edit'])) {
  $id = (int) $_GET['edit'];
  $stmt = $pdo->prepare('SELECT * FROM popups WHERE id=?');
  $stmt->execute([$id]);
  $edit = $stmt->fetch();
}
if (isset($_GET['ok']) && $_GET['ok'] === 'deleted') {
  $flash = 'Popup removido com sucesso.';
}
$items = $pdo->query('SELECT * FROM popups ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Gerenciar Popups';
$basePath = '../';
include __DIR__ . '/../partials/header.php';
?>
<div class="topbar topbar-admin admin-topbar">
  <div class="admin-brand-wrap"><a href="/intranet_generica">
      <span class="topbar-logo logo-placeholder">LOGO AQUI</span>
    </a></div>
  <div class="actions admin-actions"><a class="btn secondary" href="dashboard.php">Dashboard</a><a class="btn"
      href="logout.php">Sair</a></div>
</div>
<div class="content content-admin">
  <div class="header-row">
    <h1 class="section-title" style="margin:0;">POPUPS E AVISOS</h1><a class="btn secondary" href="../index.php">Ver
      intranet</a>
  </div>
  <?php if ($flash): ?>
    <div class="flash <?= e($type) ?>"><?= e($flash) ?></div><?php endif; ?>
  <div class="grid-2">
    <div class="form-card">
      <h3><?= $edit ? 'Editar aviso' : 'Novo aviso' ?></h3>
      <form method="post">
        <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
        <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
        <label>Título</label>
        <input class="input" type="text" name="title" value="<?= e($edit['title'] ?? '') ?>"
          placeholder="Ex.: Comunicado importante">
        <label>Mensagem</label>
        <textarea name="message" required><?= e($edit['message'] ?? '') ?></textarea>
        <label>Estilo do popup</label>
        <select class="input" name="style_type">
          <?php foreach (['info' => 'Informativo', 'warning' => 'Alerta', 'success' => 'Sucesso'] as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= (($edit['style_type'] ?? 'info') === $key) ? 'selected' : '' ?>>
              <?= e($label) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <label>Texto do botão (opcional)</label>
        <input class="input" type="text" name="button_label" value="<?= e($edit['button_label'] ?? '') ?>"
          placeholder="Ex.: Abrir comunicado">
        <label>URL do botão (opcional)</label>
        <input class="input" type="url" name="button_url" value="<?= e($edit['button_url'] ?? '') ?>"
          placeholder="https://...">
        <label>Início da exibição</label>
        <input class="input" type="datetime-local" name="starts_at"
          value="<?= !empty($edit['starts_at']) ? date('Y-m-d\TH:i', strtotime($edit['starts_at'])) : '' ?>" required>
        <label>Fim da exibição</label>
        <input class="input" type="datetime-local" name="ends_at"
          value="<?= !empty($edit['ends_at']) ? date('Y-m-d\TH:i', strtotime($edit['ends_at'])) : '' ?>" required>
        <label class="check-inline"><input type="checkbox" name="is_active" <?= (($edit['is_active'] ?? 1) == 1) ? 'checked' : '' ?>> Popup ativo</label>
        <button class="btn" type="submit"><?= $edit ? 'Salvar alterações' : 'Cadastrar aviso' ?></button>
        <?php if ($edit): ?><a class="btn secondary" href="popups.php"
            style="display:inline-block;margin-left:8px;">Cancelar</a><?php endif; ?>
      </form>
    </div>
    <div class="form-card">
      <div class="toolbar">
        <h3 style="margin:0;">Avisos cadastrados</h3>
        <div class="search-box" style="flex:1;"><input id="searchAdminPopups" type="text" placeholder="Pesquisar..."
            onkeyup="filterTable('searchAdminPopups','adminPopupsTable')"></div>
      </div>
      <div class="card">
        <table id="adminPopupsTable">
          <thead>
            <tr>
              <th>Título</th>
              <th>Período</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td><?= e($item['title']) ?></td>
                <td><?= e(date('d/m/Y H:i', strtotime($item['starts_at']))) ?> até
                  <?= e(date('d/m/Y H:i', strtotime($item['ends_at']))) ?>
                </td>
                <td><?= $item['is_active'] ? 'Ativo' : 'Inativo' ?></td>
                <td class="actions"><a class="btn secondary" href="?edit=<?= $item['id'] ?>">Editar</a><a
                    class="btn danger" onclick="return confirm('Deseja excluir este aviso?')"
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