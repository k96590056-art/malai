<?php

namespace App\Admin\Repositories;

use App\Models\AgentInterface as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class AgentInterface extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}

