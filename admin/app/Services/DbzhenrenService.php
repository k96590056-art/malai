<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\SystemConfig;
use App\Models\User_Api;
use App\Models\User;
use App\Models\GameRecord;
use Illuminate\Http\Request;

/**
 * DbzhenrenService 真人游戏接口处理类
 * 参考文档：zhenren.md
 * 
 * 注意：此类包含两部分功能：
 * 1. 主动调用API（创建账号、进入游戏、查询数据等）
 * 2. 处理真人平台发起的回调请求（签名验证、回调响应格式化等）
 */
class DbzhenrenService
{
    // 厅ID和厅名称映射关系
    protected $Halls = [
        1 => '旗舰厅',
        2 => '区块链厅',
        3 => '亚太厅',
        4 => '欧洲厅',
        5 => '国际厅',
        6 => '透亮厅',
        7 => '美洲厅',
    ];

    // 游戏类型ID和游戏类型名称映射关系
    protected $gameTypes = [
        2001 => '经典百家乐',
        2002 => '极速百家乐',
        2003 => '竞咪百家乐',
        2004 => '包桌百家乐',
        2005 => '共咪百家乐',
        2006 => '龙虎',
        2007 => '轮盘',
        2008 => '骰宝',
        2009 => '牛牛',
        2010 => '炸金花',
        2011 => '三公',
        2012 => '(旧)21点',
        2013 => '多台（不支持gameTypeId 直接进入）',
        2014 => '高额贵宾百家乐',
        2015 => '斗牛',
        2016 => '保险百家乐',
        2017 => '区块链百家乐（已下架）',
        2019 => '德州扑克',
        2020 => '番摊',
        2021 => '21点',
        2022 => '色碟',
        2023 => '温州牌九',
        2025 => '安达巴哈',
        2026 => '印度炸金花',
        2027 => '劲舞百家乐（游戏玩法同百家乐）',
        2030 => '主播百家乐（游戏玩法同百家乐）',
        2028 => '滚球',
        2029 => '六合彩',
        2031 => '3D',
        2032 => '5D',
        2036 => '多利',
        2034 => '闪电百家乐',
        2038 => '电投百家乐',
    ];

