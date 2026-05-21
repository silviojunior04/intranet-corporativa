<?php
require_once __DIR__ . '/../config/auth.php';
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Usuário ou senha inválidos.';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin - Intranet Genérica</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <div class="login-wrap">
    <div class="login-card">
      <div class="logo-center">
        <a href="/intranet_generica">
        <span class="login-logo logo-placeholder logo-placeholder-login">LOGO AQUI</span>
        </a>
      </div>
      <h1 class="login-title">Login</h1>
      <br>
      <?php if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?>
      <form method="post">
        <label>Usuário</label>
        <input class="input" type="text" name="username" required>
        <label>Senha</label>
        <input class="input" type="password" name="password" required>
        <button class="btn" type="submit">Entrar</button>
        <a class="btn secondary" href="../index.php" style="display:inline-block;margin-left:8px;">Voltar à intranet</a>
      </form>
    </div>
  </div>
</body>
</html>
