<?php
    /* 论坛 公共数据API */
    
    list($uid, $json) = app_check('x');
    
    if (isset($json['classify'])) {
        $list = sql_query('SELECT ' . sql_fieldsExcept('forum', ['postIP']) . ' FROM forum WHERE classify = ? AND type = 1 ORDER BY id DESC', [intval($json['classify'])]);
        foreach ($list as $k => $v) {
            $list[$k]['content'] = app_staticcs_resolve($v['content']);
            $list[$k]['coverImg'] = app_staticcs_resolve($v['coverImg']);
            $list[$k]['looks'] = app_getLooks('forum', $v['id']);
            $list[$k]['likes'] = app_getLikes('forum', $v['id']);
            $list[$k]['replys'] = sql_query_count('SELECT id FROM forum WHERE pid = ?', [$v['id']]);
            $list[$k]['liked'] = app_isLiked('forum', $v['id'], $uid);
            $list[$k]['udata'] = app_getUserData_mini($v['uid']);
        }
    }
    $data['classifies'] = c::$FORUM_CONFIG['classifies'];
    $data['adminClassifies'] = c::$FORUM_CONFIG['adminClassifyIds'];
    $data['defaultClassify'] = c::$FORUM_CONFIG['defaultClassifyId'];
    $data['adminUids'] = app_getAdminTable(false, 100);
    
    api_callback(1, '', ['forumData' => $data, 'topicList' => $list]);
?>