    // 玩法编号和玩法名称映射关系
    protected $betPoints = [
        // 经典百家乐 / 极速百家乐 / 竞咪百家乐 / 包桌百家乐 / 共咪百家乐 / 高额贵宾百家乐 / 保险百家乐 / 现场VIP百家乐 / 主播百家乐 / 劲舞百家乐 / 电投百家乐
        3001 => '庄',
        3002 => '闲',
        3003 => '和',
        3004 => '庄对',
        3005 => '闲对',
        3006 => '大',
        3007 => '小',
        3009 => '完美对子',
        3010 => '庄龙宝',
        3011 => '闲龙宝',
        3012 => '幸运六', // 经典百家乐/包桌百家乐/共咪百家乐为"幸运六"，极速百家乐/竞咪百家乐/高额贵宾百家乐/保险百家乐/主播百家乐/劲舞百家乐/电投百家乐为"超级六"
        3013 => '庄免佣',
        3014 => '任意对子',
        // 骰宝
        3017 => '单',
        3018 => '双',
        3019 => '小',
        3020 => '大',
        3021 => '全围',
        3022 => '围骰1',
        3023 => '围骰2',
        3024 => '围骰3',
        3025 => '围骰4',
        3026 => '围骰5',
        3027 => '围骰6',
        3028 => '单点1',
        3029 => '单点2',
        3030 => '单点3',
        3031 => '单点4',
        3032 => '单点5',
        3033 => '单点6',
        3046 => '对子1',
        3047 => '对子2',
        3048 => '对子3',
        3049 => '对子4',
        3050 => '对子5',
        3051 => '对子6',
        3052 => '牌九式12',
        3053 => '牌九式13',
        3054 => '牌九式14',
        3055 => '牌九式15',
        3056 => '牌九式16',
        3057 => '牌九式23',
        3058 => '牌九式24',
        3059 => '牌九式25',
        3060 => '牌九式26',
        3061 => '牌九式34',
        3062 => '牌九式35',
        3063 => '牌九式36',
        3064 => '牌九式45',
        3065 => '牌九式46',
        3066 => '牌九式56',
        3067 => '和值4',
        3068 => '和值5',
        3069 => '和值6',
        3070 => '和值7',
        3071 => '和值8',
        3072 => '和值9',
        3073 => '和值10',
        3074 => '和值11',
        3075 => '和值12',
        3076 => '和值13',
        3077 => '和值14',
        3078 => '和值15',
        3079 => '和值16',
        3080 => '和值17',
        // 轮盘 - 直注
        3100 => '直注0',
        3101 => '直注1',
        3102 => '直注2',
        3103 => '直注3',
        3104 => '直注4',
        3105 => '直注5',
        3106 => '直注6',
        3107 => '直注7',
        3108 => '直注8',
        3109 => '直注9',
        3110 => '直注10',
        3111 => '直注11',
        3112 => '直注12',
        3113 => '直注13',
        3114 => '直注14',
        3115 => '直注15',
        3116 => '直注16',
        3117 => '直注17',
        3118 => '直注18',
        3119 => '直注19',
        3120 => '直注20',
        3121 => '直注21',
        3122 => '直注22',
        3123 => '直注23',
        3124 => '直注24',
        3125 => '直注25',
        3126 => '直注26',
        3127 => '直注27',
        3128 => '直注28',
        3129 => '直注29',
        3130 => '直注30',
        3131 => '直注31',
        3132 => '直注32',
        3133 => '直注33',
        3134 => '直注34',
        3135 => '直注35',
        3136 => '直注36',
        // 轮盘 - 分注
        3200 => '分注0,1',
        3201 => '分注0,2',
        3202 => '分注0,3',
        3203 => '分注1,2',
        3204 => '分注1,4',
        3205 => '分注2,3',
        3206 => '分注2,5',
        3207 => '分注3,6',
        3208 => '分注4,5',
        3209 => '分注4,7',
        3210 => '分注5,6',
        3211 => '分注5,8',
        3212 => '分注6,9',
        3213 => '分注7,8',
        3214 => '分注7,10',
        3215 => '分注8,9',
        3216 => '分注8,11',
        3217 => '分注9,12',
        3218 => '分注10,11',
        3219 => '分注10,13',
        3220 => '分注11,12',
        3221 => '分注11,14',
        3222 => '分注12,15',
        3223 => '分注13,14',
        3224 => '分注13,16',
        3225 => '分注14,15',
        3226 => '分注14,17',
        3227 => '分注15,18',
        3228 => '分注16,17',
        3229 => '分注16,19',
        3230 => '分注17,18',
        3231 => '分注17,20',
        3232 => '分注18,21',
        3233 => '分注19,20',
        3234 => '分注19,22',
        3235 => '分注20,21',
        3236 => '分注20,23',
        3237 => '分注21,24',
        3238 => '分注22,23',
        3239 => '分注22,25',
        3240 => '分注23,24',
        3241 => '分注23,26',
        3242 => '分注24,27',
        3243 => '分注25,26',
        3244 => '分注25,28',
        3245 => '分注26,27',
        3246 => '分注26,29',
        3247 => '分注27,30',
        3248 => '分注28,29',
        3249 => '分注28,31',
        3250 => '分注29,30',
        3251 => '分注29,32',
        3252 => '分注30,33',
        3253 => '分注31,32',
        3254 => '分注31,34',
        3255 => '分注32,33',
        3256 => '分注32,35',
        3257 => '分注33,36',
        3258 => '分注34,35',
        3259 => '分注35,36',
        // 轮盘 - 街注
        3301 => '街注1,2,3',
        3302 => '街注4,5,6',
        3303 => '街注7,8,9',
        3304 => '街注10,11,12',
        3305 => '街注13,14,15',
        3306 => '街注16,17,18',
        3307 => '街注19,20,21',
        3308 => '街注22,23,24',
        3309 => '街注25,26,27',
        3310 => '街注28,29,30',
        3311 => '街注31,32,33',
        3312 => '街注34,35,36',
        // 轮盘 - 三数
        3400 => '三数0,1,2',
        3401 => '三数0,2,3',
        3402 => '四个号码0,1,2,3',
        // 轮盘 - 角注
        3500 => '角注1,2,4,5',
        3501 => '角注2,3,5,6',
        3502 => '角注4,5,7,8',
        3503 => '角注5,6,8,9',
        3504 => '角注7,8,10,11',
        3505 => '角注8,9,11,12',
        3506 => '角注10,11,13,14',
        3507 => '角注11,12,14,15',
        3508 => '角注13,14,16,17',
        3509 => '角注14,15,17,18',
        3510 => '角注16,17,19,20',
        3511 => '角注17,18,20,21',
        3512 => '角注19,20,22,23',
        3513 => '角注20,21,23,24',
        3514 => '角注22,23,25,26',
        3515 => '角注23,24,26,27',
        3516 => '角注25,26,28,29',
        3517 => '角注26,27,29,30',
        3518 => '角注28,29,31,32',
        3519 => '角注29,30,32,33',
        3520 => '角注31,32,34,35',
        3521 => '角注32,33,35,36',
        // 轮盘 - 线注
        3600 => '线注1,2,3,4,5,6',
        3601 => '线注4,5,6,7,8,9',
        3602 => '线注7,8,9,10,11,12',
        3603 => '线注10,11,12,13,14,15',
        3604 => '线注13,14,15,16,17,18',
        3605 => '线注16,17,18,19,20,21',
        3606 => '线注19,20,21,22,23,24',
        3607 => '线注22,23,24,25,26,27',
        3609 => '线注25,26,27,28,29,30',
        3610 => '线注28,29,30,31,32,33',
        3611 => '线注31,32,33,34,35,36',
        // 轮盘 - 列/打/颜色
        3700 => '列一',
        3701 => '列二',
        3702 => '列三',
        3703 => '打一',
        3704 => '打二',
        3705 => '打三',
        3706 => '红',
        3707 => '黑',
        3708 => '单',
        3709 => '双',
        3710 => '小',
        3711 => '大',
        // 牛牛
        3800 => '闲1平倍',
        3801 => '闲1翻倍',
        3802 => '闲2平倍',
        3803 => '闲2翻倍',
        3804 => '闲3平倍',
        3805 => '闲3翻倍',
        3806 => '庄1平倍',
        3807 => '庄1翻倍',
        3808 => '庄2平倍',
        3809 => '庄2翻倍',
        3810 => '庄3平倍',
        3811 => '庄3翻倍',
        // 炸金花
        3901 => '龙',
        3902 => '凤',
        3903 => '豹子',
        3904 => '同花顺',
        3905 => '同花',
        3906 => '顺子',
        3907 => '对8以上',
        // 旧21点
        4001 => '押注',
        4002 => '21+3',
        4003 => '对子',
        4004 => '保险',
        4005 => '加倍',
        4006 => '分牌',
        // 三公
        4007 => '闲1赢',
        4008 => '闲2赢',
        4009 => '闲3赢',
        4010 => '闲1输',
        4011 => '闲2输',
        4012 => '闲3输',
        4013 => '闲1和',
        4014 => '闲2和',
        4015 => '闲3和',
        4016 => '闲1三公',
        4017 => '闲2三公',
        4018 => '闲3三公',
        4019 => '闲1对牌以上',
        4020 => '闲2对牌以上',
        4021 => '闲3对牌以上',
        4022 => '庄对牌以上',
        // 超和/超级玩法
        4100 => '超和(0)',
        4101 => '超和(1)',
        4102 => '超和(2)',
        4103 => '超和(3)',
        4104 => '超和(4)',
        4105 => '超和(5)',
        4106 => '超和(6)',
        4107 => '超和(7)',
        4108 => '超和(8)',
        4109 => '超和(9)',
        4110 => '超级对',
        4111 => '龙7',
        4112 => '熊猫8',
        4113 => '大老虎',
        4114 => '小老虎',
        4115 => '庄天牌',
        4116 => '闲天牌',
        4117 => '天牌',
        4118 => '龙',
        4119 => '虎',
        4120 => '龙虎和',
        4121 => '庄保险',
        4122 => '庄保险',
        4123 => '闲保险',
        4124 => '闲保险',
        // 龙虎
        4201 => '龙',
        4202 => '虎',
        4203 => '和',
        4204 => '老虎和',
        4205 => '老虎对',
        // 德州扑克
        4206 => '底注',
        4207 => '跟注',
        4208 => 'AA边注',
        // 番摊
        4209 => '单',
        4210 => '双',
        4211 => '1番',
        4212 => '2番',
        4213 => '3番',
        4214 => '4番',
        4215 => '1念2',
        4216 => '1念3',
        4217 => '1念4',
        4218 => '2念1',
        4219 => '2念3',
        4220 => '2念4',
        4221 => '3念1',
        4222 => '3念2',
        4223 => '3念4',
        4224 => '4念1',
        4225 => '4念2',
        4226 => '4念3',
        4227 => '12角',
        4228 => '23角',
        4229 => '34角',
        4230 => '41角',
        4231 => '23四通',
        4232 => '13四通',
        4233 => '12四通',
        4234 => '24三通',
        4235 => '14三通',
        4236 => '12三通',
        4237 => '34二通',
        4238 => '14二通',
        4239 => '13二通',
        4240 => '23一通',
        4241 => '24一通',
        4242 => '34一通',
        4243 => '三门432',
        4244 => '三门143',
        4245 => '三门214',
        4246 => '三门321',
        // 21点
        4247 => '闲1_右_底注',
        4248 => '闲2_右_底注',
        4249 => '闲3_右_底注',
        4250 => '闲5_右_底注',
        4251 => '闲6_右_底注',
        4252 => '闲7_右_底注',
        4253 => '闲8_右_底注',
        4254 => '闲1_左_底注',
        4255 => '闲2_左_底注',
        4256 => '闲3_左_底注',
        4257 => '闲5_左_底注',
        4258 => '闲6_左_底注',
        4259 => '闲7_左_底注',
        4260 => '闲8_左_底注',
        4301 => '闲1_右_加倍',
        4302 => '闲2_右_加倍',
        4303 => '闲3_右_加倍',
        4304 => '闲5_右_加倍',
        4305 => '闲6_右_加倍',
        4306 => '闲7_右_加倍',
        4307 => '闲8_右_加倍',
        4308 => '闲1_左_加倍',
        4309 => '闲2_左_加倍',
        4310 => '闲3_左_加倍',
        4311 => '闲5_左_加倍',
        4312 => '闲6_左_加倍',
        4313 => '闲7_左_加倍',
        4314 => '闲8_左_加倍',
        4401 => '闲1_21+3',
        4402 => '闲2_21+3',
        4403 => '闲3_21+3',
        4404 => '闲5_21+3',
        4405 => '闲6_21+3',
        4406 => '闲7_21+3',
        4407 => '闲8_21+3',
        4501 => '闲1_完美对子',
        4502 => '闲2_完美对子',
        4503 => '闲3_完美对子',
        4504 => '闲5_完美对子',
        4505 => '闲6_完美对子',
        4506 => '闲7_完美对子',
        4507 => '闲8_完美对子',
        4601 => '闲1_保险',
        4602 => '闲2_保险',
        4603 => '闲3_保险',
        4604 => '闲5_保险',
        4605 => '闲6_保险',
        4606 => '闲7_保险',
        4607 => '闲8_保险',
        4701 => '闲1_旁注',
        4702 => '闲2_旁注',
        4703 => '闲3_旁注',
        4704 => '闲5_旁注',
        4705 => '闲6_旁注',
        4706 => '闲7_旁注',
        4707 => '闲8_旁注',
        // 温州牌九
        4708 => '顺门赢',
        4709 => '顺门输',
        4710 => '出门赢',
        4711 => '出门输',
        4712 => '到门赢',
        4713 => '到门输',
        // 色碟
        4714 => '0',
        4715 => '1',
        4716 => '3',
        4717 => '4',
        4718 => '单',
        4719 => '双',
        4720 => '大',
        4721 => '小',
        4722 => '和',
        4723 => '庄对',
        4724 => '闲对',
        // 安达巴哈
        4725 => '安达',
        4726 => '巴哈',
        // 印度炸金花
        4727 => 'A',
        4728 => 'B',
        4729 => '和',
        4730 => 'A对+',
        4731 => 'B对+',
        4732 => '红利六',
        // 龙虎扩展
        5501 => '龙单',
        5502 => '龙双',
        5503 => '龙红',
        5504 => '龙黑',
        5505 => '虎单',
        5506 => '虎双',
        5507 => '虎红',
        5508 => '虎黑',
        // 赛车
        5601 => '冠军大',
        5602 => '冠军小',
        5603 => '冠军单',
        5604 => '冠军双',
        5605 => '亚军大',
        5606 => '亚军小',
        5607 => '亚军单',
        5608 => '亚军双',
        5609 => '季军大',
        5610 => '季军小',
        5611 => '季军单',
        5612 => '季军双',
        5613 => '冠军号码1',
        5614 => '冠军号码2',
        5615 => '冠军号码3',
        5616 => '冠军号码4',
        5617 => '冠军号码5',
        5618 => '冠军号码6',
        5619 => '冠军号码7',
        5620 => '冠军号码8',
        5621 => '冠军号码9',
        5622 => '冠军号码10',
        5623 => '亚军号码1',
        5624 => '亚军号码2',
        5625 => '亚军号码3',
        5626 => '亚军号码4',
        5627 => '亚军号码5',
        5628 => '亚军号码6',
        5629 => '亚军号码7',
        5630 => '亚军号码8',
        5631 => '亚军号码9',
        5632 => '亚军号码10',
        5633 => '季军号码1',
        5634 => '季军号码2',
        5635 => '季军号码3',
        5636 => '季军号码4',
        5637 => '季军号码5',
        5638 => '季军号码6',
        5639 => '季军号码7',
        5640 => '季军号码8',
        5641 => '季军号码9',
        5642 => '季军号码10',
        5643 => '冠亚和值3',
        5644 => '冠亚和值4',
        5645 => '冠亚和值5',
        5646 => '冠亚和值6',
        5647 => '冠亚和值7',
        5648 => '冠亚和值8',
        5649 => '冠亚和值9',
        5650 => '冠亚和值10',
        5651 => '冠亚和值11',
        5652 => '冠亚和值12',
        5653 => '冠亚和值13',
        5654 => '冠亚和值14',
        5655 => '冠亚和值15',
        5656 => '冠亚和值16',
        5657 => '冠亚和值17',
        5658 => '冠亚和值18',
        5659 => '冠亚和值19',
        // 多利
        5701 => '闲1赢',
        5702 => '闲2赢',
        5703 => '闲3赢',
        5704 => '闲1对',
        5705 => '闲2对',
        5706 => '闲3对',
        // 走地德州
        5801 => '第1轮高牌',
        5802 => '第1轮一对',
        5803 => '第1轮两对',
        5804 => '第1轮三条',
        5805 => '第1轮顺子',
        5806 => '第1轮同花',
        5807 => '第1轮葫芦',
        5808 => '第1轮四条',
        5809 => '第1轮同花顺',
        5810 => '第1轮皇家同花顺',
        5811 => '第1轮第1手牌',
        5812 => '第1轮第2手牌',
        5813 => '第1轮第3手牌',
        5814 => '第1轮第5手牌',
        5815 => '第1轮第6手牌',
        5816 => '第1轮第7手牌',
        5817 => '幸运七',
        5818 => '超级幸运七',
        5819 => '小龙',
        5820 => '大龙',
        5821 => '龙虎斗',
        // 闪电百家乐
        5751 => '庄',
        5752 => '闲',
        5753 => '和',
        5754 => '庄对',
        5755 => '闲对',
        // 3D - 百位
        5200 => '百位号码0',
        5201 => '百位号码1',
        5202 => '百位号码2',
        5203 => '百位号码3',
        5204 => '百位号码4',
        5205 => '百位号码5',
        5206 => '百位号码6',
        5207 => '百位号码7',
        5208 => '百位号码8',
        5209 => '百位号码9',
        // 3D - 十位
        5210 => '十位号码0',
        5211 => '十位号码1',
        5212 => '十位号码2',
        5213 => '十位号码3',
        5214 => '十位号码4',
        5215 => '十位号码5',
        5216 => '十位号码6',
        5217 => '十位号码7',
        5218 => '十位号码8',
        5219 => '十位号码9',
        // 3D - 个位
        5220 => '个位号码0',
        5221 => '个位号码1',
        5222 => '个位号码2',
        5223 => '个位号码3',
        5224 => '个位号码4',
        5225 => '个位号码5',
        5226 => '个位号码6',
        5227 => '个位号码7',
        5228 => '个位号码8',
        5229 => '个位号码9',
        // 3D - 百位大小单双
        5230 => '百位大',
        5231 => '百位小',
        5232 => '百位单',
        5233 => '百位双',
        // 3D - 十位大小单双
        5234 => '十位大',
        5235 => '十位小',
        5236 => '十位单',
        5237 => '十位双',
        // 3D - 个位大小单双
        5238 => '个位大',
        5239 => '个位小',
        5240 => '个位单',
        5241 => '个位双',
        // 5D - 万位
        5300 => '万位号码0',
        5301 => '万位号码1',
        5302 => '万位号码2',
        5303 => '万位号码3',
        5304 => '万位号码4',
        5305 => '万位号码5',
        5306 => '万位号码6',
        5307 => '万位号码7',
        5308 => '万位号码8',
        5309 => '万位号码9',
        // 5D - 千位
        5310 => '千位号码0',
        5311 => '千位号码1',
        5312 => '千位号码2',
        5313 => '千位号码3',
        5314 => '千位号码4',
        5315 => '千位号码5',
        5316 => '千位号码6',
        5317 => '千位号码7',
        5318 => '千位号码8',
        5319 => '千位号码9',
        // 5D - 百位
        5320 => '百位号码0',
        5321 => '百位号码1',
        5322 => '百位号码2',
        5323 => '百位号码3',
        5324 => '百位号码4',
        5325 => '百位号码5',
        5326 => '百位号码6',
        5327 => '百位号码7',
        5328 => '百位号码8',
        5329 => '百位号码9',
        // 5D - 十位
        5330 => '十位号码0',
        5331 => '十位号码1',
        5332 => '十位号码2',
        5333 => '十位号码3',
        5334 => '十位号码4',
        5335 => '十位号码5',
        5336 => '十位号码6',
        5337 => '十位号码7',
        5338 => '十位号码8',
        5339 => '十位号码9',
        // 5D - 个位
        5340 => '个位号码0',
        5341 => '个位号码1',
        5342 => '个位号码2',
        5343 => '个位号码3',
        5344 => '个位号码4',
        5345 => '个位号码5',
        5346 => '个位号码6',
        5347 => '个位号码7',
        5348 => '个位号码8',
        5349 => '个位号码9',
        // 5D - 万位大小单双
        5350 => '万位大',
        5351 => '万位小',
        5352 => '万位单',
        5353 => '万位双',
        // 5D - 千位大小单双
        5354 => '千位大',
        5355 => '千位小',
        5356 => '千位单',
        5357 => '千位双',
        // 5D - 百位大小单双
        5358 => '百位大',
        5359 => '百位小',
        5360 => '百位单',
        5361 => '百位双',
        // 5D - 十位大小单双
        5362 => '十位大',
        5363 => '十位小',
        5364 => '十位单',
        5365 => '十位双',
        // 5D - 个位大小单双
        5366 => '个位大',
        5367 => '个位小',
        5368 => '个位单',
        5369 => '个位双',
        // 大转盘
        5401 => '号码1',
        5402 => '号码2',
        5403 => '号码5',
        5404 => '号码10',
        5405 => '号码20',
        5406 => '号码40',
        // 六合彩 - 号码
        5001 => '号码:1',
        5002 => '号码:2',
        5003 => '号码:3',
        5004 => '号码:4',
        5005 => '号码:5',
        5006 => '号码:6',
        5007 => '号码:7',
        5008 => '号码:8',
        5009 => '号码:9',
        5010 => '号码:10',
        5011 => '号码:11',
        5012 => '号码:12',
        5013 => '号码:13',
        5014 => '号码:14',
        5015 => '号码:15',
        5016 => '号码:16',
        5017 => '号码:17',
        5018 => '号码:18',
        5019 => '号码:19',
        5020 => '号码:20',
        5021 => '号码:21',
        5022 => '号码:22',
        5023 => '号码:23',
        5024 => '号码:24',
        5025 => '号码:25',
        5026 => '号码:26',
        5027 => '号码:27',
        5028 => '号码:28',
        5029 => '号码:29',
        5030 => '号码:30',
        5031 => '号码:31',
        5032 => '号码:32',
        5033 => '号码:33',
        5034 => '号码:34',
        5035 => '号码:35',
        5036 => '号码:36',
        5037 => '号码:37',
        5038 => '号码:38',
        5039 => '号码:39',
        5040 => '号码:40',
        5041 => '号码:41',
        5042 => '号码:42',
        5043 => '号码:43',
        5044 => '号码:44',
        5045 => '号码:45',
        5046 => '号码:46',
        5047 => '号码:47',
        5048 => '号码:48',
        5049 => '号码:49',
        // 六合彩 - 生肖
        5050 => '生肖:鼠',
        5051 => '生肖:牛',
        5052 => '生肖:虎',
        5053 => '生肖:兔',
        5054 => '生肖:龙',
        5055 => '生肖:蛇',
        5056 => '生肖:马',
        5057 => '生肖:羊',
        5058 => '生肖:猴',
        5059 => '生肖:鸡',
        5060 => '生肖:狗',
        5061 => '生肖:猪',
        // 六合彩 - 总和
        5062 => '总和:大',
        5063 => '总和:小',
        5064 => '总和:单',
        5065 => '总和:双',
        // 六合彩 - 特码
        5101 => '特码:1',
        5102 => '特码:2',
        5103 => '特码:3',
        5104 => '特码:4',
        5105 => '特码:5',
        5106 => '特码:6',
        5107 => '特码:7',
        5108 => '特码:8',
        5109 => '特码:9',
        5110 => '特码:10',
        5111 => '特码:11',
        5112 => '特码:12',
        5113 => '特码:13',
        5114 => '特码:14',
        5115 => '特码:15',
        5116 => '特码:16',
        5117 => '特码:17',
        5118 => '特码:18',
        5119 => '特码:19',
        5120 => '特码:20',
        5121 => '特码:21',
        5122 => '特码:22',
        5123 => '特码:23',
        5124 => '特码:24',
        5125 => '特码:25',
        5126 => '特码:26',
        5127 => '特码:27',
        5128 => '特码:28',
        5129 => '特码:29',
        5130 => '特码:30',
        5131 => '特码:31',
        5132 => '特码:32',
        5133 => '特码:33',
        5134 => '特码:34',
        5135 => '特码:35',
        5136 => '特码:36',
        5137 => '特码:37',
        5138 => '特码:38',
        5139 => '特码:39',
        5140 => '特码:40',
        5141 => '特码:41',
        5142 => '特码:42',
        5143 => '特码:43',
        5144 => '特码:44',
        5145 => '特码:45',
        5146 => '特码:46',
        5147 => '特码:47',
        5148 => '特码:48',
        5149 => '特码:49',
        // 六合彩 - 特肖
        5150 => '特肖:鼠',
        5151 => '特肖:牛',
        5152 => '特肖:虎',
        5153 => '特肖:兔',
        5154 => '特肖:龙',
        5155 => '特肖:蛇',
        5156 => '特肖:马',
        5157 => '特肖:羊',
        5158 => '特肖:猴',
        5159 => '特肖:鸡',
        5160 => '特肖:狗',
        5161 => '特肖:猪',
        // 六合彩 - 特码大小单双
        5162 => '特码:大',
        5163 => '特码:小',
        5164 => '特码:单',
        5165 => '特码:双',
    ];

