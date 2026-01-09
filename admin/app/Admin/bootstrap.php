<?php

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Form;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Show;
use Dcat\Admin\Layout\Navbar;
/**
 * Dcat-admin - admin builder based on Laravel.
 * @author jqh <https://github.com/jqhph>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 *
 * extend custom field:
 * Dcat\Admin\Form::extend('php', PHPEditor::class);
 * Dcat\Admin\Grid\Column::extend('php', PHPEditor::class);
 * Dcat\Admin\Grid\Filter::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */


Admin::navbar(function (Navbar $navbar) {
    $navbar->right(view('admin.alert'));
});
Form::resolving(function (Form $form) {

    $form->disableEditingCheck();

    $form->disableCreatingCheck();

    $form->disableViewCheck();
    $form->disableDeleteButton();

});
// 添加代理树样式
Admin::style(
    <<<CSS
.toggle-btn {
    cursor: pointer;
    color: #666;
    transition: all 0.3s;
    display: inline-block;
    width: 16px;
    text-align: center;
    margin-right: 10px;
}

.toggle-btn:hover {
    color: #333;
    background: #f0f0f0;
    border-radius: 2px;
}

.toggle-btn.expanded {
    transform: rotate(90deg);
}

.toggle-btn.expanded i {
    color: #d2b79c;
}

.toggle-placeholder {
    width: 16px;
    margin-right: 10px;
    display: inline-block;
}

.agent-tree-content {
    max-height: 600px;
    overflow-y: auto;
    padding: 10px;
}

.agent-item {
    margin: 8px 0;
}

.agent-node {
    display: flex;
    align-items: center;
    padding: 12px;
    background: #f8f9fa;
    border: 1px solid #e8e8e8;
    border-radius: 4px;
    transition: all 0.3s;
}

.agent-node:hover {
    background: #f0f0f0;
}

.agent-info {
    flex: 1;
}

.agent-name {
    font-weight: 500;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.agent-stats {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.stat-item {
    background: white;
    padding: 3px 8px;
    border-radius: 3px;
    border: 1px solid #d9d9d9;
    font-size: 11px;
    color: #666;
    white-space: nowrap;
}

.agent-children {
    margin-left: 30px;
    padding-left: 15px;
    border-left: 2px solid #e8e8e8;
}

.loading, .no-data {
    padding: 20px;
    text-align: center;
    color: #999;
}

.badge {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 3px;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-info {
    background-color: #17a2b8;
    color: white;
}

/* 盈利为正数的样式 */
.profit-positive {
    color: #f5222d;
    font-weight: bold;
}

/* 盈利为负数的样式 */
.profit-negative {
    color: #52c41a;
    font-weight: bold;
}
CSS
);
Admin::script(
    <<<JS
// 代理树功能 - 修复动态加载事件绑定
console.log('代理树JavaScript已加载');

// 主处理函数
function handleToggleClick(e) {
    e.preventDefault();
    e.stopPropagation();
    
    var \$btn = $(this);
    var agentId = \$btn.data('id');
    var \$children = $('#agent-children-' + agentId);

    console.log('点击代理ID:', agentId, '子容器:', \$children.length);
    
    if (\$btn.hasClass('expanded')) {
        // 收起
        \$btn.removeClass('expanded');
        \$btn.find('i').removeClass('fa-caret-down').addClass('fa-caret-right');
        \$children.hide();
    } else {
        // 展开
        \$btn.addClass('expanded');
        \$btn.find('i').removeClass('fa-caret-right').addClass('fa-caret-down');
        
        // 如果子容器为空，则加载数据
        if (\$children.children().length === 0) {
            console.log('加载子级数据:', agentId);
            \$children.html('<div class="loading"><i class="fa fa-spinner fa-spin"></i> 加载中...</div>');

            var url = window.AdminConfig.baseUrl + agentId;
            
            \$.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    console.log('子级数据加载成功:', agentId);
                    \$children.html(data);
                    
                    // 关键：为新加载的内容重新绑定事件
                    setTimeout(function() {
                        bindEventsToNewContent(\$children);
                    }, 50);
                },
                error: function(xhr, status, error) {
                    console.error('加载失败:', error);
                    \$children.html('<div class="no-data">加载失败</div>');
                }
            });
        } else {
            console.log('子级数据已存在:', agentId);
        }
        
        \$children.show();
    }
}

// 为新加载的内容绑定事件
function bindEventsToNewContent(\$container) {
    console.log('为新内容绑定事件');
    
    // 找到容器内所有的toggle-btn并绑定事件
    \$container.find('.toggle-btn').off('click.agentTree').on('click.agentTree', handleToggleClick);
    
    console.log('新绑定事件元素数量:', \$container.find('.toggle-btn').length);
}

// 全局事件委托 - 确保所有动态加载的元素都能响应
function initGlobalEventDelegation() {
    console.log('初始化全局事件委托');
    
    // 使用命名空间避免重复绑定
    $(document).off('click.agentTree', '.toggle-btn').on('click.agentTree', '.toggle-btn', handleToggleClick);
}

// 模态框打开时加载数据
$(document).on('show.bs.modal', '.modal', function() {
    console.log('模态框打开');
    
    var \$modal = $(this);
    var \$container = \$modal.find('.agent-tree-container');
    
    window.AdminConfig = {
        baseUrl: \$container.data('parent_url')
    };    
    
    if (\$container.length > 0) {
        var url = \$container.data('url');
        if (url) {
            \$container.html('<div class="loading"><i class="fa fa-spinner fa-spin"></i> 加载中...</div>');
            
            \$.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    \$container.html(data);
                    console.log('初始数据加载成功');
                    
                    // 初始化全局事件委托
                    initGlobalEventDelegation();
                    
                    // 为已存在的元素绑定事件
                    setTimeout(function() {
                        $('.toggle-btn').off('click.agentTree').on('click.agentTree', handleToggleClick);
                        console.log('初始事件绑定完成，元素数量:', $('.toggle-btn').length);
                    }, 100);
                },
                error: function(xhr, status, error) {
                    console.error('初始数据加载失败:', error);
                    \$container.html('<div class="no-data">数据加载失败</div>');
                }
            });
        }
    }
});

// 页面加载完成后初始化全局事件
$(document).ready(function() {
    console.log('页面加载完成，初始化代理树');
    initGlobalEventDelegation();
});

// 调试函数
window.debugAgentTree = function() {
    console.log('=== 代理树调试信息 ===');
    console.log('全局toggle-btn数量:', $('.toggle-btn').length);
    console.log('模态框内toggle-btn数量:', $('.modal .toggle-btn').length);
    
    $('.toggle-btn').each(function(i) {
        var \$btn = $(this);
        var agentId = \$btn.data('id');
        var hasClickHandler = $._data(\$btn[0], 'events') && $._data(\$btn[0], 'events').click;
        console.log('按钮' + i + ': ID=' + agentId + ', 事件绑定=' + (hasClickHandler ? '是' : '否'));
    });
};
JS
);