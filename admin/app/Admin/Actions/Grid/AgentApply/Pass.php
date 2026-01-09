<?php

namespace App\Admin\Actions\Grid\AgentApply;

use App\Models\AgentApply;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Users;
use App\User;
use App\Models\AgentSettlement;

class Pass extends RowAction
{
    /**
     * @return string
     */
	protected $title = '通过';

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
        $item = AgentApply::find($id);
        $item->state = 2;
        $item->save();
        $user = User::find($item->user_id);
        $user->isagent=1;
        $settle = AgentSettlement::where('state',1)->first();
        if ($settle) {
            $user->settlement_id = $settle->id;
        }
        $user->save();
        return $this->response()
            ->success('审核成功')
            ->redirect('/agent-applys');
    }

    /**
	 * @return string|array|void
	 */
	public function confirm()
	{
		return ['确认通过？', ''];
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