    protected $db_code;
    protected $merchant_code;
    protected $secret_key; // MD5签名密钥
    protected $aes_key; // AES加密密钥
    protected $api_url;
    protected $api_data_url;
    protected $md5_key;

    public function __construct()
    {
        // 从系统配置获取接口相关配置
        $this->db_code = "DBZR";
        $this->api_url = SystemConfig::getValue('dbzhenren_api_url') ?? env('DBZHENREN_API_URL', '');
        $this->api_data_url = SystemConfig::getValue('dbzhenren_api_data_url') ?? env('DBZHENREN_API_DATA_URL', '');
        $this->merchant_code = SystemConfig::getValue('dbzhenren_merchant_code') ?? env('DBZHENREN_MERCHANT_CODE', '');
        $this->secret_key = SystemConfig::getValue('dbzhenren_secret_key') ?? env('DBZHENREN_SECRET_KEY', '');
        $this->aes_key = SystemConfig::getValue('dbzhenren_aes_key') ?? env('DBZHENREN_AES_KEY', '');
        $this->md5_key = "pb4lKiYPgD3LhFQH";
    }

    /**
     * 验证回调请求签名
     * 签名算法：MD5("业务原文JSON+MD5盐值")
     * 
     * @param string $paramsJson 业务参数JSON字符串（params字段的值）
     * @param string $signature 签名值（需要转成大写后比较）
     * @return bool
     */
    public function verifySign($paramsJson, $signature)
    {
        if (empty($this->secret_key)) {
            Log::error('Dbzhenren 密钥未配置');
            return false;
        }

        // 生成签名：MD5(业务原文JSON + MD5盐值)
        $signString = $paramsJson . $this->secret_key;
        $calculatedSignature = strtoupper(md5($signString));

        // 比较签名（都转成大写）
        $signature = strtoupper($signature);

        $isValid = ($calculatedSignature === $signature);

        if (!$isValid) {
            Log::warning('Dbzhenren 签名验证失败', [
                'calculated' => $calculatedSignature,
                'received' => $signature,
                'params_length' => strlen($paramsJson)
            ]);
        }

        return $isValid;
    }

    /**
     * 生成回调响应签名
     * 签名算法：MD5("业务原文JSON+MD5盐值")
     * 
     * @param string $dataJson 响应数据JSON字符串
     * @return string MD5签名（大写）
     */
    public function generateSign($dataJson)
    {
        if (empty($this->secret_key)) {
            Log::error('Dbzhenren 密钥未配置');
            return '';
        }

        // 生成签名：MD5(业务原文JSON + MD5盐值)
        $signString = $dataJson . $this->secret_key;
        return strtoupper(md5($signString));
    }

    /**
     * 生成使用 md5_key 的签名
     * 签名算法：MD5("业务原文JSON + $this->md5_key")
     * 
     * @param string $dataJson 响应数据JSON字符串
     * @return string MD5签名（大写）
     */
    private function generateMd5KeySign($dataJson)
    {
        if (empty($this->md5_key)) {
            Log::error('Dbzhenren md5_key 未配置');
            return '';
        }

        // 生成签名：MD5(业务原文JSON + md5_key)
        $signString = $dataJson . $this->md5_key;
        $signature = md5($signString);
        
        Log::info('Dbzhenren 生成md5_key签名', [
            'data_json' => $dataJson,
            'data_json_length' => strlen($dataJson),
            'sign_string' => $signString,
            'signature' => strtoupper($signature)
        ]);
        
        return strtoupper($signature);
    }

    /**
     * 格式化成功响应
     * 
     * @param array $data 响应数据
     * @return array
     */
    public function formatSuccess($data)
    {
        // 将data转为JSON字符串（不转义Unicode和斜杠）
        $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        // 对data参数进行签名
        $signature = $this->generateSign($dataJson);

        return [
            'code' => 200,
            'message' => 'Success',
            'data' => $dataJson,
            'signature' => $signature
        ];
    }

    /**
     * 格式化失败响应
     * 
     * @param int|string $code 错误代码
     * @param string $message 错误消息
     * @return array
     */
    public function formatError($code, $message)
    {
        return [
            'code' => $code,
            'message' => $message
        ];
    }

    /**
     * 验证 params 字段的 signature 签名
     * 签名算法：MD5("params字段的值 + $this->md5_key")
     * 
     * @param string $params params字段的值（可能是JSON字符串或其他格式）
     * @param string $signature 客户端传入的签名
     * @return bool
     */
    private function verifyParamsSignature($params, $signature)
    {
        if (empty($signature)) {
            Log::warning('Dbzhenren signature 为空');
            return false;
        }

        if (empty($this->md5_key)) {
            Log::error('Dbzhenren md5_key 未配置');
            return false;
        }

        if (empty($params)) {
            Log::warning('Dbzhenren params 为空');
            return false;
        }

        // 计算签名：MD5(params字段的值 + md5_key)
        $signString = $params . $this->md5_key;
        $calculatedSignature = md5($signString);

        // 比较签名（不区分大小写）
        $isValid = (strtolower($calculatedSignature) === strtolower($signature));

        if (!$isValid) {
            Log::warning('Dbzhenren params signature 签名验证失败', [
                'calculated_signature' => $calculatedSignature,
                'received_signature' => $signature,
                'params' => $params,
                'params_length' => strlen($params),
                'sign_string' => $signString
            ]);
        } else {
            Log::info('Dbzhenren params signature 签名验证成功', [
                'params' => $params,
                'params_length' => strlen($params)
            ]);
        }

        return $isValid;
    }

    /**
     * 处理 getBalance 单个会员余额查询回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @return string JSON 格式字符串
     */
    public function getBalance()
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren getBalance 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren getBalance 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren getBalance params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        $loginName = $paramsData['loginName'] ?? $request->input('loginName', '');
        $currency = $paramsData['currency'] ?? $request->input('currency', 'CNY');

