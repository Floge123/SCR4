<?php

namespace Application\Entities;

class Rating
{
    public function __construct(
        private int $id,
        private string $creator,
        private int $productID,
        private string $createDate,
        private int $grade,
        private string $comment
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreator(): string
    {
        return $this->creator;
    }

    public function getProductID(): int
    {
        return $this->productID;
    }

    public function getCreateDate(): string
    {
        return $this->createDate;
    }

    public function getGrade(): int
    {
        return $this->grade;
    }

    public function getComment(): string
    {
        return $this->comment;
    }


}
