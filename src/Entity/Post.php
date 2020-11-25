<?php


namespace App\Entity;



use DateTime;

class Post
{
    /**
     * @var int
     */
    private $id;


    /**
     * @var string
     */
    private $title;


    /**
     * @var string
     */
    private $cover;


    /**
     * @var string
     */
    private $date;


    /**
     * @var string
     */
    private $text;


    /**
     * @var string
     */
    private $slug;


    /**
     * @var int
     */
    private $user_id;


    /**
     * @var int
     */
    private $category_id;


    /**
     * Post entity constructor.
     *
     * @param int $id
     * @param string $title
     * @param string $cover
     * @param string $date
     * @param string $text
     * @param string $slug
     * @param int $user_id
     * @param int $category_id
     */
    public function __construct(int $id, string $title, string $cover, string $date, string $text, string $slug, int $user_id, int
    $category_id)
    {
        $this->setId($id);
        $this->setTitle($title);
        $this->setCover($cover);
        $this->setDate($date);
        $this->setText($text);
        $this->setSlug($slug);
        $this->setUserId($user_id);
        $this->setCategoryId($category_id);
    }


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
    public function getTitle(): string
    {
        return $this->title;
    }


    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }


    /**
     * @return string
     */
    public function getCover(): string
    {
        return $this->cover;
    }


    /**
     * @param string $cover
     */
    public function setCover(string $cover): void
    {
        $this->cover = $cover;
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
    public function getSlug(): string
    {
        return $this->slug;
    }


    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }


    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }


    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }


    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->category_id;
    }


    /**
     * @param int $category_id
     */
    public function setCategoryId(int $category_id): void
    {
        $this->category_id = $category_id;
    }
}