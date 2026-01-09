<?php

namespace App\Helpers;

/**
 * HTML内容清理工具类
 */
class HtmlCleaner
{
    /**
     * 允许的HTML标签
     */
    private static $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'del',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li', 'dl', 'dt', 'dd',
        'img', 'table', 'tr', 'td', 'th', 'thead', 'tbody', 'tfoot',
        'blockquote', 'pre', 'code', 'hr',
        'div', 'span', 'a'
    ];

    /**
     * 允许的HTML属性
     */
    private static $allowedAttributes = [
        'src', 'alt', 'title', 'width', 'height',
        'href', 'target', 'rel',
        'class', 'id', 'style',
        'colspan', 'rowspan', 'align', 'valign',
        'border', 'cellpadding', 'cellspacing'
    ];

    /**
     * 清理HTML内容
     *
     * @param string|null $html
     * @return string
     */
    public static function clean($html)
    {
        if (empty($html)) {
            return '';
        }

        // 确保内容是UTF-8编码
        if (!mb_check_encoding($html, 'UTF-8')) {
            $html = mb_convert_encoding($html, 'UTF-8', 'auto');
        }

        // 清理HTML实体
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 移除BOM标记
        $html = str_replace("\xEF\xBB\xBF", '', $html);

        // 移除危险的标签和属性
        $html = self::removeDangerousTags($html);
        $html = self::removeDangerousAttributes($html);

        // 清理空白字符
        $html = self::cleanWhitespace($html);

        return $html;
    }

    /**
     * 移除危险的HTML标签
     *
     * @param string $html
     * @return string
     */
    private static function removeDangerousTags($html)
    {
        // 移除script标签
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/is', '', $html);

        // 移除iframe标签
        $html = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/is', '', $html);

        // 移除object标签
        $html = preg_replace('/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/is', '', $html);

        // 移除embed标签
        $html = preg_replace('/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/is', '', $html);

        // 移除form标签
        $html = preg_replace('/<form\b[^<]*(?:(?!<\/form>)<[^<]*)*<\/form>/is', '', $html);

        // 移除input标签
        $html = preg_replace('/<input\b[^<]*>/is', '', $html);

        // 移除button标签
        $html = preg_replace('/<button\b[^<]*(?:(?!<\/button>)<[^<]*)*<\/button>/is', '', $html);

        return $html;
    }

    /**
     * 移除危险的HTML属性
     *
     * @param string $html
     * @return string
     */
    private static function removeDangerousAttributes($html)
    {
        // 移除on*事件属性
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/is', '', $html);

        // 移除javascript:协议
        $html = preg_replace('/javascript\s*:/is', '', $html);

        // 移除data:协议（可能包含恶意代码）
        $html = preg_replace('/data\s*:\s*text\/html/is', '', $html);

        return $html;
    }

    /**
     * 清理空白字符
     *
     * @param string $html
     * @return string
     */
    private static function cleanWhitespace($html)
    {
        // 移除多余的空白字符
        $html = preg_replace('/\s+/', ' ', $html);

        // 清理HTML标签间的空白
        $html = preg_replace('/>\s+</', '><', $html);

        return trim($html);
    }

    /**
     * 获取清理后的纯文本内容
     *
     * @param string|null $html
     * @return string
     */
    public static function getText($html)
    {
        if (empty($html)) {
            return '';
        }

        // 先清理HTML
        $cleanHtml = self::clean($html);

        // 移除所有HTML标签
        $text = strip_tags($cleanHtml);

        // 解码HTML实体
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 清理空白字符
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}
