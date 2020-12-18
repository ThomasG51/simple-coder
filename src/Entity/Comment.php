<?php


namespace App\Entity;


class Comment
{
    private int $id;

    private string $date;

    private string $text;

    private string $status;

    private User $user;

    private Post $post;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }


    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }


    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }


    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }


    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }


    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }


    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }


    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }


    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }


    /**
     * @param Post $post
     */
    public function setPost(Post $post): void
    {
        $this->post = $post;
    }
}