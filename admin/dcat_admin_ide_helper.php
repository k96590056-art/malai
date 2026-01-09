<?php

/**
 * A helper file for Dcat Admin, to provide autocomplete information to your IDE
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author jqh <841324345@qq.com>
 */
namespace Dcat\Admin {
    use Illuminate\Support\Collection;

    /**
     * @property Grid\Column|Collection logintime
     * @property Grid\Column|Collection id
     * @property Grid\Column|Collection type
     * @property Grid\Column|Collection content
     * @property Grid\Column|Collection apply_count
     * @property Grid\Column|Collection banner
     * @property Grid\Column|Collection can_apply
     * @property Grid\Column|Collection state
     * @property Grid\Column|Collection created_at
     * @property Grid\Column|Collection updated_at
     * @property Grid\Column|Collection activity_id
     * @property Grid\Column|Collection user_id
     * @property Grid\Column|Collection check_time
     * @property Grid\Column|Collection name
     * @property Grid\Column|Collection version
     * @property Grid\Column|Collection detail
     * @property Grid\Column|Collection is_enabled
     * @property Grid\Column|Collection parent_id
     * @property Grid\Column|Collection order
     * @property Grid\Column|Collection icon
     * @property Grid\Column|Collection uri
     * @property Grid\Column|Collection extension
     * @property Grid\Column|Collection show
     * @property Grid\Column|Collection permission_id
     * @property Grid\Column|Collection menu_id
     * @property Grid\Column|Collection slug
     * @property Grid\Column|Collection http_method
     * @property Grid\Column|Collection http_path
     * @property Grid\Column|Collection role_id
     * @property Grid\Column|Collection value
     * @property Grid\Column|Collection username
     * @property Grid\Column|Collection password
     * @property Grid\Column|Collection avatar
     * @property Grid\Column|Collection remember_token
     * @property Grid\Column|Collection apply_info
     * @property Grid\Column|Collection realperson
     * @property Grid\Column|Collection electron
     * @property Grid\Column|Collection joker
     * @property Grid\Column|Collection sport
     * @property Grid\Column|Collection fish
     * @property Grid\Column|Collection lottery
     * @property Grid\Column|Collection e_sport
     * @property Grid\Column|Collection member_fs
     * @property Grid\Column|Collection api_code
     * @property Grid\Column|Collection api_name
     * @property Grid\Column|Collection api_money
     * @property Grid\Column|Collection game_type
     * @property Grid\Column|Collection plat_type
     * @property Grid\Column|Collection app_state
     * @property Grid\Column|Collection cateid
     * @property Grid\Column|Collection stor
     * @property Grid\Column|Collection Created_at
     * @property Grid\Column|Collection Updated_at
     * @property Grid\Column|Collection code
     * @property Grid\Column|Collection bank_name
     * @property Grid\Column|Collection max_amount
     * @property Grid\Column|Collection bank_img
     * @property Grid\Column|Collection pic
     * @property Grid\Column|Collection jump_url
     * @property Grid\Column|Collection mch_id
     * @property Grid\Column|Collection key
     * @property Grid\Column|Collection status
     * @property Grid\Column|Collection payimg
     * @property Grid\Column|Collection connection
     * @property Grid\Column|Collection queue
     * @property Grid\Column|Collection payload
     * @property Grid\Column|Collection exception
     * @property Grid\Column|Collection failed_at
     * @property Grid\Column|Collection platform_name
     * @property Grid\Column|Collection name_en
     * @property Grid\Column|Collection keywords
     * @property Grid\Column|Collection category_id
     * @property Grid\Column|Collection order_by
     * @property Grid\Column|Collection is_hot
     * @property Grid\Column|Collection is_new
     * @property Grid\Column|Collection is_recommend
     * @property Grid\Column|Collection is_pc
     * @property Grid\Column|Collection is_mobile
     * @property Grid\Column|Collection site_state
     * @property Grid\Column|Collection bet_id
     * @property Grid\Column|Collection bet_time
     * @property Grid\Column|Collection platform_type
     * @property Grid\Column|Collection bet_amount
     * @property Grid\Column|Collection valid_amount
     * @property Grid\Column|Collection win_loss
     * @property Grid\Column|Collection is_back
     * @property Grid\Column|Collection uid
     * @property Grid\Column|Collection addtime
     * @property Grid\Column|Collection pid
     * @property Grid\Column|Collection recnum
     * @property Grid\Column|Collection rechangenum
     * @property Grid\Column|Collection totalrechange
     * @property Grid\Column|Collection withdrawnum
     * @property Grid\Column|Collection totalwithdraw
     * @property Grid\Column|Collection betnum
     * @property Grid\Column|Collection totalbet
     * @property Grid\Column|Collection totalvalidamount
     * @property Grid\Column|Collection totalwinloss
     * @property Grid\Column|Collection redpackectnum
     * @property Grid\Column|Collection totalredpackect
     * @property Grid\Column|Collection releasewater
     * @property Grid\Column|Collection rakeback
     * @property Grid\Column|Collection rakebacknum
     * @property Grid\Column|Collection releasewaternum
     * @property Grid\Column|Collection isagent
     * @property Grid\Column|Collection vip_id
     * @property Grid\Column|Collection email
     * @property Grid\Column|Collection token
     * @property Grid\Column|Collection bank_id
     * @property Grid\Column|Collection bank_no
     * @property Grid\Column|Collection bank_owner
     * @property Grid\Column|Collection bank_address
     * @property Grid\Column|Collection info
     * @property Grid\Column|Collection order_no
     * @property Grid\Column|Collection out_trade_no
     * @property Grid\Column|Collection amount
     * @property Grid\Column|Collection cash_fee
     * @property Grid\Column|Collection real_money
     * @property Grid\Column|Collection pay_way
     * @property Grid\Column|Collection bank
     * @property Grid\Column|Collection day_flow
     * @property Grid\Column|Collection recharge
     * @property Grid\Column|Collection flow_money
     * @property Grid\Column|Collection money
     * @property Grid\Column|Collection start_time
     * @property Grid\Column|Collection end_time
     * @property Grid\Column|Collection img
     * @property Grid\Column|Collection memo
     * @property Grid\Column|Collection client_type
     * @property Grid\Column|Collection sort
     * @property Grid\Column|Collection template_id
     * @property Grid\Column|Collection api_type
     * @property Grid\Column|Collection transfer_type
     * @property Grid\Column|Collection before_money
     * @property Grid\Column|Collection after_money
     * @property Grid\Column|Collection settlementsday
     * @property Grid\Column|Collection betid
     * @property Grid\Column|Collection deleted_at
     * @property Grid\Column|Collection message_id
     * @property Grid\Column|Collection login_ua
     * @property Grid\Column|Collection login_ip
     * @property Grid\Column|Collection ip_address
     * @property Grid\Column|Collection desc
     * @property Grid\Column|Collection vipname
     * @property Grid\Column|Collection viptype
     * @property Grid\Column|Collection flow
     * @property Grid\Column|Collection exp
     * @property Grid\Column|Collection is_default
     * @property Grid\Column|Collection vrberfee
     * @property Grid\Column|Collection ldfee
     * @property Grid\Column|Collection redpacketid
     * @property Grid\Column|Collection redpacketfee
     * @property Grid\Column|Collection redpacketmoney
     * @property Grid\Column|Collection usetime
     * @property Grid\Column|Collection isuse
     * @property Grid\Column|Collection fid
     * @property Grid\Column|Collection api_token
     * @property Grid\Column|Collection realname
     * @property Grid\Column|Collection vip
     * @property Grid\Column|Collection level
     * @property Grid\Column|Collection paypwd
     * @property Grid\Column|Collection isonline
     * @property Grid\Column|Collection allowagent
     * @property Grid\Column|Collection balance
     * @property Grid\Column|Collection mbalance
     * @property Grid\Column|Collection totalgame
     * @property Grid\Column|Collection phone
     * @property Grid\Column|Collection mail
     * @property Grid\Column|Collection paysum
     * @property Grid\Column|Collection isdel
     * @property Grid\Column|Collection isblack
     * @property Grid\Column|Collection lastip
     * @property Grid\Column|Collection last_login_ip_address
     * @property Grid\Column|Collection sourceurl
     * @property Grid\Column|Collection loginsum
     * @property Grid\Column|Collection birthday
     * @property Grid\Column|Collection settlement_id
     * @property Grid\Column|Collection fanshuifee
     * @property Grid\Column|Collection settlementday
     * @property Grid\Column|Collection reg_ip
     * @property Grid\Column|Collection ag_money
     * @property Grid\Column|Collection allbet_money
     * @property Grid\Column|Collection bbin_money
     * @property Grid\Column|Collection bg_money
     * @property Grid\Column|Collection og_money
     * @property Grid\Column|Collection pt_money
     * @property Grid\Column|Collection gd_money
     * @property Grid\Column|Collection dg_money
     * @property Grid\Column|Collection qt_money
     * @property Grid\Column|Collection ky_money
     * @property Grid\Column|Collection ig_money
     * @property Grid\Column|Collection jdb_money
     * @property Grid\Column|Collection fg_money
     * @property Grid\Column|Collection avia_money
     * @property Grid\Column|Collection leg_money
     * @property Grid\Column|Collection bng_money
     * @property Grid\Column|Collection dt_money
     * @property Grid\Column|Collection gg_money
     * @property Grid\Column|Collection vrbet_money
     * @property Grid\Column|Collection hlgame_money
     * @property Grid\Column|Collection hbb_money
     * @property Grid\Column|Collection qg_money
     * @property Grid\Column|Collection hc_money
     * @property Grid\Column|Collection play99_money
     * @property Grid\Column|Collection yb_money
     * @property Grid\Column|Collection ly_money
     * @property Grid\Column|Collection kx_money
     * @property Grid\Column|Collection dfw_money
     * @property Grid\Column|Collection xsj_money
     * @property Grid\Column|Collection ld_money
     * @property Grid\Column|Collection ae_money
     * @property Grid\Column|Collection oap_money
     * @property Grid\Column|Collection ia_money
     * @property Grid\Column|Collection sy_money
     * @property Grid\Column|Collection xsbo_money
     * @property Grid\Column|Collection ps_money
     * @property Grid\Column|Collection habaner_money
     * @property Grid\Column|Collection jz_money
     * @property Grid\Column|Collection cmd_money
     * @property Grid\Column|Collection sbtest_money
     * @property Grid\Column|Collection wm_money
     * @property Grid\Column|Collection zeus_money
     * @property Grid\Column|Collection cg_money
     * @property Grid\Column|Collection icg_money
     * @property Grid\Column|Collection pp_money
     * @property Grid\Column|Collection pg_money
     * @property Grid\Column|Collection sg_money
     * @property Grid\Column|Collection vg_money
     * @property Grid\Column|Collection tc_money
     * @property Grid\Column|Collection datqp_money
     * @property Grid\Column|Collection tm_money
     * @property Grid\Column|Collection card_id
     *
     * @method Grid\Column|Collection logintime(string $label = null)
     * @method Grid\Column|Collection id(string $label = null)
     * @method Grid\Column|Collection type(string $label = null)
     * @method Grid\Column|Collection content(string $label = null)
     * @method Grid\Column|Collection apply_count(string $label = null)
     * @method Grid\Column|Collection banner(string $label = null)
     * @method Grid\Column|Collection can_apply(string $label = null)
     * @method Grid\Column|Collection state(string $label = null)
     * @method Grid\Column|Collection created_at(string $label = null)
     * @method Grid\Column|Collection updated_at(string $label = null)
     * @method Grid\Column|Collection activity_id(string $label = null)
     * @method Grid\Column|Collection user_id(string $label = null)
     * @method Grid\Column|Collection check_time(string $label = null)
     * @method Grid\Column|Collection name(string $label = null)
     * @method Grid\Column|Collection version(string $label = null)
     * @method Grid\Column|Collection detail(string $label = null)
     * @method Grid\Column|Collection is_enabled(string $label = null)
     * @method Grid\Column|Collection parent_id(string $label = null)
     * @method Grid\Column|Collection order(string $label = null)
     * @method Grid\Column|Collection icon(string $label = null)
     * @method Grid\Column|Collection uri(string $label = null)
     * @method Grid\Column|Collection extension(string $label = null)
     * @method Grid\Column|Collection show(string $label = null)
     * @method Grid\Column|Collection permission_id(string $label = null)
     * @method Grid\Column|Collection menu_id(string $label = null)
     * @method Grid\Column|Collection slug(string $label = null)
     * @method Grid\Column|Collection http_method(string $label = null)
     * @method Grid\Column|Collection http_path(string $label = null)
     * @method Grid\Column|Collection role_id(string $label = null)
     * @method Grid\Column|Collection value(string $label = null)
     * @method Grid\Column|Collection username(string $label = null)
     * @method Grid\Column|Collection password(string $label = null)
     * @method Grid\Column|Collection avatar(string $label = null)
     * @method Grid\Column|Collection remember_token(string $label = null)
     * @method Grid\Column|Collection apply_info(string $label = null)
     * @method Grid\Column|Collection realperson(string $label = null)
     * @method Grid\Column|Collection electron(string $label = null)
     * @method Grid\Column|Collection joker(string $label = null)
     * @method Grid\Column|Collection sport(string $label = null)
     * @method Grid\Column|Collection fish(string $label = null)
     * @method Grid\Column|Collection lottery(string $label = null)
     * @method Grid\Column|Collection e_sport(string $label = null)
     * @method Grid\Column|Collection member_fs(string $label = null)
     * @method Grid\Column|Collection api_code(string $label = null)
     * @method Grid\Column|Collection api_name(string $label = null)
     * @method Grid\Column|Collection api_money(string $label = null)
     * @method Grid\Column|Collection game_type(string $label = null)
     * @method Grid\Column|Collection plat_type(string $label = null)
     * @method Grid\Column|Collection app_state(string $label = null)
     * @method Grid\Column|Collection cateid(string $label = null)
     * @method Grid\Column|Collection stor(string $label = null)
     * @method Grid\Column|Collection Created_at(string $label = null)
     * @method Grid\Column|Collection Updated_at(string $label = null)
     * @method Grid\Column|Collection code(string $label = null)
     * @method Grid\Column|Collection bank_name(string $label = null)
     * @method Grid\Column|Collection max_amount(string $label = null)
     * @method Grid\Column|Collection bank_img(string $label = null)
     * @method Grid\Column|Collection pic(string $label = null)
     * @method Grid\Column|Collection jump_url(string $label = null)
     * @method Grid\Column|Collection mch_id(string $label = null)
     * @method Grid\Column|Collection key(string $label = null)
     * @method Grid\Column|Collection status(string $label = null)
     * @method Grid\Column|Collection payimg(string $label = null)
     * @method Grid\Column|Collection connection(string $label = null)
     * @method Grid\Column|Collection queue(string $label = null)
     * @method Grid\Column|Collection payload(string $label = null)
     * @method Grid\Column|Collection exception(string $label = null)
     * @method Grid\Column|Collection failed_at(string $label = null)
     * @method Grid\Column|Collection platform_name(string $label = null)
     * @method Grid\Column|Collection name_en(string $label = null)
     * @method Grid\Column|Collection keywords(string $label = null)
     * @method Grid\Column|Collection category_id(string $label = null)
     * @method Grid\Column|Collection order_by(string $label = null)
     * @method Grid\Column|Collection is_hot(string $label = null)
     * @method Grid\Column|Collection is_new(string $label = null)
     * @method Grid\Column|Collection is_recommend(string $label = null)
     * @method Grid\Column|Collection is_pc(string $label = null)
     * @method Grid\Column|Collection is_mobile(string $label = null)
     * @method Grid\Column|Collection site_state(string $label = null)
     * @method Grid\Column|Collection bet_id(string $label = null)
     * @method Grid\Column|Collection bet_time(string $label = null)
     * @method Grid\Column|Collection platform_type(string $label = null)
     * @method Grid\Column|Collection bet_amount(string $label = null)
     * @method Grid\Column|Collection valid_amount(string $label = null)
     * @method Grid\Column|Collection win_loss(string $label = null)
     * @method Grid\Column|Collection is_back(string $label = null)
     * @method Grid\Column|Collection uid(string $label = null)
     * @method Grid\Column|Collection addtime(string $label = null)
     * @method Grid\Column|Collection pid(string $label = null)
     * @method Grid\Column|Collection recnum(string $label = null)
     * @method Grid\Column|Collection rechangenum(string $label = null)
     * @method Grid\Column|Collection totalrechange(string $label = null)
     * @method Grid\Column|Collection withdrawnum(string $label = null)
     * @method Grid\Column|Collection totalwithdraw(string $label = null)
     * @method Grid\Column|Collection betnum(string $label = null)
     * @method Grid\Column|Collection totalbet(string $label = null)
     * @method Grid\Column|Collection totalvalidamount(string $label = null)
     * @method Grid\Column|Collection totalwinloss(string $label = null)
     * @method Grid\Column|Collection redpackectnum(string $label = null)
     * @method Grid\Column|Collection totalredpackect(string $label = null)
     * @method Grid\Column|Collection releasewater(string $label = null)
     * @method Grid\Column|Collection rakeback(string $label = null)
     * @method Grid\Column|Collection rakebacknum(string $label = null)
     * @method Grid\Column|Collection releasewaternum(string $label = null)
     * @method Grid\Column|Collection isagent(string $label = null)
     * @method Grid\Column|Collection vip_id(string $label = null)
     * @method Grid\Column|Collection email(string $label = null)
     * @method Grid\Column|Collection token(string $label = null)
     * @method Grid\Column|Collection bank_id(string $label = null)
     * @method Grid\Column|Collection bank_no(string $label = null)
     * @method Grid\Column|Collection bank_owner(string $label = null)
     * @method Grid\Column|Collection bank_address(string $label = null)
     * @method Grid\Column|Collection info(string $label = null)
     * @method Grid\Column|Collection order_no(string $label = null)
     * @method Grid\Column|Collection out_trade_no(string $label = null)
     * @method Grid\Column|Collection amount(string $label = null)
     * @method Grid\Column|Collection cash_fee(string $label = null)
     * @method Grid\Column|Collection real_money(string $label = null)
     * @method Grid\Column|Collection pay_way(string $label = null)
     * @method Grid\Column|Collection bank(string $label = null)
     * @method Grid\Column|Collection day_flow(string $label = null)
     * @method Grid\Column|Collection recharge(string $label = null)
     * @method Grid\Column|Collection flow_money(string $label = null)
     * @method Grid\Column|Collection money(string $label = null)
     * @method Grid\Column|Collection start_time(string $label = null)
     * @method Grid\Column|Collection end_time(string $label = null)
     * @method Grid\Column|Collection img(string $label = null)
     * @method Grid\Column|Collection memo(string $label = null)
     * @method Grid\Column|Collection client_type(string $label = null)
     * @method Grid\Column|Collection sort(string $label = null)
     * @method Grid\Column|Collection template_id(string $label = null)
     * @method Grid\Column|Collection api_type(string $label = null)
     * @method Grid\Column|Collection transfer_type(string $label = null)
     * @method Grid\Column|Collection before_money(string $label = null)
     * @method Grid\Column|Collection after_money(string $label = null)
     * @method Grid\Column|Collection settlementsday(string $label = null)
     * @method Grid\Column|Collection betid(string $label = null)
     * @method Grid\Column|Collection deleted_at(string $label = null)
     * @method Grid\Column|Collection message_id(string $label = null)
     * @method Grid\Column|Collection login_ua(string $label = null)
     * @method Grid\Column|Collection login_ip(string $label = null)
     * @method Grid\Column|Collection ip_address(string $label = null)
     * @method Grid\Column|Collection desc(string $label = null)
     * @method Grid\Column|Collection vipname(string $label = null)
     * @method Grid\Column|Collection viptype(string $label = null)
     * @method Grid\Column|Collection flow(string $label = null)
     * @method Grid\Column|Collection exp(string $label = null)
     * @method Grid\Column|Collection is_default(string $label = null)
     * @method Grid\Column|Collection vrberfee(string $label = null)
     * @method Grid\Column|Collection ldfee(string $label = null)
     * @method Grid\Column|Collection redpacketid(string $label = null)
     * @method Grid\Column|Collection redpacketfee(string $label = null)
     * @method Grid\Column|Collection redpacketmoney(string $label = null)
     * @method Grid\Column|Collection usetime(string $label = null)
     * @method Grid\Column|Collection isuse(string $label = null)
     * @method Grid\Column|Collection fid(string $label = null)
     * @method Grid\Column|Collection api_token(string $label = null)
     * @method Grid\Column|Collection realname(string $label = null)
     * @method Grid\Column|Collection vip(string $label = null)
     * @method Grid\Column|Collection level(string $label = null)
     * @method Grid\Column|Collection paypwd(string $label = null)
     * @method Grid\Column|Collection isonline(string $label = null)
     * @method Grid\Column|Collection allowagent(string $label = null)
     * @method Grid\Column|Collection balance(string $label = null)
     * @method Grid\Column|Collection mbalance(string $label = null)
     * @method Grid\Column|Collection totalgame(string $label = null)
     * @method Grid\Column|Collection phone(string $label = null)
     * @method Grid\Column|Collection mail(string $label = null)
     * @method Grid\Column|Collection paysum(string $label = null)
     * @method Grid\Column|Collection isdel(string $label = null)
     * @method Grid\Column|Collection isblack(string $label = null)
     * @method Grid\Column|Collection lastip(string $label = null)
     * @method Grid\Column|Collection last_login_ip_address(string $label = null)
     * @method Grid\Column|Collection sourceurl(string $label = null)
     * @method Grid\Column|Collection loginsum(string $label = null)
     * @method Grid\Column|Collection birthday(string $label = null)
     * @method Grid\Column|Collection settlement_id(string $label = null)
     * @method Grid\Column|Collection fanshuifee(string $label = null)
     * @method Grid\Column|Collection settlementday(string $label = null)
     * @method Grid\Column|Collection reg_ip(string $label = null)
     * @method Grid\Column|Collection ag_money(string $label = null)
     * @method Grid\Column|Collection allbet_money(string $label = null)
     * @method Grid\Column|Collection bbin_money(string $label = null)
     * @method Grid\Column|Collection bg_money(string $label = null)
     * @method Grid\Column|Collection og_money(string $label = null)
     * @method Grid\Column|Collection pt_money(string $label = null)
     * @method Grid\Column|Collection gd_money(string $label = null)
     * @method Grid\Column|Collection dg_money(string $label = null)
     * @method Grid\Column|Collection qt_money(string $label = null)
     * @method Grid\Column|Collection ky_money(string $label = null)
     * @method Grid\Column|Collection ig_money(string $label = null)
     * @method Grid\Column|Collection jdb_money(string $label = null)
     * @method Grid\Column|Collection fg_money(string $label = null)
     * @method Grid\Column|Collection avia_money(string $label = null)
     * @method Grid\Column|Collection leg_money(string $label = null)
     * @method Grid\Column|Collection bng_money(string $label = null)
     * @method Grid\Column|Collection dt_money(string $label = null)
     * @method Grid\Column|Collection gg_money(string $label = null)
     * @method Grid\Column|Collection vrbet_money(string $label = null)
     * @method Grid\Column|Collection hlgame_money(string $label = null)
     * @method Grid\Column|Collection hbb_money(string $label = null)
     * @method Grid\Column|Collection qg_money(string $label = null)
     * @method Grid\Column|Collection hc_money(string $label = null)
     * @method Grid\Column|Collection play99_money(string $label = null)
     * @method Grid\Column|Collection yb_money(string $label = null)
     * @method Grid\Column|Collection ly_money(string $label = null)
     * @method Grid\Column|Collection kx_money(string $label = null)
     * @method Grid\Column|Collection dfw_money(string $label = null)
     * @method Grid\Column|Collection xsj_money(string $label = null)
     * @method Grid\Column|Collection ld_money(string $label = null)
     * @method Grid\Column|Collection ae_money(string $label = null)
     * @method Grid\Column|Collection oap_money(string $label = null)
     * @method Grid\Column|Collection ia_money(string $label = null)
     * @method Grid\Column|Collection sy_money(string $label = null)
     * @method Grid\Column|Collection xsbo_money(string $label = null)
     * @method Grid\Column|Collection ps_money(string $label = null)
     * @method Grid\Column|Collection habaner_money(string $label = null)
     * @method Grid\Column|Collection jz_money(string $label = null)
     * @method Grid\Column|Collection cmd_money(string $label = null)
     * @method Grid\Column|Collection sbtest_money(string $label = null)
     * @method Grid\Column|Collection wm_money(string $label = null)
     * @method Grid\Column|Collection zeus_money(string $label = null)
     * @method Grid\Column|Collection cg_money(string $label = null)
     * @method Grid\Column|Collection icg_money(string $label = null)
     * @method Grid\Column|Collection pp_money(string $label = null)
     * @method Grid\Column|Collection pg_money(string $label = null)
     * @method Grid\Column|Collection sg_money(string $label = null)
     * @method Grid\Column|Collection vg_money(string $label = null)
     * @method Grid\Column|Collection tc_money(string $label = null)
     * @method Grid\Column|Collection datqp_money(string $label = null)
     * @method Grid\Column|Collection tm_money(string $label = null)
     * @method Grid\Column|Collection card_id(string $label = null)
     */
    class Grid {}

