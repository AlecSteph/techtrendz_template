<?php
require_once __DIR__ . "/lib/config.php";
require_once __DIR__ . "/lib/session.php";
require_once __DIR__ . "/lib/pdo.php";
require_once __DIR__ . "/lib/article.php";
require_once __DIR__ . "/lib/category.php";
require_once __DIR__ . "/templates/header.php";

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

$articles = getArticles($pdo, _FRONT_ITEM_PER_PAGE_, $page, $category_id);
$totalArticles = getTotalArticles($pdo, $category_id);
$totalPages = ceil($totalArticles / _FRONT_ITEM_PER_PAGE_);
$categories = getCategories($pdo);

?>

<h1>TechTrendz Actualités</h1>

<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" class="d-flex gap-2">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= $category_id == $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="page" value="1">
        </form>
    </div>
</div>

<div class="row text-center">
    <?php if (!empty($articles)): ?>
        <?php foreach ($articles as $article): ?>
            <div class="col-md-4 my-2 d-flex">
                <div class="card">
                    <?php if ($article['image']): ?>
                        <img src="<?= htmlspecialchars($article['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($article['title']) ?>">
                    <?php else: ?>
                        <img src="/assets/images/default-article.jpg" class="card-img-top" alt="<?= htmlspecialchars($article['title']) ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($article['title']) ?></h5>
                        <?php if ($article['category_name']): ?>
                            <small class="text-muted"><?= htmlspecialchars($article['category_name']) ?></small>
                        <?php endif; ?>
                        <p class="card-text"><?= substr(htmlspecialchars($article['content']), 0, 100) ?>...</p>
                        <a href="actualite.php?id=<?= $article['id'] ?>" class="btn btn-primary">Lire la suite</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <p>Aucun article trouvé.</p>
        </div>
    <?php endif; ?>
</div>


<?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page - 1 ?><?= $category_id ? '&category=' . $category_id : '' ?>">Précédent</a>
                </li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page || abs($i - $page) <= 2 || $i == 1 || $i == $totalPages): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?><?= $category_id ? '&category=' . $category_id : '' ?>"><?= $i ?></a>
                    </li>
                <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?><?= $category_id ? '&category=' . $category_id : '' ?>">Suivant</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php require_once __DIR__ . "/templates/footer.php"; ?>