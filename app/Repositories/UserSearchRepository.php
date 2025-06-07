<?php

namespace App\Repositories;

use App\Models\UserSearch;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

class UserSearchRepository extends BaseRepository
{
  public function __construct(UserSearch $model)
  {
    parent::__construct($model);
  }

  /**
   * Registra una nueva búsqueda para un usuario
   */
  public function logSearch(int $userId, int $cityId): UserSearch
  {
    // Elimina búsquedas duplicadas recientes
    $this->model->where('user_id', $userId)
      ->where('city_id', $cityId)
      ->delete();

    return $this->create([
      'user_id' => $userId,
      'city_id' => $cityId,
      'searched_at' => now()
    ]);
  }

  /**
   * Obtiene el historial de búsquedas de un usuario
   */
  public function getUserRecentSearches(int $userId, int $limit = 10): Collection
  {
    return $this->model->with('city')
      ->where('user_id', $userId)
      ->latest('searched_at')
      ->limit($limit)
      ->get();
  }

  /**
   * Limpia el historial de búsquedas de un usuario
   */
  public function clearUserHistory(int $userId): int
  {
    return $this->model->where('user_id', $userId)->delete();
  }
}
