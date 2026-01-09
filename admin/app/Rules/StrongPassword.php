<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 强密码验证规则
 * 
 * 要求密码包含：
 * - 至少8个字符
 * - 至少一个大写字母
 * - 至少一个小写字母
 * - 至少一个数字
 * - 至少一个特殊字符
 */
class StrongPassword implements Rule
{
    /**
     * 确定验证规则是否通过
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // 检查密码长度
        if (strlen($value) < 8) {
            return false;
        }

        // 检查是否包含大写字母
        if (!preg_match('/[A-Z]/', $value)) {
            return false;
        }

        // 检查是否包含小写字母
        if (!preg_match('/[a-z]/', $value)) {
            return false;
        }

        // 检查是否包含数字
        if (!preg_match('/[0-9]/', $value)) {
            return false;
        }

        // 检查是否包含特殊字符
        if (!preg_match('/[^A-Za-z0-9]/', $value)) {
            return false;
        }

        return true;
    }

    /**
     * 获取验证错误消息
     *
     * @return string
     */
    public function message()
    {
        return '密码必须至少8个字符，包含大写字母、小写字母、数字和特殊字符';
    }
}
