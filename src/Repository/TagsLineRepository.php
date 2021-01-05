<?php


namespace App\Repository;


use App\Entity\Tags;
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
    public function findTagsByPost(string $slug) : array
    {
        $query = $this->getPDO()->prepare('
            SELECT tags.* 
            FROM tags_lines 
            INNER JOIN tags ON tags_lines.tags_id = tags.id 
            INNER JOIN post ON tags_lines.post_id = post.id 
            WHERE post.slug = :slug
        ');

        $query->execute([
            'slug' => $slug
        ]);

        $tags = [];

        foreach($query->fetchAll() as $tag)
        {
            $instance = new Tags($tag['id'], $tag['name']);
            $tags[] = $instance;
        }

        return $tags;
    }


    /**
     * Update Tags
     *
     * @param int $tags_id
     * @param int $post_id
     */
    public function update(int $tags_id, int $post_id) : void
    {
        $query = $this->getPDO()->prepare('
            UPDATE tags_lines
            SET tags_id = :tags_id
            WHERE post_id = :post_id
        ');

        $query->execute([
            'tags_id' => $tags_id,
            'post_id' => $post_id
        ]);
    }


    /**
     * Delete tags_line
     *
     * @param int $tags_id
     */
    public function delete(int $tags_id) : void
    {
        $query = $this->getPDO()->prepare('
            DELETE FROM tags_lines
            WHERE tags_id = :tags_id
        ');

        $query->execute([
            'tags_id' => $tags_id
        ]);
    }
}