<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\UserVip;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class UserVipController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new UserVip(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('vipname');

            //$grid->column('viptype');
            $grid->column('recharge','充值累计');
            $grid->column('flow','流水累计');
/*
            $grid->column('vrbetfee','Vebet返水(%)');
            $grid->column('ldfee','雷火返水(%)');*/
            $grid->column('realperson');
            $grid->column('realperson_switch','真人开关')->display(function(){
                $id = $this->id; $token = csrf_token(); $field = 'realperson_switch'; $checked = $this->{$field} == 1 ? 'checked' : ''; $switchId = 'vip_'.$id.'_'.$field; 
                return <<<HTML
<div class="custom-control custom-switch">
  <input type="checkbox" class="custom-control-input" id="{$switchId}" {$checked} onchange="toggleVipSwitch({$id}, '{$field}', this.checked)">
  <label class="custom-control-label" for="{$switchId}"></label>
</div>
<script>
function toggleVipSwitch(id, field, state) {
  $.ajax({
    url: 'user-vips/' + id + '/toggle-switch',
    type: 'POST',
    data: { _token: '{$token}', field: field, state: state ? 1 : 0 },
    success: function (res) { res.status ? Dcat.success('状态更新成功') : (Dcat.error(res.message||'状态更新失败'), setTimeout(function(){location.reload();}, 800)); },
    error: function () { Dcat.error('网络错误'); setTimeout(function(){location.reload();}, 800); }
  });
}
</script>
HTML;
            });
            $grid->column('electron');
            $grid->column('electron_switch','电子开关')->display(function(){
                $id = $this->id; $token = csrf_token(); $field = 'electron_switch'; $checked = $this->{$field} == 1 ? 'checked' : ''; $switchId = 'vip_'.$id.'_'.$field; 
                return <<<HTML
<div class="custom-control custom-switch">
  <input type="checkbox" class="custom-control-input" id="{$switchId}" {$checked} onchange="toggleVipSwitch({$id}, '{$field}', this.checked)">
  <label class="custom-control-label" for="{$switchId}"></label>
</div>
HTML;
            });
            $grid->column('joker');
            $grid->column('joker_switch','棋牌开关')->display(function(){
                $id = $this->id; $token = csrf_token(); $field = 'joker_switch'; $checked = $this->{$field} == 1 ? 'checked' : ''; $switchId = 'vip_'.$id.'_'.$field; 
                return <<<HTML
<div class="custom-control custom-switch">
  <input type="checkbox" class="custom-control-input" id="{$switchId}" {$checked} onchange="toggleVipSwitch({$id}, '{$field}', this.checked)">
  <label class="custom-control-label" for="{$switchId}"></label>
</div>
HTML;
            });
            $grid->column('sport');
            $grid->column('sport_switch','体育开关')->display(function(){
                $id = $this->id; $token = csrf_token(); $field = 'sport_switch'; $checked = $this->{$field} == 1 ? 'checked' : ''; $switchId = 'vip_'.$id.'_'.$field; 
                return <<<HTML
<div class="custom-control custom-switch">
  <input type="checkbox" class="custom-control-input" id="{$switchId}" {$checked} onchange="toggleVipSwitch({$id}, '{$field}', this.checked)">
  <label class="custom-control-label" for="{$switchId}"></label>
</div>
HTML;
            });
            $grid->column('fish');
            $grid->column('fish_switch','捕鱼开关')->display(function(){
                $id = $this->id; $token = csrf_token(); $field = 'fish_switch'; $checked = $this->{$field} == 1 ? 'checked' : ''; $switchId = 'vip_'.$id.'_'.$field; 
                return <<<HTML
<div class="custom-control custom-switch">
  <input type="checkbox" class="custom-control-input" id="{$switchId}" {$checked} onchange="toggleVipSwitch({$id}, '{$field}', this.checked)">
  <label class="custom-control-label" for="{$switchId}"></label>
</div>
HTML;
            });
            $grid->column('lottery');
            $grid->column('lottery_switch','彩票开关')->display(function(){
                $id = $this->id; $token = csrf_token(); $field = 'lottery_switch'; $checked = $this->{$field} == 1 ? 'checked' : ''; $switchId = 'vip_'.$id.'_'.$field; 
                return <<<HTML
<div class="custom-control custom-switch">
  <input type="checkbox" class="custom-control-input" id="{$switchId}" {$checked} onchange="toggleVipSwitch({$id}, '{$field}', this.checked)">
  <label class="custom-control-label" for="{$switchId}"></label>
</div>
HTML;
            });
            $grid->column('e_sport');
            $grid->column('e_sport_switch','电竞开关')->display(function(){
                $id = $this->id; $token = csrf_token(); $field = 'e_sport_switch'; $checked = $this->{$field} == 1 ? 'checked' : ''; $switchId = 'vip_'.$id.'_'.$field; 
                return <<<HTML
<div class="custom-control custom-switch">
  <input type="checkbox" class="custom-control-input" id="{$switchId}" {$checked} onchange="toggleVipSwitch({$id}, '{$field}', this.checked)">
  <label class="custom-control-label" for="{$switchId}"></label>
</div>
HTML;
            });
            $grid->column('status')->using([1 => '正常',0 => '禁用']);
            $grid->column('vippic','对应等级图片');
            $grid->column('created_at');
            // $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

            });
        });
    }

    /**
     * 快捷切换某个反水开关字段
     */
    public function toggleSwitch($id)
    {
        try {
            $field = request('field');
            $state = (int) request('state', 0);
            $allow = [
                'realperson_switch','electron_switch','joker_switch','sport_switch','fish_switch','lottery_switch','e_sport_switch'
            ];
            if (!in_array($field, $allow, true)) {
                return response()->json(['status' => false, 'message' => '非法字段']);
            }
            $vip = \App\Models\UserVip::findOrFail($id);
            $vip->update([$field => $state]);
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => '更新失败：'.$e->getMessage()]);
        }
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        // return Show::make($id, new UserVip(), function (Show $show) {
        //     $show->field('id');
        //     $show->field('vipname');
        //     $show->field('viptype');
        //     $show->field('realperson');
        //     $show->field('electron');
        //     $show->field('chessandcard');
        //     $show->field('sports');
        //     $show->field('fish');
        //     $show->field('lottery');
        //     $show->field('lottery6');
        //     $show->field('status');
        //     $show->field('exp');
        //     $show->field('isdefault');
        //     $show->field('isdel');
        //     $show->field('created_at');
        //     $show->field('updated_at');
        // });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new UserVip(), function (Form $form) {
            $form->display('id');
            $form->text('vipname')->required();
            $form->hidden('viptype')->default(1);
            $form->decimal('recharge','充值累计');
            $form->decimal('flow','流水累计');
            $form->decimal('realperson');
            $form->switch('realperson_switch','真人开关')->default(1);
            $form->decimal('electron');
            $form->switch('electron_switch','电子开关')->default(1);
            $form->decimal('joker');
            $form->switch('joker_switch','棋牌开关')->default(1);
            $form->decimal('sport');
            $form->switch('sport_switch','体育开关')->default(1);
            $form->decimal('fish');
            $form->switch('fish_switch','捕鱼开关')->default(1);
            $form->decimal('lottery');
            $form->switch('lottery_switch','彩票开关')->default(1);
            $form->decimal('e_sport');
            $form->switch('e_sport_switch','电竞开关')->default(1);
            $form->radio('status')->options([1 => '可用',0 => '禁用'])->default(1);
            //$form->number('exp');
            $form->radio('is_default')->options([1 => '是',0 => '否'])->default(0);
            $form->text('vippic');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
