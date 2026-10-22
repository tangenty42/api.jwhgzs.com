<?php
    /* 后台管理-短链接管理 修改API */
    
    list($uid, $json) = app_check('sa', ['url'], 100);
    
    if (! $json['tag']) $json['tag'] = text_random(6);
    
    if (sql_query_count('SELECT id FROM shortUrl WHERE tag = ? AND id <> ? LIMIT 1', [$json['tag'], $json['id']])) {
        api_callback(0, '这个短链标签已经存在了哦~！');
    }
    if (! sql_exec_count('UPDATE shortUrl SET tag = ?, url = ?, note = ? WHERE id = ?', [$json['tag'], $json['url'], $json['note'], $json['id']])) {
        api_callback(0, '操作数据库失败，或者数据没有改哦~');
    }
    
    api_callback(1, '');
?>