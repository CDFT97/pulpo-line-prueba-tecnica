<?php

namespace App\Repositories;

use App\Models\City;

class CityRepository extends BaseRepository
{
  public function __construct(City $model)
  {
    parent::__construct($model);
  }

  /**
   * Busca o crea una ciudad basada en datos básicos
   */
  public function firstOrCreate(array $identifiers, array $additionalData = []): City
  {
    return $this->model->firstOrCreate($identifiers, $additionalData);
  }

  /**
   * Obtiene ciudades por nombre (búsqueda aproximada)
   */
  public function searchByName(string $name): Collection
  {
    return $this->model->where('name', 'like', "%{$name}%")->get();
  }

  /**
   * Obtiene ciudades populares (más buscadas)
   */
  public function getPopularCities(int $limit = 5): Collection
  {
    return $this->model->withCount('userSearches')
      ->orderBy('user_searches_count', 'desc')
      ->limit($limit)
      ->get();
  }
}