        if (empty($loginName)) {
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90000,
                'message' => '参数错误：loginName不能为空'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            // 从loginName中移除merchant_code前缀（只移除开头的）
            // 先将merchant_code转为小写，然后进行判断和移除操作
            $api_user = $loginName;
            if (!empty($this->merchant_code)) {
                $merchantCodeLower = strtolower($this->merchant_code);
                $loginNameLower = strtolower($loginName);
                if (strpos($loginNameLower, $merchantCodeLower) === 0) {
                    $api_user = substr($loginName, strlen($this->merchant_code));
                }
            }
            
            // 查找用户API记录
            $userApi = User_Api::where('api_user', $api_user)->where('api_code', $this->db_code)->first();
            
            if (!$userApi) {
                Log::warning('Dbzhenren getBalance 用户不存在', [
                    'loginName' => $loginName,
                    'api_user' => $api_user,
                    'api_code' => $this->db_code
                ]);
                // 直接返回 JSON 格式的错误响应
                return json_encode([
                    'code' => 1000,
                    'message' => '会员不存在'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            
            // 从数据库中获取余额
            $balance = $userApi->api_money ?? 0;

            // 余额支持4个精度（数值类型，不是字符串）
            $balance = round($balance, 4);

            // 构建data参数（注意：balance是数值类型，不是字符串）
            $dataArray = [
                'loginName' => $loginName,
                'balance' => $balance  // 数值类型，支持4个精度
            ];

            // 将data转为JSON字符串
            $dataJson = json_encode($dataArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren getBalance 处理成功', [
                'loginName' => $loginName,
                'balance' => $balance,
                'currency' => $currency,
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren getBalance 处理异常', [
                'loginName' => $loginName,
                'currency' => $currency,
                'error' => $e->getMessage()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 1000,
                'message' => '会员不存在'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 处理 getBatchBalance 批量会员余额查询回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @param array $requestData 请求数据（可选，如果不传则从 request() 获取）
     * @param callable $getBalanceCallback 获取余额的回调函数 function($loginName, $currency) { return $balance; }
     * @return string JSON 格式字符串
     */
    public function getBatchBalance($requestData = null, $getBalanceCallback = null)
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren getBatchBalance 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren getBatchBalance 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren getBatchBalance params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        $loginNames = $paramsData['loginNames'] ?? [];
        $currency = $paramsData['currency'] ?? 'CNY';

        if (empty($loginNames) || !is_array($loginNames)) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误：loginNames不能为空'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 最多传递7个会员账号
        if (count($loginNames) > 7) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误：最多传递7个会员账号'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
        $result = [];
        foreach ($loginNames as $loginName) {
            try {
                    $balance = $getBalanceCallback ? call_user_func($getBalanceCallback, $loginName, $currency) : 0;
                // 余额支持4个精度，状态不正确或账号不存在时返回0
                $balance = round($balance, 4);
            } catch (\Exception $e) {
                // 账号不存在或状态不正确时返回0
                $balance = 0;
            }

            $result[] = [
                'loginName' => $loginName,
                'balance' => $balance
            ];
        }

            // 将data转为JSON字符串
            $dataJson = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren getBatchBalance 处理成功', [
                'loginNames_count' => count($loginNames),
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren getBatchBalance 处理异常', [
                'error' => $e->getMessage()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90000,
                'message' => '处理失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 处理 betConfirm 下注确认回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @param array $requestData 请求数据（可选，如果不传则从 request() 获取）
     * @param callable $betConfirmCallback 下注确认回调函数
     *   function($transferNo, $loginName, $betTotalAmount, $betInfo, $gameTypeId, $roundNo, $betTime, $currency) {
     *     return ['success' => true, 'balance' => $balance, 'realBetAmount' => $realBetAmount, 'realBetInfo' => $realBetInfo];
     *   }
     * @return string JSON 格式字符串
     */
    public function betConfirm($requestData = null, $betConfirmCallback = null)
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren betConfirm 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren betConfirm 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren betConfirm params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        $transferNo = $paramsData['transferNo'] ?? '';
        $loginName = $paramsData['loginName'] ?? '';
        $betTotalAmount = $paramsData['betTotalAmount'] ?? 0;
        $betInfo = $paramsData['betInfo'] ?? [];
        $gameTypeId = $paramsData['gameTypeId'] ?? 0;
        $roundNo = $paramsData['roundNo'] ?? '';
        $betTime = $paramsData['betTime'] ?? 0;
        $currency = $paramsData['currency'] ?? 'CNY';

        if (empty($transferNo) || empty($loginName) || empty($betInfo)) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            // 从loginName中移除merchant_code前缀（只移除开头的）
            $api_user = $loginName;
            if (!empty($this->merchant_code)) {
                $merchantCodeLower = strtolower($this->merchant_code);
                $loginNameLower = strtolower($loginName);
                if (strpos($loginNameLower, $merchantCodeLower) === 0) {
                    $api_user = substr($loginName, strlen($this->merchant_code));
                }
            }

            // 查找用户API记录
            $userApi = User_Api::where('api_user', $api_user)
                ->where('api_code', $this->db_code)
                ->lockForUpdate()
                ->first();

            if (!$userApi) {
                Log::warning('Dbzhenren betConfirm 用户不存在', [
                    'loginName' => $loginName,
                    'api_user' => $api_user,
                    'api_code' => $this->db_code
                ]);
                return json_encode([
                    'code' => 1002,
                    'message' => '余额不足'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            // 获取用户信息
            $user = User::find($userApi->user_id);
            if (!$user) {
                Log::error('Dbzhenren betConfirm 用户记录不存在', [
                    'user_id' => $userApi->user_id
                ]);
                return json_encode([
                    'code' => 1002,
                    'message' => '余额不足'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            // 检查余额是否足够
            $betTotalAmountFloat = floatval($betTotalAmount);
            $currentBalance = floatval($userApi->api_money);

            if ($currentBalance < $betTotalAmountFloat) {
                Log::warning('Dbzhenren betConfirm 余额不足', [
                    'loginName' => $loginName,
                    'api_user' => $api_user,
                    'current_balance' => $currentBalance,
                    'bet_total_amount' => $betTotalAmountFloat
                ]);
                return json_encode([
                    'code' => 1002,
                    'message' => '余额不足'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            // 减少余额
            $newBalance = $currentBalance - $betTotalAmountFloat;
            $userApi->api_money = $newBalance;
            $userApi->save();

            // 将betTime从毫秒时间戳转换为datetime
            $betTimeSeconds = intval($betTime / 1000);
            $betTimeDatetime = date('Y-m-d H:i:s', $betTimeSeconds);

            // 构建realBetInfo（用于返回）
            $realBetInfo = [];
            
            // 写入game_records表（每个betInfo项一条记录）
            foreach ($betInfo as $betItem) {
                $betId = $betItem['betId'] ?? '';
                $betAmount = floatval($betItem['betAmount'] ?? 0);
                $betPointId = $betItem['betPointId'] ?? 0;

                // 构建realBetInfo
                $realBetInfo[] = [
                    'betId' => $betId,
                    'betAmount' => $betAmount,
                    'betPointId' => $betPointId
                ];

                // 写入game_records表
                GameRecord::create([
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'bet_id' => (string)$betId,
                    'transfer_no' => $transferNo,
                    'round_no' => $roundNo,
                    'bet_point_id' => $betPointId,
                    'bet_point_name' => isset($this->betPoints[$betPointId]) ? $this->betPoints[$betPointId] : '',
                    'game_type_id' => $gameTypeId,
                    'game_type_name' => isset($this->gameTypes[$gameTypeId]) ? $this->gameTypes[$gameTypeId] : '',
                    'game_code' => (string)$gameTypeId, // 添加game_code字段
                    'platform_type' => $this->db_code,
                    'game_type' => 'realbet', // 根据实际情况调整
                    'bet_time' => $betTimeDatetime,
                    'bet_amount' => $betAmount,
                    'valid_amount' => $betAmount,
                    'win_loss' => 0,
                    'status' => 2, // 0=未结算
                    'is_back' => 0,
                    'before_amount' => $currentBalance,
                ]);
            }

            // 构建返回数据
            $data = [
                'loginName' => $loginName,
                'balance' => round($newBalance, 4),
                'realBetAmount' => round($betTotalAmountFloat, 4),
                'realBetInfo' => $realBetInfo
            ];

            // 将data转为JSON字符串
            $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应（根据用户要求，message应该是"成功"）
            $response = [
                'code' => 200,
                'message' => '成功',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren betConfirm 处理成功', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'api_user' => $api_user,
                'bet_total_amount' => $betTotalAmountFloat,
                'before_balance' => $currentBalance,
                'after_balance' => $newBalance,
                'bet_records_count' => count($betInfo),
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren betConfirm 处理异常', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return json_encode([
                'code' => 1002,
                'message' => '余额不足'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 处理 betCancel 取消下注回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @param array $requestData 请求数据（可选，如果不传则从 request() 获取）
     * @param callable $betCancelCallback 取消下注回调函数
     *   function($transferNo, $loginName, $gameTypeId, $roundNo, $cancelTime, $currency, $betPayoutMap, $hasTransferOut) {
     *     return ['success' => true, 'balance' => $balance, 'rollbackAmount' => $rollbackAmount];
     *   }
     * @return string JSON 格式字符串
     */
    public function betCancel($requestData = null, $betCancelCallback = null)
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren betCancel 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren betCancel 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren betCancel params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        $transferNo = $paramsData['transferNo'] ?? '';
        $loginName = $paramsData['loginName'] ?? '';
        $gameTypeId = $paramsData['gameTypeId'] ?? 0;
        $roundNo = $paramsData['roundNo'] ?? '';
        $cancelTime = $paramsData['cancelTime'] ?? 0;
        $currency = $paramsData['currency'] ?? 'CNY';
        $betPayoutMap = $paramsData['betPayoutMap'] ?? [];
        $hasTransferOut = $paramsData['hasTransferOut'] ?? 0;

        if (empty($transferNo) || empty($loginName)) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            // 调用回调函数处理取消下注
            $result = $betCancelCallback ? call_user_func($betCancelCallback, $transferNo, $loginName, $gameTypeId, $roundNo, $cancelTime, $currency, $betPayoutMap, $hasTransferOut) : ['success' => false, 'message' => '回调函数未设置'];

            if (!isset($result['success']) || !$result['success']) {
                return json_encode([
                    'code' => 90000,
                    'message' => $result['message'] ?? '处理失败'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $data = [
                'loginName' => $loginName,
                'balance' => round($result['balance'], 4),
                'rollbackAmount' => round($result['rollbackAmount'], 4)
            ];

            // 将data转为JSON字符串
            $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren betCancel 处理成功', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren betCancel 处理异常', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'error' => $e->getMessage()
            ]);
            return json_encode([
                'code' => 90000,
                'message' => '处理失败：' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 处理 gamePayout 派彩回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @param array $requestData 请求数据（可选，如果不传则从 request() 获取）
     * @param callable $gamePayoutCallback 派彩回调函数
     *   function($transferNo, $loginName, $payoutAmount, $gameTypeId, $roundNo, $payoutTime, $currency, $transferType, $playerId, $betPayoutMap) {
     *     return ['success' => true, 'balance' => $balance, 'realAmount' => $realAmount, 'badAmount' => $badAmount];
     *   }
     * @return string JSON 格式字符串
     */
    public function gamePayout($requestData = null, $gamePayoutCallback = null)
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren gamePayout 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren gamePayout 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren gamePayout params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        $transferNo = $paramsData['transferNo'] ?? '';
        $loginName = $paramsData['loginName'] ?? '';
        $payoutAmount = $paramsData['payoutAmount'] ?? 0;
        $gameTypeId = $paramsData['gameTypeId'] ?? 0;
        $roundNo = $paramsData['roundNo'] ?? '';
        $payoutTime = $paramsData['payoutTime'] ?? 0;
        $currency = $paramsData['currency'] ?? 'CNY';
        $transferType = $paramsData['transferType'] ?? 'PAYOUT';
        $playerId = $paramsData['playerId'] ?? 0;
        $betPayoutMap = $paramsData['betPayoutMap'] ?? [];

        if (empty($transferNo) || empty($loginName)) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            // 调用回调函数处理派彩
            $result = $gamePayoutCallback ? call_user_func($gamePayoutCallback, $transferNo, $loginName, $payoutAmount, $gameTypeId, $roundNo, $payoutTime, $currency, $transferType, $playerId, $betPayoutMap) : ['success' => false, 'message' => '回调函数未设置'];

            if (!$result['success']) {
                return json_encode([
                    'code' => 90000,
                    'message' => $result['message'] ?? '处理失败'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $data = [
                'loginName' => $loginName,
                'balance' => round($result['balance'], 4),
                'realAmount' => round($result['realAmount'], 6),
                'badAmount' => round($result['badAmount'], 6)
            ];

            // 将data转为JSON字符串
            $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren gamePayout 处理成功', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren gamePayout 处理异常', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'error' => $e->getMessage()
            ]);
            return json_encode([
                'code' => 90000,
                'message' => '处理失败：' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 处理 activityPayout 活动和小费类回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @param array $requestData 请求数据（可选，如果不传则从 request() 获取）
     * @param callable $activityPayoutCallback 活动派彩回调函数
     *   function($transferNo, $loginName, $payoutAmount, $payoutType, $transferType, $playerId, $payoutTime, $currency, $hasTransferOut) {
     *     return ['success' => true, 'balance' => $balance, 'realAmount' => $realAmount, 'badAmount' => $badAmount];
     *   }
     * @return string JSON 格式字符串
     */
    public function activityPayout($requestData = null, $activityPayoutCallback = null)
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren activityPayout 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren activityPayout 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren activityPayout params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        $transferNo = $paramsData['transferNo'] ?? '';
        $loginName = $paramsData['loginName'] ?? '';
        $payoutAmount = $paramsData['payoutAmount'] ?? 0;
        $payoutType = $paramsData['payoutType'] ?? '';
        $transferType = $paramsData['transferType'] ?? '';
        $playerId = $paramsData['playerId'] ?? 0;
        $payoutTime = $paramsData['payoutTime'] ?? 0;
        $currency = $paramsData['currency'] ?? 'CNY';
        $hasTransferOut = $paramsData['hasTransferOut'] ?? 0;

        if (empty($transferNo) || empty($loginName)) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            // 调用回调函数处理活动派彩
            $result = $activityPayoutCallback ? call_user_func($activityPayoutCallback, $transferNo, $loginName, $payoutAmount, $payoutType, $transferType, $playerId, $payoutTime, $currency, $hasTransferOut) : ['success' => false, 'message' => '回调函数未设置'];

            if (!$result['success']) {
                // 活动和消费类不允许产生坏账，余额不足时返回失败
                if ($payoutType === 'DEDUCTION' && $result['code'] == 1002) {
                    return json_encode([
                        'code' => 1002,
                        'message' => '余额不足'
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                return json_encode([
                    'code' => $result['code'] ?? 90000,
                    'message' => $result['message'] ?? '处理失败'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $data = [
                'loginName' => $loginName,
                'balance' => round($result['balance'], 4),
                'realAmount' => round($result['realAmount'], 4),
                'badAmount' => round($result['badAmount'], 4)
            ];

            // 将data转为JSON字符串
            $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren activityPayout 处理成功', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren activityPayout 处理异常', [
                'transferNo' => $transferNo,
                'loginName' => $loginName,
                'error' => $e->getMessage()
            ]);
            return json_encode([
                'code' => 90000,
                'message' => '处理失败：' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 处理 playerbetting 玩家下注推送回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @return string JSON 格式字符串
     */
    public function playerBetting()
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren playerBetting 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren playerBetting 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren playerBetting params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        if (empty($paramsData) || !is_array($paramsData)) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            // 处理下注推送数据
            // paramsData 是一个数组，每个元素包含 changePayout 和 bettingRecordList
            // changePayout 包含：transferNo, transferType, gameTypeId, roundNo, playerId, loginName, payoutTime, payoutAmount, currency
            // bettingRecordList 是一个数组，包含多个投注记录
            
            Log::info('Dbzhenren playerBetting 处理数据', [
                'params_data' => $paramsData,
                'data_count' => count($paramsData)
            ]);

            // 遍历 paramsData 数组，处理每个 changePayout 和 bettingRecordList
            foreach ($paramsData as $item) {
                $changePayout = $item['changePayout'] ?? [];
                $bettingRecordList = $item['bettingRecordList'] ?? [];

                if (empty($changePayout) || empty($bettingRecordList)) {
                    Log::warning('Dbzhenren playerBetting 数据格式错误', [
                        'changePayout' => $changePayout,
                        'bettingRecordList' => $bettingRecordList
                    ]);
                    continue;
                }

                $transferNo = $changePayout['transferNo'] ?? '';
                $transferType = $changePayout['transferType'] ?? '';
                $loginName = $changePayout['loginName'] ?? '';
                
                // 从loginName中移除merchant_code前缀（只移除开头的）
                $api_user = $loginName;
                if (!empty($this->merchant_code)) {
                    $merchantCodeLower = strtolower($this->merchant_code);
                    $loginNameLower = strtolower($loginName);
                    if (strpos($loginNameLower, $merchantCodeLower) === 0) {
                        $api_user = substr($loginName, strlen($this->merchant_code));
                    }
                }

                // 查找用户API记录
                $userApi = User_Api::where('api_user', $api_user)
                    ->where('api_code', $this->db_code)
                    ->first();

                if (!$userApi) {
                    Log::warning('Dbzhenren playerBetting 用户不存在', [
                        'loginName' => $loginName,
                        'api_user' => $api_user,
                        'api_code' => $this->db_code
                    ]);
                    continue;
                }

                // 获取用户信息
                $user = User::find($userApi->user_id);
                if (!$user) {
                    Log::error('Dbzhenren playerBetting 用户记录不存在', [
                        'user_id' => $userApi->user_id
                    ]);
                    continue;
                }

                // 处理每个投注记录
                foreach ($bettingRecordList as $bettingRecord) {
                    // 根据 zhenren.md，只有 recordType 为 1（正式）的记录才会返回给商户
                    $recordType = $bettingRecord['recordType'] ?? 0;
                    if ($recordType != 1) {
                        Log::info('Dbzhenren playerBetting 跳过非正式记录', [
                            'bettingRecord' => $bettingRecord,
                            'recordType' => $recordType
                        ]);
                        continue;
                    }

                    $betId = (string)($bettingRecord['id'] ?? '');
                    if (empty($betId)) {
                        Log::warning('Dbzhenren playerBetting betId 为空', [
                            'bettingRecord' => $bettingRecord
                        ]);
                        continue;
                    }

                    // 根据 bet_id 查找或创建记录
                    $gameRecord = GameRecord::where('bet_id', $betId)
                        ->where('platform_type', $this->db_code)
                        ->first();
                        $status == 2;
                        if($bettingRecord['betStatus'] == 2) $status = 0;
                        if($bettingRecord['betStatus'] == 1) $status = 1;
                    // 准备数据
                    $recordData = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'bet_id' => $betId,
                        'transfer_no' => $transferNo ? (string)$transferNo : null,
                        'round_no' => $bettingRecord['roundNo'] ?? $changePayout['roundNo'] ?? null,
                        'bet_point_id' => $bettingRecord['betPointId'] ?? null,
                        'bet_point_name' => isset($bettingRecord['betPointId']) && isset($this->betPoints[$bettingRecord['betPointId']]) 
                            ? $this->betPoints[$bettingRecord['betPointId']] 
                            : ($bettingRecord['betPointName'] ?? ''),
                        'game_type_id' => $bettingRecord['gameTypeId'] ?? $changePayout['gameTypeId'] ?? null,
                        'game_type_name' => isset($bettingRecord['gameTypeId']) && isset($this->gameTypes[$bettingRecord['gameTypeId']]) 
                            ? $this->gameTypes[$bettingRecord['gameTypeId']] 
                            : ($bettingRecord['gameTypeName'] ?? ''),
                        'platform_id' => $bettingRecord['platformId'] ?? null,
                        'platform_name' => isset($bettingRecord['platformId']) && isset($this->Halls[$bettingRecord['platformId']]) 
                            ? $this->Halls[$bettingRecord['platformId']] 
                            : ($bettingRecord['platformName'] ?? ''),
                        'player_id' => $bettingRecord['playerId'] ?? $changePayout['playerId'] ?? null,
                        'bet_flag' => $bettingRecord['betFlag'] ?? 0,
                        'table_code' => $bettingRecord['tableCode'] ?? null,
                        'boot_no' => $bettingRecord['bootNo'] ?? null,
                        'judge_result' => $bettingRecord['judgeResult'] ?? null,
                        'login_ip' => $bettingRecord['loginIp'] ?? null,
                        'bet_amount' => floatval($bettingRecord['betAmount'] ?? 0),
                        'valid_amount' => floatval($bettingRecord['validBetAmount'] ?? $bettingRecord['betAmount'] ?? 0),
                        'win_loss' => floatval($bettingRecord['netAmount'] ?? 0),
                        'before_amount' => floatval($bettingRecord['beforeAmount'] ?? 0),
                        'pay_amount' => floatval($bettingRecord['payAmount'] ?? 0),
                        'platform_type' => $this->db_code,
                        'game_type' => 'realbet',
                        'status' => $status, // 0=未结算 1=已结算 2=取消注单
                        'is_back' => 0,
                    ];

                    // 根据 transferType 调整状态
                    // PAYOUT=正常结算；DISCARD=跳局结算；CANCEL=取消局；REPAYOUT=重算局
                    if ($transferType === 'CANCEL') {
                        $recordData['status'] = 2; // 取消注单
                    } elseif ($transferType === 'PAYOUT' || $transferType === 'DISCARD' || $transferType === 'REPAYOUT') {
                        $recordData['status'] = 1; // 已结算
                    }

                    // payoutTime 转换为 datetime
                    if (isset($changePayout['payoutTime']) && $changePayout['payoutTime'] > 0) {
                        $payoutTimeSeconds = intval($changePayout['payoutTime'] / 1000);
                        $recordData['bet_time'] = date('Y-m-d H:i:s', $payoutTimeSeconds);
                    } else {
                        $recordData['bet_time'] = date('Y-m-d H:i:s');
                    }

                    if ($gameRecord) {
                        // 更新现有记录
                        $gameRecord->update($recordData);
                        Log::info('Dbzhenren playerBetting 更新记录', [
                            'bet_id' => $betId,
                            'transfer_no' => $transferNo,
                            'transfer_type' => $transferType
                        ]);
                    } else {
                        // 创建新记录
                        GameRecord::create($recordData);
                        Log::info('Dbzhenren playerBetting 创建记录', [
                            'bet_id' => $betId,
                            'transfer_no' => $transferNo,
                            'transfer_type' => $transferType
                        ]);
                    }
                }
            }

            $data = [
                'merchantCode' => $this->merchant_code
            ];

            // 将data转为JSON字符串
            $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren playerBetting 处理成功', [
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren playerBetting 处理异常', [
                'error' => $e->getMessage(),
                'params_data' => $paramsData
            ]);
            return json_encode([
                'code' => 90000,
                'message' => '处理失败：' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 处理 activityRebate 返利活动推送回调
     * 从 POST 请求中获取参数
     * 直接返回 JSON 格式响应，不依赖系统返回规则
     * 
     * @param array $requestData 请求数据（可选，如果不传则从 request() 获取）
     * @param callable $activityRebateCallback 返利推送回调函数
     *   function($detailId, $activityType, $agentId, $agentCode, $playerId, $loginName, $activityId, $activityName, $createdTime, $rewardAmount) {
     *     return ['success' => true];
     *   }
     * @return string JSON 格式字符串
     */
    public function activityRebate($requestData = null, $activityRebateCallback = null)
    {
        // 从 POST 请求中获取参数
        $request = request();
        
        // 记录所有请求参数，方便调试
        Log::info('Dbzhenren activityRebate 请求参数', [
            'all_post_params' => $request->all(),
            'params' => $request->input('params'),
            'signature' => $request->input('signature'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_input' => $request->getContent(),
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);
        
        $params = $request->input('params', '');
        $signature = $request->input('signature', '');

        // 验证 signature 签名（只对 params 字段进行验证）
        if (!$this->verifyParamsSignature($params, $signature)) {
            Log::error('Dbzhenren activityRebate 签名验证失败', [
                'params' => $params,
                'signature' => $signature,
                'raw_input' => $request->getContent()
            ]);
            // 直接返回 JSON 格式的错误响应
            return json_encode([
                'code' => 90001,
                'message' => '签名验证失败'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        // 解析 params 参数（可能是JSON字符串）
        $paramsData = [];
        if (!empty($params)) {
            // 尝试解析为JSON
            $decodedParams = json_decode($params, true);
            if (is_array($decodedParams)) {
                $paramsData = $decodedParams;
            } else {
                // 如果不是JSON，可能是其他格式，记录日志
                Log::warning('Dbzhenren activityRebate params 不是有效的JSON', [
                    'params' => $params
                ]);
            }
        }

        $detailId = $paramsData['detailId'] ?? 0;
        $activityType = $paramsData['activityType'] ?? 0;
        $agentId = $paramsData['agentId'] ?? 0;
        $agentCode = $paramsData['agentCode'] ?? '';
        $playerId = $paramsData['playerId'] ?? 0;
        $loginName = $paramsData['loginName'] ?? '';
        $activityId = $paramsData['activityId'] ?? 0;
        $activityName = $paramsData['activityName'] ?? '';
        $createdTime = $paramsData['createdTime'] ?? '';
        $rewardAmount = $paramsData['rewardAmount'] ?? 0;

        if (empty($detailId) || empty($loginName)) {
            return json_encode([
                'code' => 90000,
                'message' => '参数错误'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        try {
            // 调用回调函数处理返利推送
            $result = $activityRebateCallback ? call_user_func($activityRebateCallback, $detailId, $activityType, $agentId, $agentCode, $playerId, $loginName, $activityId, $activityName, $createdTime, $rewardAmount) : ['success' => false, 'message' => '回调函数未设置'];

            if (!$result['success']) {
                return json_encode([
                    'code' => 90000,
                    'message' => $result['message'] ?? '处理失败'
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $data = [
                'merchantCode' => $this->merchant_code
            ];

            // 将data转为JSON字符串
            $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            // 对data参数进行签名（使用md5_key）
            $responseSignature = $this->generateMd5KeySign($dataJson);

            // 构建成功响应
            $response = [
                'code' => 200,
                'message' => 'Success',
                'data' => $dataJson,
                'signature' => $responseSignature
            ];

            Log::info('Dbzhenren activityRebate 处理成功', [
                'detailId' => $detailId,
                'loginName' => $loginName,
                'response' => $response
            ]);

            // 直接返回 JSON 格式字符串
            return json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Dbzhenren activityRebate 处理异常', [
                'detailId' => $detailId,
                'loginName' => $loginName,
                'error' => $e->getMessage()
            ]);
            return json_encode([
                'code' => 90000,
                'message' => '处理失败：' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 解析回调请求参数
     * 
     * @param array $request 原始请求数据
     * @return array ['merchantCode', 'transferNo', 'params', 'signature', 'timestamp', 'paramsArray']
     */
    public function parseRequest($request)
    {
        $merchantCode = $request['merchantCode'] ?? '';
        $transferNo = $request['transferNo'] ?? '';
        $params = $request['params'] ?? '';
        $signature = $request['signature'] ?? '';
        $timestamp = $request['timestamp'] ?? 0;

        // 解析params JSON字符串
        $paramsArray = [];
        if (!empty($params)) {
            $paramsArray = json_decode($params, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Dbzhenren 解析params JSON失败', [
                    'params' => $params,
                    'json_error' => json_last_error_msg()
                ]);
            }
        }

        return [
            'merchantCode' => $merchantCode,
            'transferNo' => $transferNo,
            'params' => $params,
            'signature' => $signature,
            'timestamp' => $timestamp,
            'paramsArray' => $paramsArray
        ];
    }

    /**
     * 验证回调请求（包括签名验证）
     * 
     * @param array $request 原始请求数据
     * @return array ['valid' => bool, 'parsed' => array, 'error' => string]
     */
    public function validateRequest($request)
    {
        $parsed = $this->parseRequest($request);

        // 验证商户号
        if ($parsed['merchantCode'] !== $this->merchant_code) {
            return [
                'valid' => false,
                'parsed' => $parsed,
                'error' => '商户号不匹配'
            ];
        }

        // 验证签名
        if (!$this->verifySign($parsed['params'], $parsed['signature'])) {
            return [
                'valid' => false,
                'parsed' => $parsed,
                'error' => '签名验证失败'
            ];
        }

        return [
            'valid' => true,
            'parsed' => $parsed,
            'error' => ''
        ];
    }

    /**
     * 生成API请求签名（用于数据接口）
     * 签名算法：MD5("业务原文JSON+MD5盐值")
     * 
     * @param string $source 业务参数JSON字符串
     * @return string MD5签名（大写）
     */
    private function generateApiSign($source)
    {
        if (empty($this->secret_key)) {
            Log::error('Dbzhenren 密钥未配置');
            return '';
        }
        return strtoupper(md5($source . $this->secret_key));
    }

    /**
     * 构建加密请求参数（根据文档3.2节）
     * 将业务参数进行AES加密和MD5签名
     * 
     * @param array $businessParams 业务参数（原始JSON参数）
     * @return array 包含merchantCode、params（加密）、signature（签名）的请求参数
     */
    private function buildEncryptedRequest($businessParams)
    {
        // 在公共方法中统一设置 lang 固定为1
        $businessParams['lang'] = 1;
        
        // 记录原始业务参数
        Log::info('Dbzhenren 开始组装加密请求参数', [
            'business_params' => $businessParams,
            'merchant_code' => $this->merchant_code
        ]);
        
        // 1. 将业务参数转为JSON字符串
        // 注意：JavaScript 的 JSON.stringify 会保持数字类型为数字，字符串为字符串
        // 但参考代码中有些字段是字符串类型（如 deviceType: "1"），需要确保类型一致
        $sourceJson = json_encode($businessParams, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        if ($sourceJson === false) {
            Log::error('Dbzhenren 业务参数JSON编码失败', [
                'params' => $businessParams,
                'error' => json_last_error_msg()
            ]);
            return null;
        }
        
        // 记录JSON字符串（用于调试对比）
        Log::info('Dbzhenren 业务参数JSON字符串', [
            'source_json' => $sourceJson,
            'json_length' => strlen($sourceJson),
            'business_params' => $businessParams
        ]);
        
        // 2. 生成MD5签名：MD5(原始JSON + md5Key)
        // 参考代码：var signature = CryptoJS.MD5(params + md5Kye).toString().toUpperCase();
        $signString = $sourceJson . $this->secret_key;
        $signature = strtoupper(md5($signString));
        
        Log::info('Dbzhenren 生成MD5签名', [
            'source_json' => $sourceJson,
            'secret_key' => $this->secret_key,
            'sign_string' => $signString, // 完整签名字符串，用于对比
            'sign_string_length' => strlen($signString),
            'signature' => $signature
        ]);
        
        // 3. AES加密：AES/ECB/PKCS5Padding
        $encryptedParams = $this->aesEncrypt($sourceJson, $this->aes_key);
        
        if (empty($encryptedParams)) {
            Log::error('Dbzhenren AES加密失败', [
                'source_json_length' => strlen($sourceJson),
                'aes_key_length' => strlen($this->aes_key)
            ]);
            return null;
        }
        
        Log::info('Dbzhenren AES加密完成', [
            'encrypted_params_length' => strlen($encryptedParams),
            'encrypted_params_preview' => substr($encryptedParams, 0, 50) . '...'
        ]);
        
        // 4. 构建最终请求参数（只包含三个字段：merchantCode, params, signature）
        $finalParams = [
            'merchantCode' => $this->merchant_code,
            'params' => $encryptedParams, // AES加密后的Base64字符串
            'signature' => $signature      // MD5签名（大写）
        ];
        
        // 记录最终组装的参数
        Log::info('Dbzhenren 请求参数组装完成', [
            'merchant_code' => $this->merchant_code,
            'params_length' => strlen($encryptedParams),
            'signature' => $signature,
            'final_params_keys' => array_keys($finalParams), // 确认只有三个字段
            'final_params' => [
                'merchantCode' => $finalParams['merchantCode'],
                'params' => substr($encryptedParams, 0, 50) . '... (AES加密后的Base64)',
                'signature' => $finalParams['signature']
            ],
            'note' => '最终请求参数只包含三个字段：merchantCode, params, signature'
        ]);
        
        return $finalParams;
    }

    /**
     * 发送HTTP请求
     * 根据文档3.1节，所有接口都使用POST方式，传送JSON形式的数据
     *
     * @param string $url API地址
     * @param array $params 业务参数（会被加密和签名）
     * @param string $method 请求方法（POST/GET，默认POST）
     * @param string $contentType Content-Type（application/json 或 application/x-www-form-urlencoded，默认application/json）
     * @param array $headers 额外的请求头
     * @param bool $needEncrypt 是否需要加密（默认true，根据文档所有接口都需要加密）
     * @return array
     */
    private function sendRequest($url, $params = [], $method = 'POST', $contentType = 'application/json', $headers = [], $needEncrypt = true)
    {
        // 请求前日志记录
        Log::info('Dbzhenren 请求开始', [
            'url' => $url,
            'method' => $method,
            'content_type' => $contentType,
            'need_encrypt' => $needEncrypt,
            'original_params' => $params,
            'headers' => $headers,
            'params_count' => count($params),
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        $startTime = microtime(true); // 记录请求开始时间
        
        $ch = curl_init();
        $requestUrl = $url;
        $requestBody = '';
        $businessParams = $params; // 保存原始业务参数用于日志
        $finalRequestParams = null; // 保存最终请求参数（merchantCode, params, signature）

        if ($method === 'GET') {
            // GET请求不加密（如 /api/merchant/ok）
            $queryString = http_build_query($params);
            $requestUrl = $url . '?' . $queryString;
            
            // 记录GET请求参数
            Log::info('Dbzhenren GET请求参数组装完成', [
                'url' => $url,
                'request_url' => $requestUrl,
                'params' => $params,
                'query_string' => $queryString
            ]);
            
            curl_setopt($ch, CURLOPT_URL, $requestUrl);
            curl_setopt($ch, CURLOPT_POST, false);
            $requestBody = $queryString;
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            
            // 根据文档3.2节，所有接口都需要加密和签名（除了GET请求）
            if ($needEncrypt && $contentType === 'application/json') {
                // 构建加密请求参数
                $encryptedParams = $this->buildEncryptedRequest($params);
                
                if ($encryptedParams === null) {
                    Log::error('Dbzhenren 请求参数加密失败，终止请求', [
                        'url' => $url,
                        'business_params' => $params
                    ]);
                    curl_close($ch);
                    return [
                        'code' => -1,
                        'message' => '请求参数加密失败'
                    ];
                }
                
                // 保存最终请求参数（merchantCode, params, signature）
                $finalRequestParams = $encryptedParams;
                
                // 将加密后的参数转为JSON
                $requestBody = json_encode($encryptedParams, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                
                if ($requestBody === false) {
                    $jsonError = json_last_error_msg();
                    Log::error('Dbzhenren 加密参数JSON编码失败', [
                        'url' => $url,
                        'encrypted_params' => $encryptedParams,
                        'error' => $jsonError
                    ]);
                    curl_close($ch);
                    return [
                        'code' => -1,
                        'message' => 'JSON编码失败：' . $jsonError
                    ];
                }
                
                // 确保JSON字符串是UTF-8编码，去除BOM
                $requestBody = preg_replace('/^\xEF\xBB\xBF/', '', $requestBody);
                
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                $requestHeaders = array_merge([
                    'Content-Type: application/json'
                ], $headers);
            } elseif ($contentType === 'application/json') {
                // 不需要加密的JSON请求（特殊情况）
                $finalRequestParams = $params; // 保存最终请求参数
                $requestBody = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                
                if ($requestBody === false) {
                    $jsonError = json_last_error_msg();
                    Log::error('Dbzhenren JSON编码失败', [
                        'url' => $url,
                        'params' => $params,
                        'error' => $jsonError
                    ]);
                    curl_close($ch);
                    return [
                        'code' => -1,
                        'message' => 'JSON编码失败：' . $jsonError
                    ];
                }
                
                $requestBody = preg_replace('/^\xEF\xBB\xBF/', '', $requestBody);
                
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                $requestHeaders = array_merge([
                    'Content-Type: application/json'
                ], $headers);
            } else {
                // 表单格式（不常用，但保留兼容性）
                $finalRequestParams = $params; // 保存最终请求参数
                $requestBody = http_build_query($params);
                
                curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
                $requestHeaders = array_merge([
                    'Content-Type: application/x-www-form-urlencoded'
                ], $headers);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // 根据文档3.5节，超时时间设置为30秒
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // 记录最终请求参数和原始字符串
        // 对于加密请求，finalRequestParams 应该是 merchantCode, params, signature 三个字段
        if ($method === 'GET') {
            $finalRequestParams = $params;
        }
        
        Log::info('Dbzhenren 最终请求参数和原始字符串', [
            'url' => $requestUrl,
            'method' => $method,
            'content_type' => $contentType,
            'final_request_params' => $finalRequestParams, // 最终请求的参数数组（加密请求应该是 merchantCode, params, signature）
            'final_request_params_keys' => $finalRequestParams ? array_keys($finalRequestParams) : [], // 确认字段名称
            'final_request_params_count' => $finalRequestParams ? count($finalRequestParams) : 0, // 确认字段数量
            'request_body_raw_string' => $requestBody, // 请求的原始JSON字符串（完整内容）
            'request_body_length' => strlen($requestBody),
            'business_params' => $businessParams, // 原始业务参数（用于对比）
            'need_encrypt' => $needEncrypt,
            'note' => $needEncrypt && $contentType === 'application/json' ? '加密请求：最终参数应该是 merchantCode, params, signature 三个字段' : ''
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $endTime = microtime(true); // 记录请求结束时间
        $duration = round(($endTime - $startTime) * 1000, 2); // 计算请求耗时（毫秒）

        if ($curlError) {
            Log::error('Dbzhenren API请求CURL错误', [
                'url' => $requestUrl,
                'method' => $method,
                'curl_error' => $curlError,
                'http_code' => $httpCode,
                'duration_ms' => $duration
            ]);
            
            // 请求后日志记录（CURL错误）
            Log::info('Dbzhenren 请求结束（CURL错误）', [
                'url' => $requestUrl,
                'method' => $method,
                'http_code' => $httpCode,
                'duration_ms' => $duration,
                'status' => 'failed',
                'error' => $curlError,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            return [
                'code' => -1,
                'message' => '请求失败：' . $curlError
            ];
        }

        // 记录响应日志
        $responseData = json_decode($response, true);
        Log::info('Dbzhenren API响应', [
            'url' => $requestUrl,
            'method' => $method,
            'http_code' => $httpCode,
            'response' => $responseData ?: $response,
            'response_length' => strlen($response),
            'duration_ms' => $duration
        ]);

        // 如果HTTP状态码不是200，统一返回错误格式
        if ($httpCode !== 200) {
            $errorMessage = '请求失败';
            if ($responseData && is_array($responseData)) {
                // 尝试从响应中提取错误信息
                $errorMessage = $responseData['error'] ?? $responseData['message'] ?? 'HTTP ' . $httpCode;
            } else {
                $errorMessage = 'HTTP ' . $httpCode . ($response ? ': ' . substr($response, 0, 200) : '');
            }
            
            Log::error('Dbzhenren API请求失败', [
                'url' => $requestUrl,
                'http_code' => $httpCode,
                'response' => $responseData ?: $response,
                'duration_ms' => $duration
            ]);
            
            // 请求后日志记录（HTTP错误）
            Log::info('Dbzhenren 请求结束（HTTP错误）', [
                'url' => $requestUrl,
                'method' => $method,
                'http_code' => $httpCode,
                'duration_ms' => $duration,
                'status' => 'failed',
                'error' => $errorMessage,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            return [
                'code' => $httpCode,
                'message' => $errorMessage,
                'data' => $responseData ?: null
            ];
        }

        if (!$responseData || !is_array($responseData)) {
            Log::error('Dbzhenren API响应解析失败', [
                'url' => $requestUrl,
                'http_code' => $httpCode,
                'response' => $response,
                'duration_ms' => $duration
            ]);
            
            // 请求后日志记录（解析失败）
            Log::info('Dbzhenren 请求结束（解析失败）', [
                'url' => $requestUrl,
                'method' => $method,
                'http_code' => $httpCode,
                'duration_ms' => $duration,
                'status' => 'failed',
                'error' => '响应解析失败',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            return [
                'code' => -1,
                'message' => '响应解析失败',
                'data' => null,
                'raw_response' => $response
            ];
        }

        // 如果响应中没有code字段，尝试从HTTP状态码或其他字段推断
        if (!isset($responseData['code'])) {
            // 检查是否有status字段（Spring Boot错误格式）
            if (isset($responseData['status'])) {
                $responseData['code'] = $responseData['status'];
            } else {
                // 默认设置为200（因为HTTP状态码已经是200了）
                $responseData['code'] = 200;
            }
        }

        // 请求后日志记录（成功）
        $status = ($responseData['code'] == 200 || $responseData['code'] == '200') ? 'success' : 'failed';
        Log::info('Dbzhenren 请求结束', [
            'url' => $requestUrl,
            'method' => $method,
            'http_code' => $httpCode,
            'response_code' => $responseData['code'] ?? 'unknown',
            'duration_ms' => $duration,
            'status' => $status,
            'message' => $responseData['message'] ?? '',
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        return $responseData;
    }

    /**
     * AES加密（AES/ECB/PKCS5Padding）
     * 参考 CryptoJS.AES.encrypt 的实现
     * 
     * @param string $data 要加密的数据
     * @param string $key 加密密钥（UTF-8字符串）
     * @return string Base64编码的加密结果
     */
    private function aesEncrypt($data, $key)
    {
        if (empty($key)) {
            Log::error('Dbzhenren AES密钥未配置');
            return '';
        }
        
        // CryptoJS 使用 UTF-8 解析密钥，然后根据密钥长度选择 AES-128 或 AES-256
        // 如果密钥长度 <= 16 字节，使用 AES-128；如果 <= 32 字节，使用 AES-256
        $keyBytes = mb_convert_encoding($key, 'UTF-8', 'UTF-8');
        $keyLength = strlen($keyBytes);
        
        // 根据密钥长度选择加密算法
        if ($keyLength <= 16) {
            // AES-128：密钥长度必须是 16 字节，不足则用 null 填充
            $paddedKey = str_pad($keyBytes, 16, "\0", STR_PAD_RIGHT);
            $cipher = 'AES-128-ECB';
        } elseif ($keyLength <= 24) {
            // AES-192：密钥长度必须是 24 字节
            $paddedKey = str_pad($keyBytes, 24, "\0", STR_PAD_RIGHT);
            $cipher = 'AES-192-ECB';
        } else {
            // AES-256：密钥长度必须是 32 字节，超过则截断
            $paddedKey = substr(str_pad($keyBytes, 32, "\0", STR_PAD_RIGHT), 0, 32);
            $cipher = 'AES-256-ECB';
        }
        
        Log::info('Dbzhenren AES加密参数', [
            'key_length' => $keyLength,
            'padded_key_length' => strlen($paddedKey),
            'cipher' => $cipher,
            'data_length' => strlen($data)
        ]);
        
        // PHP的openssl_encrypt默认使用PKCS7填充（等同于PKCS5）
        $encrypted = openssl_encrypt($data, $cipher, $paddedKey, OPENSSL_RAW_DATA);
        
        if ($encrypted === false) {
            $error = openssl_error_string();
            Log::error('Dbzhenren AES加密失败', [
                'error' => $error,
                'cipher' => $cipher,
                'key_length' => strlen($paddedKey),
                'data_length' => strlen($data)
            ]);
            return '';
        }
        
        // CryptoJS 返回的是 Base64 编码的字符串
        $base64Result = base64_encode($encrypted);
        
        Log::info('Dbzhenren AES加密成功', [
            'encrypted_length' => strlen($encrypted),
            'base64_length' => strlen($base64Result),
            'base64_preview' => substr($base64Result, 0, 50) . '...'
        ]);
        
        return $base64Result;
    }

    /**
     * AES解密（AES/ECB/PKCS5Padding）
     * 
     * @param string $encryptedData Base64编码的加密数据
     * @param string $key 解密密钥
     * @return string 解密后的原始数据
     */
    private function aesDecrypt($encryptedData, $key)
    {
        if (empty($key)) {
            Log::error('Dbzhenren AES密钥未配置');
            return '';
        }
        
        $data = base64_decode($encryptedData);
        $decrypted = openssl_decrypt($data, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        
        if ($decrypted === false) {
            Log::error('Dbzhenren AES解密失败', [
                'error' => openssl_error_string()
            ]);
            return '';
        }
        
        return $decrypted;
    }

    /**
     * 检测是否为移动端访问
     * 
     * @return bool
     */
    private function isMobile()
    {
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
        return false;
    }

    /**
     * 根据接口路径判断接口类型并返回正确的API URL
     * 
     * @param string $path 接口路径（如 /api/merchant/create/v2 或 /data/merchant/betHistoryRecord/v1）
     * @return string 完整的API URL
     */
    private function getApiUrl($path)
    {
        // 判断是基础接口还是数据接口
        if (strpos($path, '/data/merchant/') === 0) {
            // 数据接口：使用 api_data_url（如果配置了），否则使用 api_url
            $baseUrl = !empty($this->api_data_url) ? $this->api_data_url : $this->api_url;
        } else {
            // 基础接口：使用 api_url
            $baseUrl = $this->api_url;
        }
        
        return rtrim($baseUrl, '/') . $path;
    }

    /**
     * 发送数据接口请求（带签名和Header）
     * 用于5.x节的数据接口
     * 根据文档3.2节，数据接口也需要加密和签名
     *
     * @param string $method 接口方法名（如 betHistoryRecord, reportPlayer）
     * @param array $params 业务参数
     * @param int $pageIndex 页码（用于Header）
     * @return array
     */
    private function sendDataRequest($method, $params, $pageIndex = 1)
    {
        // 数据接口使用 api_data_url
        if (empty($this->api_data_url)) {
            Log::error('Dbzhenren API Data URL未配置');
            return [
                'code' => -1,
                'message' => 'API Data URL未配置'
            ];
        }

        // 生成时间戳（如果不存在）
        if (!isset($params['timestamp'])) {
            $params['timestamp'] = time() * 1000; // 毫秒级时间戳
        }

        // 设置请求头（数据接口的特殊headers）
        $headers = [
            'merchantCode: ' . $this->merchant_code,
            'pageIndex: ' . $pageIndex
        ];

        // 直接使用 api_data_url 构建URL
        $url = rtrim($this->api_data_url, '/') . '/data/merchant/' . $method . '/v1';
        
        // 使用sendRequest方法，它会自动对业务参数进行加密和签名
        return $this->sendRequest($url, $params, 'POST', 'application/json', $headers, true);
    }

    /**
     * 4.1 创建游戏账号
     * API地址：/api/merchant/create/v1 或 /api/merchant/create/v2
     * 
     * @param string $loginName 游戏账号（6-50字符，需要包括商户的前缀，只能包含以下特殊字符[下划线、@、#、&、*]）
     * @param string $loginPassword 登陆密码（6-32字符，不允许的符号：`''[]./'"'$）
     * @param string $api_code API代码（可选）
     * @param string $nickName 昵称（可选，最多12位的数字+字母，以及允许下划线和@符号）
     * @param int $oddType 盘口类型（V1版本必填，V2版本已取消，请参考限红类型附件）
     * @param int $lang 语言（固定设为1）
     * @param string $version 版本（v1 或 v2，默认v2，建议使用V2版本）
     * @return array 返回格式：['code' => 200, 'message' => '成功', 'request' => [...], 'data' => [...]]
     */
    public function register($loginName, $loginPassword, $api_code = "", $nickName = '', $oddType = 0, $lang = 1, $version = 'v2')
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        // V1版本必须要有oddType参数
        if ($version === 'v1' && $oddType <= 0) {
            return [
                'code' => 400,
                'message' => 'V1版本必须提供oddType参数（盘口类型）'
            ];
        }

        // 在用户名前面加上merchant_code前缀（如果还没有的话）
        $fullLoginName = (string)$loginName;
        if (!empty($this->merchant_code)) {
            // 检查用户名是否已经包含merchant_code前缀
            if (strpos($fullLoginName, $this->merchant_code) !== 0) {
                $fullLoginName = $this->merchant_code . $fullLoginName;
            }
        }

        // 参数验证（在加上前缀后验证）
        if (empty($fullLoginName) || strlen($fullLoginName) < 6 || strlen($fullLoginName) > 50) {
            return [
                'code' => 400,
                'message' => '游戏账号长度必须在6-50字符之间（包含商户前缀）'
            ];
        }

        if (empty($loginPassword) || strlen($loginPassword) < 6 || strlen($loginPassword) > 32) {
            return [
                'code' => 400,
                'message' => '登陆密码长度必须在6-32字符之间'
            ];
        }

        if (!empty($nickName) && strlen($nickName) > 12) {
            return [
                'code' => 400,
                'message' => '昵称最多12位'
            ];
        }

        // 确保参数类型正确
        $params = [
            'loginName' => $fullLoginName,
            'loginPassword' => (string)$loginPassword,
            'timestamp' => (int)(time() * 1000), // 确保是整数类型
        ];

        // 昵称可选参数
        if (!empty($nickName)) {
            $params['nickName'] = (string)$nickName;
        }

        // V1版本必须包含oddType参数
        if ($version === 'v1') {
            $params['oddType'] = (int)$oddType;
        }

        $url = $this->getApiUrl('/api/merchant/create/' . $version);
        return $this->sendRequest($url, $params, 'POST');
    }

    /**
     * 4.2 快捷开始游戏(三合一)
     * 综合了创建游戏账号接口、上分接口、进入游戏接口
     * 
     * @param string $loginName 游戏账号
     * @param string $loginPassword 游戏密码
     * @param int $deviceType 设备类型（1=PC, 2=H5, 3=iOS, 4=Android）
     * @param float $amount 转账金额（可选，大于0时视为期望同时带入余额）
     * @param string $transferNo 转账单号（可选，带金额时必填）
     * @param int $oddType 盘口类型（V1版本必填，V2版本已取消）
     * @param int $lang 语言（固定设为1）
     * @param string $backurl 返回商户地址（可选）
     * @param string $domain 动态游戏域名（可选）
     * @param int $showExit 是否显示退出按钮（0=显示，1=不显示）
     * @param int $gameTypeId 游戏类型ID（可选）
     * @param int $anchorId 主播ID（可选）
     * @param string $ip 透传玩家真实ip（可选）
     * @param int $isCompetition 是否进入大赛（0=大厅，1=大赛）
     * @param string $playerLanguageV2 玩家首次登录默认语言（可选）
     * @param string $version 版本（v1 或 v2，默认v2）
     * @return array
     */
    public function fastGame($loginName, $loginPassword, $deviceType = 2, $amount = 0, $transferNo = '', $oddType = 0, $lang = 1, $backurl = '', $domain = '', $showExit = 0, $gameTypeId = 0, $anchorId = 0, $ip = '', $isCompetition = 0, $playerLanguageV2 = '', $version = 'v2')
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        // 在用户名前面加上merchant_code前缀（如果还没有的话）
        $fullLoginName = (string)$loginName;
        if (!empty($this->merchant_code)) {
            // 检查用户名是否已经包含merchant_code前缀
            if (strpos($fullLoginName, $this->merchant_code) !== 0) {
                $fullLoginName = $this->merchant_code . $fullLoginName;
            }
        }

        $params = [
            'loginName' => $fullLoginName,
            'loginPassword' => $loginPassword,
            'deviceType' => $deviceType,
            'timestamp' => time() * 1000,
        ];

        // V1版本需要oddType参数
        if ($version === 'v1' && $oddType > 0) {
            $params['oddType'] = $oddType;
        }

        if (!empty($backurl)) {
            $params['backurl'] = $backurl;
        }
        if (!empty($domain)) {
            $params['domain'] = $domain;
        }
        if ($showExit > 0) {
            $params['showExit'] = $showExit;
        }
        if ($gameTypeId > 0) {
            $params['gameTypeId'] = $gameTypeId;
        }
        if ($anchorId > 0) {
            $params['anchorId'] = $anchorId;
        }
        if (!empty($ip)) {
            $params['ip'] = $ip;
        }
        if ($isCompetition > 0) {
            $params['isCompetition'] = $isCompetition;
        }
        if (!empty($playerLanguageV2)) {
            $params['playerLanguageV2'] = $playerLanguageV2;
        }

        // 如果传递了金额，则同时上分
        if ($amount > 0) {
            $params['amount'] = $amount;
            if (empty($transferNo)) {
                $transferNo = time() . rand(100000, 999999);
            }
            $params['transferNo'] = $transferNo;
        }

        $url = $this->getApiUrl('/api/merchant/fastGame/' . $version);
        return $this->sendRequest($url, $params, 'POST');
    }

    /**
     * 4.7 禁止/开启投注状态
     * 
     * @param string $loginName 游戏账号
     * @param int $enabled 状态（0=开启，1=禁用）
     * @return array
     */
    public function enableUserBet($loginName, $enabled = 1)
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        $params = [
            'loginName' => $loginName,
            'enabled' => $enabled,
            'timestamp' => time() * 1000,
        ];

        $url = $this->getApiUrl('/api/merchant/enableUserBetd/v1');
        return $this->sendRequest($url, $params, 'POST');
    }

    /**
     * 4.8 重置游戏登陆密码
     * 
     * @param string $loginName 游戏账号
     * @param string $newPassword 新的密码
     * @return array
     */
    public function resetPassword($loginName, $newPassword)
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        $params = [
            'loginName' => $loginName,
            'newPassword' => $newPassword,
            'timestamp' => time() * 1000,
        ];

        $url = $this->getApiUrl('/api/merchant/resetLoginPwd/v1');
        return $this->sendRequest($url, $params, 'POST');
    }

    /**
     * 4.9 进入游戏
     * 
     * @param string $loginName 游戏账号
     * @param string $loginPassword 游戏密码
     * @param string $api_code API代码（可选）
     * @param int $deviceType 设备类型（1=PC, 2=H5, 3=iOS, 4=Android）
     * @param int $oddType 盘口类型（V1版本必填，V2版本已取消）
     * @param int $lang 语言（固定设为1）
     * @param string $backurl 返回商户地址（可选）
     * @param string $domain 动态游戏域名（可选）
     * @param int $showExit 是否显示退出按钮（0=显示，1=不显示）
     * @param int $gameTypeId 游戏类型ID（可选）
     * @param int $anchorId 主播ID（可选）
     * @param string $ip 透传玩家真实ip（可选）
     * @param int $isCompetition 是否进入大赛（0=大厅，1=大赛）
     * @param string $playerLanguageV2 玩家首次登录默认语言（可选）
     * @param string $version 版本（v1 或 v2，默认v2）
     * @return array
     */
    public function login($loginName, $loginPassword, $api_code = "", $deviceType = 2, $oddType = 0, $lang = 1, $backurl = '', $domain = '', $showExit = 0, $gameTypeId = "", $anchorId = 0, $ip = '', $isCompetition = 0, $playerLanguageV2 = '', $version = 'v2')
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        // 在用户名前面加上merchant_code前缀（如果还没有的话）
        $fullLoginName = (string)$loginName;
        if (!empty($this->merchant_code)) {
            // 检查用户名是否已经包含merchant_code前缀
            if (strpos($fullLoginName, $this->merchant_code) !== 0) {
                $fullLoginName = $this->merchant_code . $fullLoginName;
            }
        }

        // 如果没有传入 backurl，根据访问类型自动设置
        if (empty($backurl)) {
            $isMobile = $this->isMobile();
            if ($isMobile) {
                $backurl = env('WAP_URL', '');
            } else {
                $backurl = env('PC_URL', '');
            }
        }

        $params = [
            'loginName' => $fullLoginName,
            'loginPassword' => $loginPassword,
            'deviceType' => $deviceType,
            'timestamp' => time() * 1000,
        ];

        // V1版本需要oddType参数
        if ($version === 'v1' && $oddType > 0) {
            $params['oddType'] = $oddType;
        }

        if (!empty($backurl)) {
            $params['backurl'] = $backurl;
        }
        if (!empty($domain)) {
            $params['domain'] = $domain;
        }
        if ($showExit > 0) {
            $params['showExit'] = $showExit;
        }
        if ($gameTypeId > 0) {
            $params['gameTypeId'] = $gameTypeId;
        }
        if ($anchorId > 0) {
            $params['anchorId'] = $anchorId;
        }
        if (!empty($ip)) {
            $params['ip'] = $ip;
        }
        if ($isCompetition > 0) {
            $params['isCompetition'] = $isCompetition;
        }
        if (!empty($playerLanguageV2)) {
            $params['playerLanguageV2'] = $playerLanguageV2;
        }

        $url = $this->getApiUrl('/api/merchant/forwardGame/' . $version);
        $res = $this->sendRequest($url, $params, 'POST');
        $res["data"] = $res["code"] == 200 ? $res["data"]["url"] : "";
        return $res;
    }

    /**
     * 4.11 获取游戏维护状态
     * 
     * @return array
     */
    public function checkMaintenance()
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        $params = [
            'timestamp' => time() * 1000,
        ];

        $url = $this->getApiUrl('/api/merchant/checkMaintaince/v1');
        return $this->sendRequest($url, $params, 'POST');
    }

    /**
     * 4.12 校验API接口是否可访问
     * GET方法，无参数
     * 
     * @return array
     */
    public function checkOk()
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        $url = $this->getApiUrl('/api/merchant/ok');
        return $this->sendRequest($url, [], 'GET');
    }

    /**
     * 4.14 会员离桌接口
     * 
     * @param string $loginName 游戏账号
     * @param int $tableId 桌台id（可选）
     * @return array
     */
    public function leaveTable($loginName, $tableId = 0)
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        $params = [
            'loginName' => $loginName,
            'timestamp' => time() * 1000,
        ];

        if ($tableId > 0) {
            $params['tableId'] = $tableId;
        }

        $url = $this->getApiUrl('/api/merchant/foreLeaveTable/v1');
        return $this->sendRequest($url, $params, 'POST');
    }

    /**
     * 4.15 获取各商户场馆桌台数量
     * 
     * @return array
     */
    public function getTableNumber()
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        $params = [
            'timestamp' => time() * 1000,
        ];

        $url = $this->getApiUrl('/api/merchant/agentTableNumber/v1');
        return $this->sendRequest($url, $params, 'POST');
    }

    /**
     * 5.2 游戏记录(时间区间)
     * 
     * @param string $startTime 开始时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $endTime 结束时间（格式：yyyy-MM-dd HH:mm:ss，时间范围不能超过30分钟）
     * @param int $pageIndex 页码
     * @return array
     */
    public function getGameRecords($startTime, $endTime, $pageIndex = 1)
    {
        $params = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        return $this->sendDataRequest('betHistoryRecord', $params, $pageIndex);
    }

    /**
     * 5.3 对账接口（按代理按日统计注单量）
     * 
     * @param int $startDate 报表开始日期（格式：yyyyMMdd）
     * @param int $endDate 报表结束日期（格式：yyyyMMdd）
     * @param int $pageIndex 页码
     * @param int $exchange 是否转换为商户货币（0=游戏币，1=商户货币）
     * @return array
     */
    public function getReportAgent($startDate, $endDate, $pageIndex = 1, $exchange = 0)
    {
        $params = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        if ($exchange > 0) {
            $params['exchange'] = $exchange;
        }

        return $this->sendDataRequest('reportAgent', $params, $pageIndex);
    }

    /**
     * 5.4 对账接口（按会员按日统计注单量）
     * 
     * @param int $startDate 报表开始日期（格式：yyyyMMdd）
     * @param int $endDate 报表结束日期（格式：yyyyMMdd）
     * @param int $pageIndex 页码
     * @param int $exchange 是否转换为商户货币（0=游戏币，1=商户货币）
     * @return array
     */
    public function getReportPlayer($startDate, $endDate, $pageIndex = 1, $exchange = 0)
    {
        $params = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        if ($exchange > 0) {
            $params['exchange'] = $exchange;
        }

        return $this->sendDataRequest('reportPlayer', $params, $pageIndex);
    }

    /**
     * 5.5 查询在线会员列表
     * 
     * @param int $pageIndex 页码
     * @return array
     */
    public function getOnlineUsers($pageIndex = 1)
    {
        $params = [
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        return $this->sendDataRequest('onlineUsers', $params, $pageIndex);
    }

    /**
     * 5.6 活动彩金数据
     * 
     * @param string $startTime 开始时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $endTime 结束时间（格式：yyyy-MM-dd HH:mm:ss，时间范围不能超过30分钟）
     * @param int $pageIndex 页码
     * @param int $activityType 活动类型（可选，1=红包雨，2=玩法返利，10=任务奖励，11=抽奖，12=兑奖）
     * @return array
     */
    public function getActivityRecord($startTime, $endTime, $pageIndex = 1, $activityType = 0)
    {
        $params = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        if ($activityType > 0) {
            $params['activityType'] = $activityType;
        }

        return $this->sendDataRequest('activityRecord', $params, $pageIndex);
    }

    /**
     * 5.7 打赏明细数据
     * 
     * @param string $startTime 开始时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $endTime 结束时间（格式：yyyy-MM-dd HH:mm:ss，时间范围不能超过30分钟）
     * @param int $pageIndex 页码
     * @return array
     */
    public function getRewardRecord($startTime, $endTime, $pageIndex = 1)
    {
        $params = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        return $this->sendDataRequest('rewardRecordList', $params, $pageIndex);
    }

    /**
     * 5.8 大赛流水记录(时间区间)
     * 
     * @param string $startTime 开始时间（格式：yyyy-MM-dd HH:mm:ss）
     * @param string $endTime 结束时间（格式：yyyy-MM-dd HH:mm:ss，时间范围不能超过30分钟）
     * @param int $pageIndex 页码
     * @return array
     */
    public function getMatchAccountChange($startTime, $endTime, $pageIndex = 1)
    {
        $params = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        return $this->sendDataRequest('matchAccountChange', $params, $pageIndex);
    }

    /**
     * 5.9 主播列表
     * 
     * @param int $clientType 客户端类型（1=WEB, 2=iOS, 3=Android）
     * @param string $ip 客户端ip
     * @param int $pageIndex 页码
     * @param int $pageSize 每页条数
     * @return array
     */
    public function getLives($clientType = 1, $ip = '', $pageIndex = 1, $pageSize = 10)
    {
        $params = [
            'clientType' => $clientType,
            'pageIndex' => $pageIndex,
            'pageSize' => $pageSize,
            'timestamp' => time() * 1000,
        ];

        if (!empty($ip)) {
            $params['ip'] = $ip;
        }

        return $this->sendDataRequest('lives', $params, $pageIndex);
    }

    /**
     * 5.10 异常注单状态查询
     * 
     * @param int $id 注单号（可选）
     * @param string $roundNo 局号（可选）
     * @param array $roundNoList 注单局号集合（可选，批量查询，数量不能大于50）
     * @param int $dataStatus 注单状态（可选，1=正常，2=商户禁用，3=下注失败，4=余额不足，5=局状态不对，6=下注确认失败，7=其它异常）
     * @param int $pageIndex 页码
     * @return array
     */
    public function queryAbnormalBetting($id = 0, $roundNo = '', $roundNoList = [], $dataStatus = 0, $pageIndex = 1)
    {
        $params = [
            'pageIndex' => $pageIndex,
            'timestamp' => time() * 1000,
        ];

        if ($id > 0) {
            $params['id'] = $id;
        }
        if (!empty($roundNo)) {
            $params['roundNo'] = $roundNo;
        }
        if (!empty($roundNoList) && is_array($roundNoList) && count($roundNoList) <= 50) {
            $params['roundNoList'] = $roundNoList;
        }
        if ($dataStatus > 0) {
            $params['dataStatus'] = $dataStatus;
        }

        return $this->sendDataRequest('queryAbnormalBettingData', $params, $pageIndex);
    }

    /**
     * 5.11 好路桌台
     * 
     * @param string $goodRoadTypes 好路类型（多个类型用,号隔开，不填则返回所有类型）
     * @return array
     */
    public function getGoodRoadTables($goodRoadTypes = '')
    {
        $params = [
            'timestamp' => time() * 1000,
        ];

        if (!empty($goodRoadTypes)) {
            $params['goodRoadTypes'] = $goodRoadTypes;
        }

        return $this->sendDataRequest('goodRoadTables', $params, 1);
    }

    /**
     * 5.12 查询单个会员在线状态
     * 
     * @param string $loginName 游戏账号
     * @return array
     */
    public function getPlayerOnlineStatus($loginName)
    {
        if (empty($this->api_url)) {
            return [
                'code' => 400,
                'message' => 'API URL未配置'
            ];
        }

        $params = [
            'loginName' => $loginName,
            'timestamp' => time() * 1000,
        ];

        $headers = [
            'merchantCode: ' . $this->merchant_code
        ];

        $url = $this->getApiUrl('/data/merchant/playerIsOnline/v1');
        return $this->sendRequest($url, $params, 'POST', 'application/json', $headers);
    }

    /**
     * 5.13 主播排班
     * 
     * @param string $dayStr 当前时间（日期格式 yyyy-MM-dd）
     * @return array
     */
    public function getAnchorScheduling($dayStr = '')
    {
        if (empty($dayStr)) {
            $dayStr = date('Y-m-d');
        }

        $params = [
            'dayStr' => $dayStr,
            'timestamp' => time() * 1000,
        ];

        return $this->sendDataRequest('getAnchorSchedulingOfDate', $params, 1);
    }
}

