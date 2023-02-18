<?php

namespace Basics\Blade\Concerns;

trait CompilesDump
{
    /**
     * Compile the dumpVars statement into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileDumpVars($expression)
    {
        return implode("\n", [
            // styled dump
            '<div class="row">',
            "    <?php foreach (get_defined_vars() as \$_key => \$_value) { ?>",
            '        <div class="col-12 col-md-4">',
            '            <div class="card">',
            '                <div class="card-header">',
            '                    <div class="card-title">',
            "                        \$<?php echo \$_key; ?>",
            "                        <?php if (is_object(\$_value)) { ?>",
            "                            <span class=\"ml-2\">(<?php echo get_class(\$_value); ?>)</span>",
            '                        <?php } ?>',
            '                    </div>',
            '                </div>',
            '                <div class="card-body">',
            "                    <?php if (\$_value instanceof \Illuminate\View\ComponentAttributeBag) { ?>",
            "                        <textarea readonly rows=\"3\" class=\"w-100\"><?php echo json_encode(reset(\$_value)); ?></textarea>",
            '                    <?php } else { ?>',
            "                        <textarea readonly rows=\"2\" class=\"w-100\"><?php echo json_encode(\$_value); ?></textarea>",
            '                    <?php } ?>',
            '                </div>',
            '            </div>',
            '        </div>',
            '    <?php } ?>',
            '</div>',
            // cleans context
            "<?php unset(\$_key, \$_value); ?>",
        ]);
    }
}
