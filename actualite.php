<?php
require_once __DIR__ . "/lib/config.php";
require_once __DIR__ . "/lib/session.php";
require_once __DIR__ . "/lib/pdo.php";
require_once __DIR__ . "/lib/article.php";
require_once __DIR__ . "/lib/comment.php";
require_once __DIR__ . "/templates/header.php";

$article = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $article = getArticleById($pdo, (int)$_GET['id']);
}

if (!$article) {
    header('Location: actualites.php');
    exit;
}

$comments = getCommentsByArticle($pdo, $article['id']);
$errors = [];
$messages = [];

if (isset($_POST['addComment']) && isset($_SESSION['user'])) {
    $content = trim($_POST['comment'] ?? '');
    
    if (empty($content)) {
        $errors[] = "Le commentaire ne peut pas être vide";
    } elseif (strlen($content) > 1000) {
        $errors[] = "Le commentaire ne peut pas dépasser 1000 caractères";
    } else {
        if (addComment($pdo, $article['id'], $_SESSION['user']['id'], $content)) {
            $messages[] = "Votre commentaire a été ajouté avec succès";
            $comments = getCommentsByArticle($pdo, $article['id']);
        } else {
            $errors[] = "Une erreur s'est produite lors de l'ajout du commentaire";
        }
    }
}
?>


<div class="row flex-lg-row-reverse align-items-center g-5 py-5">
    <div class="col-10 col-sm-8 col-lg-6">
        <?php if ($article['image']): ?>
            <img src="<?= htmlspecialchars($article['image']) ?>" class="d-block mx-lg-auto img-fluid" alt="<?= htmlspecialchars($article['title']) ?>" width="700" height="500" loading="lazy">
        <?php else: ?>
            <img src="/assets/images/default-article.jpg" class="d-block mx-lg-auto img-fluid" alt="<?= htmlspecialchars($article['title']) ?>" width="700" height="500" loading="lazy">
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <h1 class="display-5 fw-bold text-body-emphasis lh-1 mb-3"><?= htmlspecialchars($article['title']) ?></h1>
        <div class="lead">
            <?= nl2br(htmlspecialchars($article['content'])) ?>
        </div>
    </div>
</div>


<div class="row mt-5">
    <div class="col-12">
        <h3>Commentaires</h3>
        
        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $message): ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['user'])): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Ajouter un commentaire</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <textarea class="form-control" name="comment" rows="3" placeholder="Votre commentaire..." required></textarea>
                        </div>
                        <button type="submit" name="addComment" class="btn btn-primary">Ajouter le commentaire</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <a href="login.php">Connectez-vous</a> pour pouvoir laisser un commentaire.
            </div>
        <?php endif; ?>
        
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <?= htmlspecialchars($comment['first_name']) ?> <?= htmlspecialchars($comment['last_name']) ?>
                                </h6>
                                <p class="card-text"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                            </div>
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">Soyez le premier à commenter cet article !</p>
        <?php endif; ?>
    </div>
</div>


<?php require_once __DIR__ . "/templates/footer.php"; ?>