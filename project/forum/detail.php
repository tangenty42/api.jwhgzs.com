<?php
    /* 论坛 详细页数据API */
    
    list($uid, $json) = app_check('x');
    
    $replyList = sql_query('SELECT ' . sql_fieldsExcept('forum', ['postIP']) . ' FROM forum WHERE id = ? OR pid = ?', [intval($json['id']), intval($json['id'])]);
    $data = [];
    foreach ($replyList as $k => $v) {
        $data[$k] = $v;
        $data[$k]['_id'] = $k;
        $data[$k]['looks'] = app_getLooks('forum', $v['id']);
        $data[$k]['likes'] = app_getLikes('forum', $v['id']);
        $data[$k]['liked'] = app_isLiked('forum', $v['id'], $uid);
        $data[$k]['udata'] = app_getUserData_mini($v['uid']);
    }
    // 除了主帖子（id最先的），其他帖子倒序
    // 2026/7/3：祖宗之法，大可变！！
    // $tmp = [];
    // foreach ($data as $k => $v) {
    //     if ($k == 0) continue;
    //     $tmp[] = $v;
    // }
    // rsort($tmp);
    // $data = array_merge([$data[0]], $tmp);
    
    $forumData['adminUids'] = app_getAdminTable(false, 100);
    
    if ($uid !== null)
        app_look('forum', $data[0]['id'], $uid);
    
    api_callback(1, '', ['forumData' => $forumData, 'topicTree' => $data]);
?>