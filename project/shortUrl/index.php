<?php
    /* 短链接系统 跳转数据获取API */
    
    $data = sql_query1('SELECT * FROM shortUrl WHERE tag = ? LIMIT 1', [$_GET['tag']]);
    if ($data)
        $url = u($data['url']);
    else
        $url = u('shortUrlPortal://');
    
    header('Location: ' . $url);
?>