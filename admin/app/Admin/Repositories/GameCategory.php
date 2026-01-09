<?php

namespace App\Admin\Repositories;

use App\Models\GameCategory as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class GameCategory extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}