    class MiniGrid extends Grid {}

    /**
     * @property Show\Field|Collection logintime
     * @property Show\Field|Collection id
     * @property Show\Field|Collection type
     * @property Show\Field|Collection content
     * @property Show\Field|Collection apply_count
     * @property Show\Field|Collection banner
     * @property Show\Field|Collection can_apply
     * @property Show\Field|Collection state
     * @property Show\Field|Collection created_at
     * @property Show\Field|Collection updated_at
     * @property Show\Field|Collection activity_id
     * @property Show\Field|Collection user_id
     * @property Show\Field|Collection check_time
     * @property Show\Field|Collection name
     * @property Show\Field|Collection version
     * @property Show\Field|Collection detail
     * @property Show\Field|Collection is_enabled
     * @property Show\Field|Collection parent_id
     * @property Show\Field|Collection order
     * @property Show\Field|Collection icon
     * @property Show\Field|Collection uri
     * @property Show\Field|Collection extension
     * @property Show\Field|Collection show
     * @property Show\Field|Collection permission_id
     * @property Show\Field|Collection menu_id
     * @property Show\Field|Collection slug
     * @property Show\Field|Collection http_method
     * @property Show\Field|Collection http_path
     * @property Show\Field|Collection role_id
     * @property Show\Field|Collection value
     * @property Show\Field|Collection username
     * @property Show\Field|Collection password
     * @property Show\Field|Collection avatar
     * @property Show\Field|Collection remember_token
     * @property Show\Field|Collection apply_info
     * @property Show\Field|Collection realperson
     * @property Show\Field|Collection electron
     * @property Show\Field|Collection joker
     * @property Show\Field|Collection sport
     * @property Show\Field|Collection fish
     * @property Show\Field|Collection lottery
     * @property Show\Field|Collection e_sport
     * @property Show\Field|Collection member_fs
     * @property Show\Field|Collection api_code
     * @property Show\Field|Collection api_name
     * @property Show\Field|Collection api_money
     * @property Show\Field|Collection game_type
     * @property Show\Field|Collection plat_type
     * @property Show\Field|Collection app_state
     * @property Show\Field|Collection cateid
     * @property Show\Field|Collection stor
     * @property Show\Field|Collection Created_at
     * @property Show\Field|Collection Updated_at
     * @property Show\Field|Collection code
     * @property Show\Field|Collection bank_name
     * @property Show\Field|Collection max_amount
     * @property Show\Field|Collection bank_img
     * @property Show\Field|Collection pic
     * @property Show\Field|Collection jump_url
     * @property Show\Field|Collection mch_id
     * @property Show\Field|Collection key
     * @property Show\Field|Collection status
     * @property Show\Field|Collection payimg
     * @property Show\Field|Collection connection
     * @property Show\Field|Collection queue
     * @property Show\Field|Collection payload
     * @property Show\Field|Collection exception
     * @property Show\Field|Collection failed_at
     * @property Show\Field|Collection platform_name
     * @property Show\Field|Collection name_en
     * @property Show\Field|Collection keywords
     * @property Show\Field|Collection category_id
     * @property Show\Field|Collection order_by
     * @property Show\Field|Collection is_hot
     * @property Show\Field|Collection is_new
     * @property Show\Field|Collection is_recommend
     * @property Show\Field|Collection is_pc
     * @property Show\Field|Collection is_mobile
     * @property Show\Field|Collection site_state
     * @property Show\Field|Collection bet_id
     * @property Show\Field|Collection bet_time
     * @property Show\Field|Collection platform_type
     * @property Show\Field|Collection bet_amount
     * @property Show\Field|Collection valid_amount
     * @property Show\Field|Collection win_loss
     * @property Show\Field|Collection is_back
     * @property Show\Field|Collection uid
     * @property Show\Field|Collection addtime
     * @property Show\Field|Collection pid
     * @property Show\Field|Collection recnum
     * @property Show\Field|Collection rechangenum
     * @property Show\Field|Collection totalrechange
     * @property Show\Field|Collection withdrawnum
     * @property Show\Field|Collection totalwithdraw
     * @property Show\Field|Collection betnum
     * @property Show\Field|Collection totalbet
     * @property Show\Field|Collection totalvalidamount
     * @property Show\Field|Collection totalwinloss
     * @property Show\Field|Collection redpackectnum
     * @property Show\Field|Collection totalredpackect
     * @property Show\Field|Collection releasewater
     * @property Show\Field|Collection rakeback
     * @property Show\Field|Collection rakebacknum
     * @property Show\Field|Collection releasewaternum
     * @property Show\Field|Collection isagent
     * @property Show\Field|Collection vip_id
     * @property Show\Field|Collection email
     * @property Show\Field|Collection token
     * @property Show\Field|Collection bank_id
     * @property Show\Field|Collection bank_no
     * @property Show\Field|Collection bank_owner
     * @property Show\Field|Collection bank_address
     * @property Show\Field|Collection info
     * @property Show\Field|Collection order_no
     * @property Show\Field|Collection out_trade_no
     * @property Show\Field|Collection amount
     * @property Show\Field|Collection cash_fee
     * @property Show\Field|Collection real_money
     * @property Show\Field|Collection pay_way
     * @property Show\Field|Collection bank
     * @property Show\Field|Collection day_flow
     * @property Show\Field|Collection recharge
     * @property Show\Field|Collection flow_money
     * @property Show\Field|Collection money
     * @property Show\Field|Collection start_time
     * @property Show\Field|Collection end_time
     * @property Show\Field|Collection img
     * @property Show\Field|Collection memo
     * @property Show\Field|Collection client_type
     * @property Show\Field|Collection sort
     * @property Show\Field|Collection template_id
     * @property Show\Field|Collection api_type
     * @property Show\Field|Collection transfer_type
     * @property Show\Field|Collection before_money
     * @property Show\Field|Collection after_money
     * @property Show\Field|Collection settlementsday
     * @property Show\Field|Collection betid
     * @property Show\Field|Collection deleted_at
     * @property Show\Field|Collection message_id
     * @property Show\Field|Collection login_ua
     * @property Show\Field|Collection login_ip
     * @property Show\Field|Collection ip_address
     * @property Show\Field|Collection desc
     * @property Show\Field|Collection vipname
     * @property Show\Field|Collection viptype
     * @property Show\Field|Collection flow
     * @property Show\Field|Collection exp
     * @property Show\Field|Collection is_default
     * @property Show\Field|Collection vrberfee
     * @property Show\Field|Collection ldfee
     * @property Show\Field|Collection redpacketid
     * @property Show\Field|Collection redpacketfee
     * @property Show\Field|Collection redpacketmoney
     * @property Show\Field|Collection usetime
     * @property Show\Field|Collection isuse
     * @property Show\Field|Collection fid
     * @property Show\Field|Collection api_token
     * @property Show\Field|Collection realname
     * @property Show\Field|Collection vip
     * @property Show\Field|Collection level
     * @property Show\Field|Collection paypwd
     * @property Show\Field|Collection isonline
     * @property Show\Field|Collection allowagent
     * @property Show\Field|Collection balance
     * @property Show\Field|Collection mbalance
     * @property Show\Field|Collection totalgame
     * @property Show\Field|Collection phone
     * @property Show\Field|Collection mail
     * @property Show\Field|Collection paysum
     * @property Show\Field|Collection isdel
     * @property Show\Field|Collection isblack
     * @property Show\Field|Collection lastip
     * @property Show\Field|Collection last_login_ip_address
     * @property Show\Field|Collection sourceurl
     * @property Show\Field|Collection loginsum
     * @property Show\Field|Collection birthday
     * @property Show\Field|Collection settlement_id
     * @property Show\Field|Collection fanshuifee
     * @property Show\Field|Collection settlementday
     * @property Show\Field|Collection reg_ip
     * @property Show\Field|Collection ag_money
     * @property Show\Field|Collection allbet_money
     * @property Show\Field|Collection bbin_money
     * @property Show\Field|Collection bg_money
     * @property Show\Field|Collection og_money
     * @property Show\Field|Collection pt_money
     * @property Show\Field|Collection gd_money
     * @property Show\Field|Collection dg_money
     * @property Show\Field|Collection qt_money
     * @property Show\Field|Collection ky_money
     * @property Show\Field|Collection ig_money
     * @property Show\Field|Collection jdb_money
     * @property Show\Field|Collection fg_money
     * @property Show\Field|Collection avia_money
     * @property Show\Field|Collection leg_money
     * @property Show\Field|Collection bng_money
     * @property Show\Field|Collection dt_money
     * @property Show\Field|Collection gg_money
     * @property Show\Field|Collection vrbet_money
     * @property Show\Field|Collection hlgame_money
     * @property Show\Field|Collection hbb_money
     * @property Show\Field|Collection qg_money
     * @property Show\Field|Collection hc_money
     * @property Show\Field|Collection play99_money
     * @property Show\Field|Collection yb_money
     * @property Show\Field|Collection ly_money
     * @property Show\Field|Collection kx_money
     * @property Show\Field|Collection dfw_money
     * @property Show\Field|Collection xsj_money
     * @property Show\Field|Collection ld_money
     * @property Show\Field|Collection ae_money
     * @property Show\Field|Collection oap_money
     * @property Show\Field|Collection ia_money
     * @property Show\Field|Collection sy_money
     * @property Show\Field|Collection xsbo_money
     * @property Show\Field|Collection ps_money
     * @property Show\Field|Collection habaner_money
     * @property Show\Field|Collection jz_money
     * @property Show\Field|Collection cmd_money
     * @property Show\Field|Collection sbtest_money
     * @property Show\Field|Collection wm_money
     * @property Show\Field|Collection zeus_money
     * @property Show\Field|Collection cg_money
     * @property Show\Field|Collection icg_money
     * @property Show\Field|Collection pp_money
     * @property Show\Field|Collection pg_money
     * @property Show\Field|Collection sg_money
     * @property Show\Field|Collection vg_money
     * @property Show\Field|Collection tc_money
     * @property Show\Field|Collection datqp_money
     * @property Show\Field|Collection tm_money
     * @property Show\Field|Collection card_id
     *
     * @method Show\Field|Collection logintime(string $label = null)
     * @method Show\Field|Collection id(string $label = null)
     * @method Show\Field|Collection type(string $label = null)
     * @method Show\Field|Collection content(string $label = null)
     * @method Show\Field|Collection apply_count(string $label = null)
     * @method Show\Field|Collection banner(string $label = null)
     * @method Show\Field|Collection can_apply(string $label = null)
     * @method Show\Field|Collection state(string $label = null)
     * @method Show\Field|Collection created_at(string $label = null)
     * @method Show\Field|Collection updated_at(string $label = null)
     * @method Show\Field|Collection activity_id(string $label = null)
     * @method Show\Field|Collection user_id(string $label = null)
     * @method Show\Field|Collection check_time(string $label = null)
     * @method Show\Field|Collection name(string $label = null)
     * @method Show\Field|Collection version(string $label = null)
     * @method Show\Field|Collection detail(string $label = null)
     * @method Show\Field|Collection is_enabled(string $label = null)
     * @method Show\Field|Collection parent_id(string $label = null)
     * @method Show\Field|Collection order(string $label = null)
     * @method Show\Field|Collection icon(string $label = null)
     * @method Show\Field|Collection uri(string $label = null)
     * @method Show\Field|Collection extension(string $label = null)
     * @method Show\Field|Collection show(string $label = null)
     * @method Show\Field|Collection permission_id(string $label = null)
     * @method Show\Field|Collection menu_id(string $label = null)
     * @method Show\Field|Collection slug(string $label = null)
     * @method Show\Field|Collection http_method(string $label = null)
     * @method Show\Field|Collection http_path(string $label = null)
     * @method Show\Field|Collection role_id(string $label = null)
     * @method Show\Field|Collection value(string $label = null)
     * @method Show\Field|Collection username(string $label = null)
     * @method Show\Field|Collection password(string $label = null)
     * @method Show\Field|Collection avatar(string $label = null)
     * @method Show\Field|Collection remember_token(string $label = null)
     * @method Show\Field|Collection apply_info(string $label = null)
     * @method Show\Field|Collection realperson(string $label = null)
     * @method Show\Field|Collection electron(string $label = null)
     * @method Show\Field|Collection joker(string $label = null)
     * @method Show\Field|Collection sport(string $label = null)
     * @method Show\Field|Collection fish(string $label = null)
     * @method Show\Field|Collection lottery(string $label = null)
     * @method Show\Field|Collection e_sport(string $label = null)
     * @method Show\Field|Collection member_fs(string $label = null)
     * @method Show\Field|Collection api_code(string $label = null)
     * @method Show\Field|Collection api_name(string $label = null)
     * @method Show\Field|Collection api_money(string $label = null)
     * @method Show\Field|Collection game_type(string $label = null)
     * @method Show\Field|Collection plat_type(string $label = null)
     * @method Show\Field|Collection app_state(string $label = null)
     * @method Show\Field|Collection cateid(string $label = null)
     * @method Show\Field|Collection stor(string $label = null)
     * @method Show\Field|Collection Created_at(string $label = null)
     * @method Show\Field|Collection Updated_at(string $label = null)
     * @method Show\Field|Collection code(string $label = null)
     * @method Show\Field|Collection bank_name(string $label = null)
     * @method Show\Field|Collection max_amount(string $label = null)
     * @method Show\Field|Collection bank_img(string $label = null)
     * @method Show\Field|Collection pic(string $label = null)
     * @method Show\Field|Collection jump_url(string $label = null)
     * @method Show\Field|Collection mch_id(string $label = null)
     * @method Show\Field|Collection key(string $label = null)
     * @method Show\Field|Collection status(string $label = null)
     * @method Show\Field|Collection payimg(string $label = null)
     * @method Show\Field|Collection connection(string $label = null)
     * @method Show\Field|Collection queue(string $label = null)
     * @method Show\Field|Collection payload(string $label = null)
     * @method Show\Field|Collection exception(string $label = null)
     * @method Show\Field|Collection failed_at(string $label = null)
     * @method Show\Field|Collection platform_name(string $label = null)
     * @method Show\Field|Collection name_en(string $label = null)
     * @method Show\Field|Collection keywords(string $label = null)
     * @method Show\Field|Collection category_id(string $label = null)
     * @method Show\Field|Collection order_by(string $label = null)
     * @method Show\Field|Collection is_hot(string $label = null)
     * @method Show\Field|Collection is_new(string $label = null)
     * @method Show\Field|Collection is_recommend(string $label = null)
     * @method Show\Field|Collection is_pc(string $label = null)
     * @method Show\Field|Collection is_mobile(string $label = null)
     * @method Show\Field|Collection site_state(string $label = null)
     * @method Show\Field|Collection bet_id(string $label = null)
     * @method Show\Field|Collection bet_time(string $label = null)
     * @method Show\Field|Collection platform_type(string $label = null)
     * @method Show\Field|Collection bet_amount(string $label = null)
     * @method Show\Field|Collection valid_amount(string $label = null)
     * @method Show\Field|Collection win_loss(string $label = null)
     * @method Show\Field|Collection is_back(string $label = null)
     * @method Show\Field|Collection uid(string $label = null)
     * @method Show\Field|Collection addtime(string $label = null)
     * @method Show\Field|Collection pid(string $label = null)
     * @method Show\Field|Collection recnum(string $label = null)
     * @method Show\Field|Collection rechangenum(string $label = null)
     * @method Show\Field|Collection totalrechange(string $label = null)
     * @method Show\Field|Collection withdrawnum(string $label = null)
     * @method Show\Field|Collection totalwithdraw(string $label = null)
     * @method Show\Field|Collection betnum(string $label = null)
     * @method Show\Field|Collection totalbet(string $label = null)
     * @method Show\Field|Collection totalvalidamount(string $label = null)
     * @method Show\Field|Collection totalwinloss(string $label = null)
     * @method Show\Field|Collection redpackectnum(string $label = null)
     * @method Show\Field|Collection totalredpackect(string $label = null)
     * @method Show\Field|Collection releasewater(string $label = null)
     * @method Show\Field|Collection rakeback(string $label = null)
     * @method Show\Field|Collection rakebacknum(string $label = null)
     * @method Show\Field|Collection releasewaternum(string $label = null)
     * @method Show\Field|Collection isagent(string $label = null)
     * @method Show\Field|Collection vip_id(string $label = null)
     * @method Show\Field|Collection email(string $label = null)
     * @method Show\Field|Collection token(string $label = null)
     * @method Show\Field|Collection bank_id(string $label = null)
     * @method Show\Field|Collection bank_no(string $label = null)
     * @method Show\Field|Collection bank_owner(string $label = null)
     * @method Show\Field|Collection bank_address(string $label = null)
     * @method Show\Field|Collection info(string $label = null)
     * @method Show\Field|Collection order_no(string $label = null)
     * @method Show\Field|Collection out_trade_no(string $label = null)
     * @method Show\Field|Collection amount(string $label = null)
     * @method Show\Field|Collection cash_fee(string $label = null)
     * @method Show\Field|Collection real_money(string $label = null)
     * @method Show\Field|Collection pay_way(string $label = null)
     * @method Show\Field|Collection bank(string $label = null)
     * @method Show\Field|Collection day_flow(string $label = null)
     * @method Show\Field|Collection recharge(string $label = null)
     * @method Show\Field|Collection flow_money(string $label = null)
     * @method Show\Field|Collection money(string $label = null)
     * @method Show\Field|Collection start_time(string $label = null)
     * @method Show\Field|Collection end_time(string $label = null)
     * @method Show\Field|Collection img(string $label = null)
     * @method Show\Field|Collection memo(string $label = null)
     * @method Show\Field|Collection client_type(string $label = null)
     * @method Show\Field|Collection sort(string $label = null)
     * @method Show\Field|Collection template_id(string $label = null)
     * @method Show\Field|Collection api_type(string $label = null)
     * @method Show\Field|Collection transfer_type(string $label = null)
     * @method Show\Field|Collection before_money(string $label = null)
     * @method Show\Field|Collection after_money(string $label = null)
     * @method Show\Field|Collection settlementsday(string $label = null)
     * @method Show\Field|Collection betid(string $label = null)
     * @method Show\Field|Collection deleted_at(string $label = null)
     * @method Show\Field|Collection message_id(string $label = null)
     * @method Show\Field|Collection login_ua(string $label = null)
     * @method Show\Field|Collection login_ip(string $label = null)
     * @method Show\Field|Collection ip_address(string $label = null)
     * @method Show\Field|Collection desc(string $label = null)
     * @method Show\Field|Collection vipname(string $label = null)
     * @method Show\Field|Collection viptype(string $label = null)
     * @method Show\Field|Collection flow(string $label = null)
     * @method Show\Field|Collection exp(string $label = null)
     * @method Show\Field|Collection is_default(string $label = null)
     * @method Show\Field|Collection vrberfee(string $label = null)
     * @method Show\Field|Collection ldfee(string $label = null)
     * @method Show\Field|Collection redpacketid(string $label = null)
     * @method Show\Field|Collection redpacketfee(string $label = null)
     * @method Show\Field|Collection redpacketmoney(string $label = null)
     * @method Show\Field|Collection usetime(string $label = null)
     * @method Show\Field|Collection isuse(string $label = null)
     * @method Show\Field|Collection fid(string $label = null)
     * @method Show\Field|Collection api_token(string $label = null)
     * @method Show\Field|Collection realname(string $label = null)
     * @method Show\Field|Collection vip(string $label = null)
     * @method Show\Field|Collection level(string $label = null)
     * @method Show\Field|Collection paypwd(string $label = null)
     * @method Show\Field|Collection isonline(string $label = null)
     * @method Show\Field|Collection allowagent(string $label = null)
     * @method Show\Field|Collection balance(string $label = null)
     * @method Show\Field|Collection mbalance(string $label = null)
     * @method Show\Field|Collection totalgame(string $label = null)
     * @method Show\Field|Collection phone(string $label = null)
     * @method Show\Field|Collection mail(string $label = null)
     * @method Show\Field|Collection paysum(string $label = null)
     * @method Show\Field|Collection isdel(string $label = null)
     * @method Show\Field|Collection isblack(string $label = null)
     * @method Show\Field|Collection lastip(string $label = null)
     * @method Show\Field|Collection last_login_ip_address(string $label = null)
     * @method Show\Field|Collection sourceurl(string $label = null)
     * @method Show\Field|Collection loginsum(string $label = null)
     * @method Show\Field|Collection birthday(string $label = null)
     * @method Show\Field|Collection settlement_id(string $label = null)
     * @method Show\Field|Collection fanshuifee(string $label = null)
     * @method Show\Field|Collection settlementday(string $label = null)
     * @method Show\Field|Collection reg_ip(string $label = null)
     * @method Show\Field|Collection ag_money(string $label = null)
     * @method Show\Field|Collection allbet_money(string $label = null)
     * @method Show\Field|Collection bbin_money(string $label = null)
     * @method Show\Field|Collection bg_money(string $label = null)
     * @method Show\Field|Collection og_money(string $label = null)
     * @method Show\Field|Collection pt_money(string $label = null)
     * @method Show\Field|Collection gd_money(string $label = null)
     * @method Show\Field|Collection dg_money(string $label = null)
     * @method Show\Field|Collection qt_money(string $label = null)
     * @method Show\Field|Collection ky_money(string $label = null)
     * @method Show\Field|Collection ig_money(string $label = null)
     * @method Show\Field|Collection jdb_money(string $label = null)
     * @method Show\Field|Collection fg_money(string $label = null)
     * @method Show\Field|Collection avia_money(string $label = null)
     * @method Show\Field|Collection leg_money(string $label = null)
     * @method Show\Field|Collection bng_money(string $label = null)
     * @method Show\Field|Collection dt_money(string $label = null)
     * @method Show\Field|Collection gg_money(string $label = null)
     * @method Show\Field|Collection vrbet_money(string $label = null)
     * @method Show\Field|Collection hlgame_money(string $label = null)
     * @method Show\Field|Collection hbb_money(string $label = null)
     * @method Show\Field|Collection qg_money(string $label = null)
     * @method Show\Field|Collection hc_money(string $label = null)
     * @method Show\Field|Collection play99_money(string $label = null)
     * @method Show\Field|Collection yb_money(string $label = null)
     * @method Show\Field|Collection ly_money(string $label = null)
     * @method Show\Field|Collection kx_money(string $label = null)
     * @method Show\Field|Collection dfw_money(string $label = null)
     * @method Show\Field|Collection xsj_money(string $label = null)
     * @method Show\Field|Collection ld_money(string $label = null)
     * @method Show\Field|Collection ae_money(string $label = null)
     * @method Show\Field|Collection oap_money(string $label = null)
     * @method Show\Field|Collection ia_money(string $label = null)
     * @method Show\Field|Collection sy_money(string $label = null)
     * @method Show\Field|Collection xsbo_money(string $label = null)
     * @method Show\Field|Collection ps_money(string $label = null)
     * @method Show\Field|Collection habaner_money(string $label = null)
     * @method Show\Field|Collection jz_money(string $label = null)
     * @method Show\Field|Collection cmd_money(string $label = null)
     * @method Show\Field|Collection sbtest_money(string $label = null)
     * @method Show\Field|Collection wm_money(string $label = null)
     * @method Show\Field|Collection zeus_money(string $label = null)
     * @method Show\Field|Collection cg_money(string $label = null)
     * @method Show\Field|Collection icg_money(string $label = null)
     * @method Show\Field|Collection pp_money(string $label = null)
     * @method Show\Field|Collection pg_money(string $label = null)
     * @method Show\Field|Collection sg_money(string $label = null)
     * @method Show\Field|Collection vg_money(string $label = null)
     * @method Show\Field|Collection tc_money(string $label = null)
     * @method Show\Field|Collection datqp_money(string $label = null)
     * @method Show\Field|Collection tm_money(string $label = null)
     * @method Show\Field|Collection card_id(string $label = null)
     */
    class Show {}

    /**
     
     */
    class Form {}

}

namespace Dcat\Admin\Grid {
    /**
     
     */
    class Column {}

    /**
     
     */
    class Filter {}
}

namespace Dcat\Admin\Show {
    /**
     
     */
    class Field {}
}
