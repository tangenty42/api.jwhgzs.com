<?php
    /* 临时文件列表API */
    
    list($uid, $json, $file) = app_check('x');
    
    // 以数据表为准（上传时间取自上传动作，历史数据为迁移时导入）
    $list = sql_query('SELECT name, uploadTime AS time FROM tmp_20230813 ORDER BY uploadTime DESC');
    foreach ($list as $k => $v) {
        $list[$k]['url'] = u('static://user/tmp/20230813') . '/' . $v['name'];
    }
    
    api_callback(1, '', ['fileList' => $list]);
?>