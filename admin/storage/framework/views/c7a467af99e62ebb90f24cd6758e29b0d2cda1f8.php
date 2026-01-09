<div class="<?php echo e($viewClass['form-group'], false); ?>" >

    <label class="<?php echo e($viewClass['label'], false); ?> control-label pt-0"><?php echo $label; ?></label>

    <div class="<?php echo e($viewClass['field'], false); ?>">

        <?php if($checkAll): ?>
            <?php echo $checkAll; ?>

            <hr style="margin-top: 10px;margin-bottom: 0;">
        <?php endif; ?>

        <?php echo $__env->make('admin::form.error', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $checkbox; ?>


        <input type="hidden" name="<?php echo e($name, false); ?>[]">

        <?php echo $__env->make('admin::form.help-block', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </div>
</div>

<?php if(! empty($canCheckAll)): ?>
<script init="[name='_check_all_']" once>
    $this.on('change', function () {
        $(this).parents('.form-field').find('input[type="checkbox"]:not(:first)').prop('checked', this.checked).trigger('change');
    });
</script>
<?php endif; ?>

<?php if(! empty($loads)): ?>
<script once>
    var selector = '<?php echo $selector; ?>',
        fields = '<?php echo $loads['fields']; ?>'.split('^'),
        urls = '<?php echo $loads['urls']; ?>'.split('^');

    $(document).off('change', selector);
    $(document).on('change', selector, function () {
        var values = [];

        $(selector+':checked').each(function () {
            if (String(this.value) === '0' || this.value) {
                values.push(this.value)
            }
        });

        Dcat.helpers.loadFields(this, {
            group: '.fields-group',
            urls: urls,
            fields: fields,
            textField: "<?php echo e($loads['textField'], false); ?>",
            idField: "<?php echo e($loads['idField'], false); ?>",
            values: values,
        });
    });
    $(selector+':checked').trigger('change')
</script>
<?php endif; ?><?php /**PATH D:\www\aiyou\admin\vendor\dcat\laravel-admin\src/../resources/views/form/checkbox.blade.php ENDPATH**/ ?>