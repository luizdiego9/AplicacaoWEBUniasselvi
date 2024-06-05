<?php

declare(strict_types=1);

namespace Seminario\Mvc\Repository;

use PDO;
use Seminario\Mvc\Entity\News;

class NewsRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function add(News $news): bool
    {
        $sql = 'INSERT INTO news (title, content, author, date) VALUES (?, ?, ?, ?)';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $news->getTitle());
        $statement->bindValue(2, $news->getContent());
        $statement->bindValue(3, $news->getAuthor());
        $statement->bindValue(4, $news->getDate());

        $result = $statement->execute();
        $id = $this->pdo->lastInsertId();

        $news->setId(intval($id));

        return $result;
    }

    public function remove(int $id): bool
    {
        $sql = 'DELETE FROM news WHERE id = ?';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $id);

        return $statement->execute();
    }

    public function update(News $news): bool
    {
        $sql = 'UPDATE news SET title = ?, content = ?, author = ?, date = ? WHERE id = ?';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $news->getTitle());
        $statement->bindValue(2, $news->getContent());
        $statement->bindValue(3, $news->getAuthor());
        $statement->bindValue(4, $news->getDate());
        $statement->bindValue(5, $news->getId());

        return $statement->execute();
    }

    /**
     * @return News[]
     */
    public function all(): array
    {
        $newsList = $this->pdo
            ->query('SELECT * FROM news;')
            ->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(
            $this->hydrateNews(...),
            $newsList
        );
    }

    public function find(int $id)
    {
        $statement = $this->pdo->prepare('SELECT * FROM news WHERE id = ?;');
        $statement->bindValue(1, $id, \PDO::PARAM_INT);
        $statement->execute();

        return $this->hydrateNews($statement->fetch(\PDO::FETCH_ASSOC));
    }

    private function hydrateNews(array $newsList): News
    {
        $newsList['date'] = new \DateTime($newsList['date']);
        
        $news = new News($newsList['title'], $newsList['content'], $newsList['author'], $newsList['date']);

        $news->setId($newsList['id']);

        return $news;
    }
}
