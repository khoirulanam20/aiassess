<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function search(string $query, int $perPage = 15): LengthAwarePaginator;

    public function updateProfile(int $id, array $data): User;

    public function updateAvatar(int $id, string $path): User;
}
