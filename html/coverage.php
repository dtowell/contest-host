<?php
xdebug_start_code_coverage(XDEBUG_CC_UNUSED|XDEBUG_CC_DEAD_CODE);
register_shutdown_function(function(){
    $new_coverage = xdebug_get_code_coverage();
    $old_coverage = unserialize(@file_get_contents("coverage"));
    foreach ($new_coverage as $file=>$lines)
        foreach ($lines as $line=>$flag)
            if ($flag > ($old_coverage[$file][$line]??-3))
                $old_coverage[$file][$line] = $flag;
    file_put_contents("coverage",serialize($old_coverage));
});