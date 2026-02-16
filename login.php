<?php
require_once 'lib/config.php';
require_once 'lib/session.php';
require_once 'lib/pdo.php';
require_once 'lib/user.php';

require_once 'templates/header.php';

$errors = [];
$messages = [];

if (isset($_POST['loginUser'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $errors[] = "Email et mot de passe sont obligatoires";
    } else {
        $user = verifyUserLoginPassword($pdo, $email, $password);
        
        if ($user) {
            $_SESSION['user'] = $user;
            
            if ($user['role'] === 'admin') {
                header('Location: admin/index.php');
                exit;
            } else {
                header('Location: index.php');
                exit;
            }
        } else {
            $errors[] = "Email ou mot de passe incorrect";
        }
    }
}

?>
    <h1>Login</h1>

    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de psse</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <input type="submit" name="loginUser" class="btn btn-primary" value="Se connecter">

    </form>

    <?php
require_once 'templates/footer.php';
?>