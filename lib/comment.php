<?php

function getCommentsByArticle(PDO $pdo, int $article_id): array
{
    $sql = "SELECT c.*, u.first_name, u.last_name 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.article_id = :article_id 
            ORDER BY c.created_at DESC";
    
    $query = $pdo->prepare($sql);
    $query->bindValue(':article_id', $article_id, PDO::PARAM_INT);
    $query->execute();
    
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function addComment(PDO $pdo, int $article_id, int $user_id, string $content): bool
{
    $sql = "INSERT INTO comments (article_id, user_id, content) VALUES (:article_id, :user_id, :content)";
    
    $query = $pdo->prepare($sql);
    $query->bindValue(':article_id', $article_id, PDO::PARAM_INT);
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->bindValue(':content', $content, PDO::PARAM_STR);
    
    return $query->execute();
}

function deleteComment(PDO $pdo, int $comment_id, int $user_id): bool
{
    $sql = "DELETE FROM comments WHERE id = :id AND user_id = :user_id";
    
    $query = $pdo->prepare($sql);
    $query->bindValue(':id', $comment_id, PDO::PARAM_INT);
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    
    return $query->rowCount() > 0;
}
