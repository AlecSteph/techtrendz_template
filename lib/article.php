<?php

function getArticleById(PDO $pdo, int $id):array|bool
{
    $query = $pdo->prepare("SELECT * FROM articles WHERE id = :id");
    $query->bindValue(":id", $id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

function getArticles(PDO $pdo, ?int $limit = null, ?int $page = null, ?int $category_id = null):array|bool
{
    $sql = "SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id";
    
    if ($category_id !== null) {
        $sql .= " WHERE a.category_id = :category_id";
    }
    
    $sql .= " ORDER BY a.id DESC";
    
    if ($limit !== null) {
        $offset = 0;
        if ($page !== null && $page > 1) {
            $offset = ($page - 1) * $limit;
        }
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $query = $pdo->prepare($sql);
        $query->bindValue(':limit', $limit, PDO::PARAM_INT);
        $query->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        if ($category_id !== null) {
            $query->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        }
    } else {
        $query = $pdo->prepare($sql);
        if ($category_id !== null) {
            $query->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        }
    }
    
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getTotalArticles(PDO $pdo, ?int $category_id = null):int|bool
{
    $sql = "SELECT COUNT(*) as total FROM articles";
    
    if ($category_id !== null) {
        $sql .= " WHERE category_id = :category_id";
        $query = $pdo->prepare($sql);
        $query->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    } else {
        $query = $pdo->prepare($sql);
    }
    
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function saveArticle(PDO $pdo, string $title, string $content, ?string $image, int $category_id, ?int $id = null):bool 
{
    if ($id === null) {
        $sql = "INSERT INTO articles (title, content, image, category_id) VALUES (:title, :content, :image, :category_id)";
        $query = $pdo->prepare($sql);
    } else {
        $sql = "UPDATE articles SET title = :title, content = :content, image = :image, category_id = :category_id WHERE id = :id";
        $query = $pdo->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
    }

    $query->bindValue(':title', $title, PDO::PARAM_STR);
    $query->bindValue(':content', $content, PDO::PARAM_STR);
    $query->bindValue(':image', $image, PDO::PARAM_STR);
    $query->bindValue(':category_id', $category_id, PDO::PARAM_INT);

    return $query->execute();  
}

function deleteArticle(PDO $pdo, int $id):bool
{
    $sql = "DELETE FROM articles WHERE id = :id";
    $query = $pdo->prepare($sql);
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    
    if ($query->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}