<?php
    /* 强制设备下线API */
    
    list($uid, $json) = app_check();
    
    if (! sql_exec_count('UPDATE userToken SET token = \'\' WHERE id = ? AND userId = ? LIMIT 1', [intval($json['id']), $uid])) {
        api_callback(0, '操作数据失败~');
    }
    
    api_callback(1, '');
?>