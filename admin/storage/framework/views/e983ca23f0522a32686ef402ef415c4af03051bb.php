<div class="<?php echo e($viewClass['form-group'], false); ?>">

    <label class="<?php echo e($viewClass['label'], false); ?> control-label"><?php echo $label; ?></label>

    <div class="<?php echo e($viewClass['field'], false); ?>">

        <?php echo $__env->make('admin::form.error', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="row" style="max-width: 603px">
            <div class="col-md-6" style="margin-right: 0">
                <div class="input-group">
                    <span class="input-group-prepend">
                        <span class="input-group-text bg-white"><i class="feather icon-calendar"></i></span>
                    </span>
                    <input autocomplete="off" type="text" name="<?php echo e($name['start'], false); ?>" value="<?php echo e($value['start'] ?? null, false); ?>" class="form-control <?php echo e($class['start'], false); ?>" style="width:180px" <?php echo $attributes; ?> />
                </div>
            </div>

            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-prepend">
                        <span class="input-group-text bg-white"><i class="feather icon-calendar"></i></span>
                    </span>
                    <input autocomplete="off" type="text" name="<?php echo e($name['end'], false); ?>" value="<?php echo e($value['end'] ?? null, false); ?>" class="form-control <?php echo e($class['end'], false); ?>" style="width: 180px" <?php echo $attributes; ?> />
                </div>
            </div>
        </div>

        <?php echo $__env->make('admin::form.help-block', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </div>
</div>

<script require="@moment,@bootstrap-datetimepicker" init="<?php echo $selector['start']; ?>">
    var options = <?php echo admin_javascript_json($options); ?>;
    var $end = $('<?php echo $selector['end']; ?>');

    $this.datetimepicker(options);
    $end.datetimepicker($.extend(options, {useCurrent: false}));
    $this.on("dp.change", function (e) {
        $end.data("DateTimePicker").minDate(e.date);
    });
    $end.on("dp.change", function (e) {
        $this.data("DateTimePicker").maxDate(e.date);
    });
</script>

<?php /**PATH F:\www\aiyou\admin\vendor\dcat\laravel-admin\src/../resources/views/form/datetimerange.blade.php ENDPATH**/ ?>