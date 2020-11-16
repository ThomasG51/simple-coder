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
            VALUES(:tags_id, $post_id)
        ');

        $query->execute([
            'tags_id' => $tagsId,
            'post_id' => $postId
        ]);
    }
}