<?php
    /* 新宁空间-珍贵档案 删除外号/梗API */
    
    list($uid, $json) = app_check('vs', [], 100);
    
    if (! sql_exec_count('DELETE FROM xnzx_student_PA WHERE id = ?', [$json['id']])) {
        api_callback(0, '操作数据失败了呢~');
    }
    
    api_callback(1, '');
?>