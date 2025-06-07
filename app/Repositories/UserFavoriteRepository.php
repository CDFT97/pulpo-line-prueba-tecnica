<?php

namespace App\Repositories;

use App\Models\UserFavorite;
use Illuminate\Database\Eloquent\Collection;

class UserFavoriteRepository extends BaseRepository
{
  public function __construct(UserFavorite $model)
  {
    parent::__construct($model);
  }

  /**
   * Alterna el estado de favorito para una ciudad de usuario
   */
  public function toggleFavorite(int $userId, int $cityId): array
  {
    $favorite = $this->where([
      'user_id' => $userId,
      'city_id' => $cityId
    ], 'first');

    if ($favorite) {
      $this->delete($favorite);
      return ['action' => 'removed', 'favorite' => null];
    }

    $newFavorite = $this->create([
      'user_id' => $userId,
      'city_id' => $cityId
    ]);

    return ['action' => 'added', 'favorite' => $newFavorite];
  }

  /**
   * Obtiene las ciudades favoritas de un usuario
   */
  public function getUserFavorites(int $userId): Collection
  {
    return $this->model->with('city')
      ->where('user_id', $userId)
      ->latest()
      ->get();
  }

  /**
   * Verifica si una ciudad es favorita del usuario
   */
  public function isFavorite(int $userId, int $cityId): bool
  {
    return $this->model->where([
      'user_id' => $userId,
      'city_id' => $cityId
    ])->exists();
  }
}
