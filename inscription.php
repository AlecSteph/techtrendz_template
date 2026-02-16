<?php
require_once 'lib/config.php';
require_once 'lib/tools.php';
require_once 'lib/pdo.php';
require_once 'lib/user.php';

require_once 'templates/header.php';

$errors = [];
$messages = [];
if (isset($_POST['addUser'])) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $errors[] = "Tous les champs sont obligatoires";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    } elseif (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
    } else {
        if (addUser($pdo, $first_name, $last_name, $email, $password)) {
            $messages[] = "Merci pour votre inscription ! Vous pouvez maintenant vous connecter.";
        } else {
            $errors[] = "Une erreur s'est produite lors de votre inscription";
        }
    }
}

?>
    <h1>Inscription</h1>

    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $message): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="first_name" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="first_name" name="first_name">
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Nom</label>
            <input type="text" class="form-control" id="last_name" name="last_name">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de psse</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <input type="submit" name="addUser" class="btn btn-primary" value="Enregistrer">

    </form>

    <?php
require_once 'templates/footer.php';
?>