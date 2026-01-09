<?php

namespace App\Admin\Actions\Grid\Withdraw;

use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Withdraw;
use App\User;

class Refuse extends RowAction
{
    /**
     * @return string
     */
	protected $title = '拒绝';

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        $id = $this->getKey();
        $model = Withdraw::find($id);
        $model->state = 3;
        $model->save();
        $user = User::find($model->user_id);
        $user->balance += $model->amount;
        $user->save();

        return $this->response()
            ->success('审核成功')
            ->refresh();
    }

    /**
	 * @return string|array|void
	 */
	public function confirm()
	{
		// return ['Confirm?', 'contents'];
	}

    /**
     * @param Model|Authenticatable|HasPermissions|null $user
     *
     * @return bool
     */
    protected function authorize($user): bool
    {
        return true;
    }

    /**
     * @return array
     */
    protected function parameters()
    {
        return [];
    }
}
