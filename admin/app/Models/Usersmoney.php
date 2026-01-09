<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Services\TgService;
class Usersmoney extends Model
{

    protected $table = "usersmoney";
    protected $guarded = [];

    public static function upinfo($userid,$plat_name,$balance)
    {
        $usersmoney = self::where('user_id',$userid)->first();
        
        // 统一处理平台名称映射，确保 Yes 平台正确映射到 yes_money
        $plat_name = strtolower($plat_name);
        if ($plat_name === 'yes' || $plat_name === 'yesagent' || $plat_name === 'yes918') {
            $plat_name = 'yes';
        }
        
        $vote = $plat_name . '_money';
        
        if($usersmoney) {
            $usersmoney->$vote = $balance;
            $usersmoney->save();
        }else{
            $arr['user_id'] = $userid;
            $arr[$vote] = $balance;
            self::create($arr);
        }
    }

    public static function addinfo($userid,$plat_name,$balance)
    {
        $usersmoney = self::where('user_id',$userid)->first();
        
        // 统一处理平台名称映射，确保 Yes 平台正确映射到 yes_money
        $plat_name = strtolower($plat_name);
        if ($plat_name === 'yes' || $plat_name === 'yesagent' || $plat_name === 'yes918') {
            $plat_name = 'yes';
        }
        
        $vote = $plat_name . '_money';
        
        if($usersmoney) {
            // 确保字段存在，如果不存在则初始化为0
            if (!isset($usersmoney->$vote)) {
                $usersmoney->$vote = 0;
            }
            $usersmoney->$vote += $balance;
            $usersmoney->save();
        }else{
            $arr['user_id'] = $userid;
            $arr[$vote] = $balance;
            self::create($arr);
        }
    }

    public static function setmoneyinit($userid,$plat_name)
    {
        $usersmoney = self::where('user_id',$userid)->first();
        
        // 统一处理平台名称映射，确保 Yes 平台正确映射到 yes_money
        $plat_name = strtolower($plat_name);
        if ($plat_name === 'yes' || $plat_name === 'yesagent' || $plat_name === 'yes918') {
            $plat_name = 'yes';
        }
        
        $vote = $plat_name . '_money';
        
        if($usersmoney) {
        $usersmoney->$vote = 0;
        $usersmoney->save();
        }
    }

    public static function kouinfo($userid,$plat_name,$balance)
    {
        $usersmoney = self::where('user_id',$userid)->first();
        
        // 统一处理平台名称映射，确保 Yes 平台正确映射到 yes_money
        $plat_name = strtolower($plat_name);
        if ($plat_name === 'yes' || $plat_name === 'yesagent' || $plat_name === 'yes918') {
            $plat_name = 'yes';
        }
        
        $vote = $plat_name . '_money';
        
        if($usersmoney) {
            // 确保字段存在，如果不存在则初始化为0
            if (!isset($usersmoney->$vote)) {
                $usersmoney->$vote = 0;
            }
            if($usersmoney->$vote>=$balance) {
                $usersmoney->$vote -= $balance;
                $usersmoney->save();
            }
        }
    }

    public static function getUserBalance($userid)
    {
        
        $tg = New TgService;
        $gamemoneylist = $tg->gamesalllist();        
        $usersmoney = self::where('user_id',$userid)->first();
        $i=0;
        $retdata=[];
        foreach ($gamemoneylist as $val) {
            $vote = strtolower($val['platform_code']) . '_money';
            $retdata[$i]['name']= $val['platformname'];
            $retdata[$i]['platname']= strtolower($val['platform_code']);
            $retdata[$i]['balance']=  isset($usersmoney->$vote) ? round($usersmoney->$vote,2) :0;
            $i++;
        }
        return $retdata;
    }
    public static function getTotalAppUserBalance($userid)
    {
        $tg = New TgService;
        $gamemoneylist = $tg->gamesalllist();   
        $usersmoney = self::where('user_id',$userid)->first();
        $balance =0;
        foreach ($gamemoneylist as $val) {
            $vote = strtolower($val['platform_code']) . '_money';
            $balance +=  isset($usersmoney->$vote) ? round($usersmoney->$vote,2) :0;
        }
        return $balance;
    }
    public static function getAppUserBalance($userid,$platgamename)
    {

        $usersmoney = self::where('user_id',$userid)->first();
        $i=0;
        $retdata=[];
        foreach ($platgamename as $val) {
            $vote = strtolower($val['platform_code']) . '_money';
            $retdata[$i]['name']= $val['platformname'];
            $retdata[$i]['platname']= strtolower($val['platform_code']);
            $retdata[$i]['balance']=  isset($usersmoney->$vote) ? round($usersmoney->$vote,2) :0;
            $i++;
        }
        return $retdata;
    }

    /**
     * 根据代理接口代码更新余额
     * 接口代码格式：如 "YesAgent"、"PussyAgent"
     * 转换规则：移除"Agent"后缀 -> 转小写 -> 组合成 {name}_money 字段
     * 
     * @param int $userid 用户ID
     * @param string $interfaceCode 接口代码（如 "YesAgent"、"PussyAgent"）
     * @param float $amount 金额（正数为增加，负数为减少）
     * @return void
     */
    public static function updateBalanceByInterface($userid, $interfaceCode, $amount)
    {
        if (empty($interfaceCode)) {
            return;
        }

        // 移除"Agent"后缀，转小写，组合成字段名
        $fieldName = str_replace('Agent', '', $interfaceCode);
        $fieldName = strtolower($fieldName);
        $vote = $fieldName . '_money';

        $usersmoney = self::where('user_id', $userid)->first();
        
        if (!$usersmoney) {
            $usersmoney = self::create(['user_id' => $userid]);
        }

        // 确保字段存在，如果不存在则初始化为0
        if (!isset($usersmoney->$vote)) {
            $usersmoney->$vote = 0;
        }

        // 更新余额
        $usersmoney->$vote += $amount;
        $usersmoney->save();
    }

    /**
     * 根据代理接口代码获取余额
     * 
     * @param int $userid 用户ID
     * @param string $interfaceCode 接口代码（如 "YesAgent"、"PussyAgent"）
     * @return float 余额
     */
    public static function getBalanceByInterface($userid, $interfaceCode)
    {
        if (empty($interfaceCode)) {
            return 0;
        }

        // 移除"Agent"后缀，转小写，组合成字段名
        $fieldName = str_replace('Agent', '', $interfaceCode);
        $fieldName = strtolower($fieldName);
        $vote = $fieldName . '_money';

        $usersmoney = self::where('user_id', $userid)->first();
        
        if (!$usersmoney || !isset($usersmoney->$vote)) {
            return 0;
        }

        return floatval($usersmoney->$vote);
    }
}
