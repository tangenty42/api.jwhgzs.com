<?php
    /* 首页轮播数据同步API */
    
    list($uid, $json) = app_check('x');
    
    $carouselList = sql_query('SELECT ' . sql_fieldsExcept('forum', ['content', 'postIP']) . ' FROM forum WHERE classify = ?', [c::$FORUM_CONFIG['carouselClassifyId']]);
    foreach ($carouselList as $k => $v) {
        $carouselList[$k]['coverImg'] = app_staticcs_resolve($v['coverImg']);
    }
    
    api_callback(1, '', ['carouselList' => $carouselList]);
?>