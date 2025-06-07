<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
  protected $model;

  public function __construct(Model $model)
  {
    $this->model = $model;
  }

  public function all() : Collection
  {
    return $this->model->get();
  }

  public function get(int $id) : Model
  {
    return $this->model->find($id);
  }

  public function save(Model $model): bool
  {
    return $model->save();
  }

  public function create(array $data): Model
  {
    return $this->model->create($data);
  }

  public function delete(Model $model): ?bool
  {
    return $model->delete();
  }

  public function make(array $data): Model
  {
    return $this->model->make($data);
  }

  public function getPaginated(int $perPage = 10)
  {
    return $this->model->orderBy('id', 'desc')  // Si no hay created_at, ordena por ID descendente
      ->paginate($perPage);
  }

  public function where(array $conditions, string $returnType = 'get'): Builder|Collection|Model|null
  {
    $query = $this->model->where($conditions);

    if ($returnType === 'get') {
      return $query->get();
    } elseif ($returnType === 'first') {
      return $query->first();
    }

    return $query;
  }

  public function whereIn(string $column, array $values): Collection
  {
    return $this->model->whereIn($column, $values)->get();
  }

  public function query()
  {
    return $this->model->query();
  }
}
