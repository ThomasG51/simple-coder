<?php


namespace App\Controller;


use App\Repository\TagsLineRepository;
use Lib\AbstractController;

class TagsLineController extends AbstractController
{
    /**
     * Create new tags line
     *
     * @param int $tags_id
     * @param int $post_id
     */
    public function create(int $tags_id, int $post_id)
    {
        $tagsLineManager = new TagsLineRepository();
        $tagsLineManager->create($tags_id, $post_id);
    }
}