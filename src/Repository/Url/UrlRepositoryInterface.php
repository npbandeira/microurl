<?php
// src/Repository/UrlRepositoryInterface.php
namespace MicroUrl\Repository\Url;

interface UrlRepositoryInterface
{
    public function findByOriginalUrl(string $originalUrl): ?array;
    public function findByShortCode(string $shortCode): ?array;
    public function create(array $data): array;
    public function incrementVisits(string $shortCode): bool;
    public function generateUniqueCode(): string;
}