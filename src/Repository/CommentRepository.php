<?php


namespace App\Repository;


use App\Entity\Comment;
use App\Entity\Post;
use Lib\AbstractRepository;

class CommentRepository extends AbstractRepository
{
    private UserRepository $userManager;

    private PostRepository $postManager;


    /**
     * CommentRepository constructor.
     */
    public function __construct()
    {
        $this->userManager = new UserRepository();
        $this->postManager = new PostRepository();
    }


    /**
     * Create new comment
     *
     * @param Comment $comment
     */
    public function create(Comment $comment) : void
    {
        $query = $this->getPDO()->prepare('
            INSERT INTO comment (date, text, status, user_id, post_id)
            VALUES (NOW(), :text, :status, :user, :post)
        ');

        $query->execute([
            'text' => $comment->getText(),
            'status' => $comment->getStatus(),
            'user' => $comment->getUser()->getId(),
            'post' => $comment->getPost()->getId()
        ]);
    }


    /**
     * Return all comments
     *
     * @return array|Comment[]
     */
    public function findAll() : array
    {
        $query = $this->getPDO()->prepare('
            SELECT comment.*, post.slug, user.email
            FROM comment
            INNER JOIN post ON comment.post_id = post.id
            INNER JOIN user ON comment.user_id = user.id
            ORDER BY date DESC
        ');

        $query->execute([]);

        $comments = [];

        foreach($query->fetchAll() as $comment)
        {
            $instance = new Comment();
            $instance->setId($comment['id']);
            $instance->setCreatedAt($comment['date']);
            $instance->setText($comment['text']);
            $instance->setStatus($comment['status']);
            $instance->setUser($this->userManager->findOne($comment['email']));
            $instance->setPost($this->postManager->findOne($comment['slug']));

            $comments[] = $instance;
        }

         return $comments;
    }


    /**
     * Return all comments by post
     *
     * @param Post $post
     * @return array|Comment[]
     */
    public function findByPost(Post $post) : array
    {
        $query = $this->getPDO()->prepare('
            SELECT comment.*, post.slug, user.email
            FROM comment
            INNER JOIN post ON comment.post_id = post.id
            INNER JOIN user ON comment.user_id = user.id
            WHERE post_id = :post
            ORDER BY date DESC
        ');

        $query->execute(['post' => $post->getId()]);

        $comments = [];

        foreach($query->fetchAll() as $comment)
        {
            $instance = new Comment();
            $instance->setId($comment['id']);
            $instance->setCreatedAt($comment['date']);
            $instance->setText($comment['text']);
            $instance->setStatus(($comment['status']));
            $instance->setUser($this->userManager->findOne($comment['email']));
            $instance->setPost($this->postManager->findOne($comment['slug']));

            $comments[] = $instance;
        }

        return $comments;
    }


    /**
     * Return one comment
     *
     * @param int $id
     * @return Comment|null
     */
    public function findOne(int $id)
    {
        $query = $this->getPDO()->prepare('
            SELECT comment.*, post.slug, user.email
            FROM comment
            INNER JOIN post ON comment.post_id = post.id
            INNER JOIN user ON comment.user_id = user.id
            WHERE comment.id = :id
        ');

        $query->execute(['id' => $id]);

        $comment = $query->fetch();

        if(!$comment)
        {
            return null;
        }

        $instance = new Comment();
        $instance->setId($comment['id']);
        $instance->setCreatedAt($comment['date']);
        $instance->setText($comment['text']);
        $instance->setStatus($comment['status']);
        $instance->setUser($this->userManager->findOne($comment['email']));
        $instance->setPost($this->postManager->findOne($comment['slug']));

        return $instance;
    }


    /**
     * Delete comment
     *
     * @param Comment $comment
     */
    public function delete(Comment $comment) : void
    {
        $query = $this->getPDO()->prepare('
            DELETE FROM comment
            WHERE id = :id
        ');

        $query->execute([
            'id' => $comment->getId()
        ]);
    }


    /**
     * Report Comment
     *
     * @param Comment $comment
     */
    public function report(Comment $comment) : void
    {
        $query = $this->getPDO()->prepare('
            UPDATE comment
            SET status = :status
            WHERE id = :id
        ');

        $query->execute([
            'status' => $comment->getStatus(),
            'id' => $comment->getId()
        ]);
    }


    /**
     * Return count reported comments
     *
     * @return int
     */
    public function countReported() : int
    {
        $query = $this->getPDO()->prepare('
            SELECT COUNT(id) AS reported
            FROM comment
            WHERE status = :status
        ');

        $query->execute([
            'status' => 'reported'
        ]);

        $comments = $query->fetch();

        return $comments['reported'];
    }
}