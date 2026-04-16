<?php

class Database {
    
    private string $servername = 'db_mysql'; 
    private string $dbName = 'paikw_database';
    private string $username = 'root';
    private string $password = 'root';
    private PDO $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $dsn = "mysql:host={$this->servername};dbname={$this->dbName}";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            exit('Database connection failed! ' . $e->getMessage());
        }
    }

    public function createUser(string $username, string $email, string $password): bool
    {
        try {
            $sql = 'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)';
            $stmt = $this->conn->prepare($sql);
            
            return $stmt->execute([
                ':username' => $username,
                ':email'    => $email,
                ':password' => $password
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception("Ten adres e-mail jest już zajęty.");
            }
            throw $e;
        }
    }

    // sprawdzanie czy email nie jest zajęty
    public function fetchByEmail(string $email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':email' => $email
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // przeglad kategori
    public function getCategories(): array 
    {
        $sql = 'SELECT * FROM categories ORDER BY name ASC';
        $stmt = $this->conn->prepare($sql); 
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // dodawanie posta
    public function addPost(string $title, string $content, int $category_id, int $author_id, string $image_path): bool
    {
            $sql = 'INSERT INTO posts (title, content, category_id,author_id, image) VALUES (:title, :content, :category_id, :author_id, :image)';
            $stmt = $this->conn->prepare($sql);
            
            return $stmt->execute([
                ':title' => $title,
                ':content'    => $content,
                ':category_id' => $category_id,
                ':author_id' => $author_id,
                ':image' => $image_path
            ]); 
    }

    // Wyświetlanie wszystkich postów
    public function getPosts(array $category_ids = [], string $sort_by = 'data', string $search = ''): array
    {
        // Bazowe zapytanie
        $sql = 'SELECT posts.*, categories.name AS category_name, users.username AS user_name, users.avatar AS user_avatar, users.id AS user_id,
                (SELECT COUNT(*) FROM ratings WHERE post_id = posts.id AND rating_type = "like") as likes_count,
                (SELECT COUNT(*) FROM ratings WHERE post_id = posts.id AND rating_type = "dislike") as dislikes_count,
                (SELECT COUNT(*) FROM comments WHERE post_id = posts.id) as comments_count
                FROM posts 
                JOIN categories ON posts.category_id = categories.id 
                JOIN users ON posts.author_id = users.id';

        $where_conditions = [];
        $params = [];

        if (!empty($category_ids)) {
            $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
            $where_conditions[] = "posts.category_id IN ($placeholders)";
            $params = array_merge($params, $category_ids);
        }

        if (!empty($search)) {
            $where_conditions[] = "posts.title LIKE ?";
            $params[] = "%$search%"; 
        }

        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(" AND ", $where_conditions);
        }

        switch ($sort_by) {
            case 'most_liked':
                $sql .= ' ORDER BY likes_count DESC';
                break;
            case 'most_disliked':
                $sql .= ' ORDER BY dislikes_count DESC';
                break;
            default:
                $sql .= ' ORDER BY posts.data DESC';
                break;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // podglad pojedynczego posta
    public function getPostById($id) {
        $sql = "SELECT p.*, u.username as user_name, c.name as category_name, u.id AS user_id, u.avatar AS user_avatar,
                (SELECT COUNT(*) FROM ratings WHERE post_id = p.id AND rating_type = 'like') as likes_count,
                (SELECT COUNT(*) FROM ratings WHERE post_id = p.id AND rating_type = 'dislike') as dislikes_count
                FROM posts p
                JOIN users u ON p.author_id = u.id
                JOIN categories c ON p.category_id = c.id
                WHERE p.id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // dodjawanie lików i dislaików
    public function Rate($postId, $userId, $type) {
        try {
            $sql = "SELECT rating_type FROM ratings WHERE post_id = ? AND user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$postId, $userId]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($current) {
                if ($current['rating_type'] === $type) {
                    $del = "DELETE FROM ratings WHERE post_id = ? AND user_id = ?";
                    $stmt = $this->conn->prepare($del);
                    $stmt->execute([$postId, $userId]);
                } else {
                    $upd = "UPDATE ratings SET rating_type = ? WHERE post_id = ? AND user_id = ?";
                    $stmt = $this->conn->prepare($upd);
                    $stmt->execute([$type, $postId, $userId]);
                }
            } else {
                $ins = "INSERT INTO ratings (post_id, user_id, rating_type) VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($ins);
                $stmt->execute([$postId, $userId, $type]);
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getUserVote(int $postId, int $userId): ?string {
        $sql = "SELECT rating_type FROM ratings WHERE post_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$postId, $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['rating_type'] : null;
    }


    // dodawnie komentarzy
    public function addComment($postId, $userId, $content, $parentId = null) {
        $pId = (!empty($parentId)) ? $parentId : null;
        $sql = "INSERT INTO comments (post_id, user_id, content, parent_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$postId, $userId, $content, $pId]);
    }

    // Pobieranie komentarzy
    public function getCommentsByPost($post_id) {
        $sql = "SELECT c.*, u.username, u.avatar,
                (SELECT COUNT(*) FROM comment_ratings WHERE comment_id = c.id AND rating_type = 'like') as likes_count,
                (SELECT COUNT(*) FROM comment_ratings WHERE comment_id = c.id AND rating_type = 'dislike') as dislikes_count
                FROM comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.post_id = ? 
                ORDER BY c.data ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$post_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Sprawdzanie głosu użytkownika na konkretny komentarz
    public function getCommentVote($commentId, $userId) {
        $sql = "SELECT rating_type FROM comment_ratings WHERE comment_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$commentId, $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['rating_type'] : null;
    }

    // Obsługa lajkowania komentarza (analogicznie do Rate dla postów)
    public function RateComment($commentId, $userId, $type) {
        $current = $this->getCommentVote($commentId, $userId);

        if ($current) {
            if ($current === $type) {
                $sql = "DELETE FROM comment_ratings WHERE comment_id = ? AND user_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$commentId, $userId]);
            } else {
                $sql = "UPDATE comment_ratings SET rating_type = ? WHERE comment_id = ? AND user_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$type, $commentId, $userId]);
            }
        } else {
            $sql = "INSERT INTO comment_ratings (comment_id, user_id, rating_type) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$commentId, $userId, $type]);
        }
    }
    
    // Obsługa znajdowania komenatrza po id użytkwonika
    public function getCommentById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obsługa usuwania komentarz
    public function deleteComment($id) {
        $stmt = $this->conn->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Pobieranie profilu
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // wyswietalne postów konkretengo użytkwonika
    public function getPostsByUserId(int $userId): array
    {
        $sql = 'SELECT posts.*, categories.name AS category_name, users.username AS user_name,
                (SELECT COUNT(*) FROM ratings WHERE post_id = posts.id AND rating_type = "like") as likes_count,
                (SELECT COUNT(*) FROM ratings WHERE post_id = posts.id AND rating_type = "dislike") as dislikes_count,
                (SELECT COUNT(*) FROM comments WHERE post_id = posts.id) as comments_count
                FROM posts 
                JOIN categories ON posts.category_id = categories.id 
                JOIN users ON posts.author_id = users.id
                WHERE posts.author_id = ? 
                ORDER BY posts.data DESC';

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // usuwanie posta
    public function deletePost(int $id): bool 
    {
        $sql = 'DELETE FROM posts WHERE id = :id';
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // edycjia posta
    public function updatePost(int $id, string $title, string $content, int $categoryId, ?string $image): bool 
    {
        $sql = 'UPDATE posts 
                SET title = :title, 
                    content = :content, 
                    category_id = :category_id, 
                    image = :image 
                WHERE id = :id';
                
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':category_id' => $categoryId,
            ':image' => $image, 
            ':id' => $id
        ]);
    }

    // edycjia profilu
    public function updateUserFull($id, $username, $email, $bio, $avatar, $password): bool 
    {
        $sql = "UPDATE users SET 
                username = :username, 
                email = :email, 
                bio = :bio, 
                avatar = :avatar, 
                password = :password 
                WHERE id = :id";
                
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':bio' => $bio,
            ':avatar' => $avatar,
            ':password' => $password,
            ':id' => $id
        ]);
    }

    // usuwanie konta
        public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }


    // Sprawdza czy zalogowany user obserwuje profil
    public function isFollowing($followerId, $followedId) {
        $stmt = $this->conn->prepare("SELECT 1 FROM followers WHERE follower_id = ? AND followed_id = ?");
        $stmt->execute([$followerId, $followedId]);
        return (bool)$stmt->fetch();
    }

    // Liczenie ilu użytkownik ma obserwujących
    public function getFollowersCount($userId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM followers WHERE followed_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    // obserwuj / nie obeserwuj
    public function toggleFollow($followerId, $followedId) {
        if ($this->isFollowing($followerId, $followedId)) {
            $stmt = $this->conn->prepare("DELETE FROM followers WHERE follower_id = ? AND followed_id = ?");
        } else {
            $stmt = $this->conn->prepare("INSERT INTO followers (follower_id, followed_id) VALUES (?, ?)");
        }
        return $stmt->execute([$followerId, $followedId]);
    }

    // posty obserwowanych użytkowników
    public function getFollowedPosts($userId) {
        $stmt = $this->conn->prepare("
            SELECT p.*, u.username, u.avatar, c.name as category_name,
                (SELECT COUNT(*) FROM ratings WHERE post_id = p.id AND rating_type = 'like') as likes_count,
                (SELECT COUNT(*) FROM ratings WHERE post_id = p.id AND rating_type = 'dislike') as dislikes_count,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
            FROM posts p
            JOIN users u ON p.author_id = u.id
            JOIN categories c ON p.category_id = c.id
            JOIN followers f ON p.author_id = f.followed_id
            WHERE f.follower_id = ?
            ORDER BY p.data DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // szukanie użytkowników
    public function searchUsers($query) {
        $stmt = $this->conn->prepare("SELECT id, username, avatar FROM users WHERE username LIKE ? LIMIT 10");
        $stmt->execute(["%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}