<?php

namespace MicroUrl\Model;

use MicroUrl\Database;
use Exception;
use MicroUrl\Repository\Url\UrlRepositoryInterface;

class Url
{
    private UrlRepositoryInterface $repository;

    public function __construct(UrlRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function findByOriginalUrl(string $originalUrl): ?array
    {
        return $this->repository->findByOriginalUrl($originalUrl);
    }

    public function findByShortCode(string $shortCode): ?array
    {
        return $this->repository->findByShortCode($shortCode);
    }

    public function create(array $data): array
    {
        return $this->repository->create($data);
    }

    public function incrementVisits(string $shortCode): bool
    {
        return $this->repository->incrementVisits($shortCode);
    }
}