<div class="filter-input col-sm-<?php echo e($width, false); ?> "  style="<?php echo $style; ?>">
    <div class="form-group" >
        <div class="input-group input-group-sm">
            <div class="input-group-prepend">
                <span class="input-group-text text-capitalize bg-white"><b><?php echo $label; ?></b></span>
            </div>

            <input type="text" class="form-control" placeholder="<?php echo e($label, false); ?>" name="<?php echo e($name['start'], false); ?>" value="<?php echo e(request($name['start'], \Illuminate\Support\Arr::get($value, 'start')), false); ?>">
            <span class="input-group-addon" style="border-left: 0; border-right: 0;">To</span>
            <input type="text" class="form-control" placeholder="<?php echo e($label, false); ?>" name="<?php echo e($name['end'], false); ?>" value="<?php echo e(request($name['end'], \Illuminate\Support\Arr::get($value, 'end')), false); ?>">
        </div>
    </div>
</div><?php /**PATH D:\www\bob\admin\vendor\dcat\laravel-admin\src/../resources/views/filter/between.blade.php ENDPATH**/ ?>