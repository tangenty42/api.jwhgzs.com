<?php
    /* 后台管理-短链接管理 添加API */
    
    list($uid, $json) = app_check('sa', ['url'], 100);
    
    if (! $json['tag']) $json['tag'] = text_random(6);
    
    if (sql_query_count('SELECT id FROM shortUrl WHERE tag = ? LIMIT 1', [$json['tag']])) {
        api_callback(0, '这个短链标签已经存在了哦~！');
    }
    if (! sql_exec_count('INSERT INTO shortUrl (id, tag, url, note) VALUES (?, ?, ?, ?)', [sql_newId('shortUrl'), $json['tag'], $json['url'], $json['note']])) {
        api_callback(0, '操作数据库失败了呢~');
    }
    
    api_callback(1, '');
?>