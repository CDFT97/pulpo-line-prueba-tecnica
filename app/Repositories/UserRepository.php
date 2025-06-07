<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Str;

class UserRepository extends BaseRepository
{
  public function __construct(User $user)
  {
    parent::__construct($user);
  }

  public function findOrCreate(array $userData): User
  {
    $user = $this->model->where('email', $userData['email'])->first();

    if (!$user) {
      $user = $this->model->create([
        'email' => $userData['email'],
        'name' => $userData['name'],
        'password' => bcrypt($userData['password']),
      ]);
    }

    return $user;
  }

  public function getByEmail($email): ?User
  {
    return $this->model->where('email', $email)->first();
  }
}
