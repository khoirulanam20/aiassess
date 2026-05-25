<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new User);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->paginate($perPage);
    }

    public function updateProfile(int $id, array $data): User
    {
        $user = $this->findById($id);
        $user->update($data);

        return $user->fresh();
    }

    public function updateAvatar(int $id, string $path): User
    {
        $user = $this->findById($id);
        $user->userDetail()->updateOrCreate(
            ['user_id' => $id],
            ['avatar_url' => $path]
        );

        return $user->fresh();
    }
}
