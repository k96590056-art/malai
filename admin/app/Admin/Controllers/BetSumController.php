<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\User;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class BetSumController extends AdminController
{
    protected $title = '下注汇总';
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new User(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('username');
            $plat = config('conf.plat_type');
            foreach ($plat as $k => $v) {
                $grid->column($v)->display(function () use ($v){
                    return $this->getBetSumByPlat($v);
                });
            }
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }
}
