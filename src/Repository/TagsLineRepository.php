<?php


namespace App\Repository;


use Lib\AbstractRepository;

class TagsLineRepository extends AbstractRepository
{
    /**
     * Create new TagsLine
     *
     * @param int $tagsId
     * @param int $postId
     */
    public function create(int $tagsId, int $postId) : void
    {
        $query = $this->getPDO()->prepare('
            INSERT INTO tags_lines(tags_id, post_id)
            VALUES(:tags_id, :post_id)
        ');

        $query->execute([
            'tags_id' => $tagsId,
            'post_id' => $postId
        ]);
    }


    /**
     * Return all tags form a specific post
     *
     * @param string $slug
     * @return array
     */
    public function findTagsByPost(string $slug)
    {
        $query = $this->getPDO()->prepare('
            SELECT tags.name 
            FROM tags_lines 
            LEFT JOIN tags ON tags_lines.tags_id = tags.id 
            LEFT JOIN post ON tags_lines.post_id = post.id 
            WHERE post.slug = :slug
        ');

        $query->execute([
            'slug' => $slug
        ]);

        return $query->fetchAll();
    }
}