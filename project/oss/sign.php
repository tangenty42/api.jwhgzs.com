<?php
    /* OSS 直传预签名API */
    
    // mime 允许为空（tmp 模块任意类型），各模块自行校验
    list($uid, $json) = app_check('xa', ['module', 'size']);
    
    if (! $uid) {
        api_callback(0, '你还没登录呢~');
    }
    $size = intval($json['size']);
    if ($size <= 0 || $size > c::$UPLOAD_SIZELIMIT) {
        api_callback(0, '文件大小超过限制了呢~');
    }
    $extMap = [
        'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif',
        'image/webp' => 'webp', 'image/svg+xml' => 'svg', 'video/mp4' => 'mp4'
    ];
    $mime = strtolower(trim('' . $json['mime']));
    // tmp 模块允许任意类型，MIME 无法识别时不参与签名
    if ($json['module'] == 'tmp20230813') {
        if (! isset($extMap[$mime])) $mime = '';
    } else {
        if (! isset($extMap[$mime])) {
            api_callback(0, '不支持的文件类型呢~');
        }
    }
    $ext = isset($extMap[$mime]) ? $extMap[$mime] : '';
    
    switch ('' . $json['module']) {
        case 'avatar':
            $key = 'user/avatar/' . $uid . '.' . $ext;
            break;
        case 'forum':
            $key = 'user/forum/' . text_random() . '.' . $ext;
            break;
        case 'PA':
            app_check('a', ['id']);
            if (! app_getAdminLevel($uid)) {
                api_callback(0, '你的权限不足哦！~');
            }
            if (! preg_match('#^\d+(_\d+)?$#', '' . $json['id'])) {
                api_callback(0, '参数错误呢~');
            }
            $name = ($json['mode'] == 1 ? '0' : md5(time_microtime()));
            $key = 'user/PA/' . $json['id'] . '/' . $name . '.' . $ext;
            break;
        case 'weekly':
            app_check('a', ['id', 'title', 'type']);
            $pid = ($json['type'] == 'upload' ? intval($json['id']) : intval(sql_query1('SELECT parentId FROM xnzx_weekly_article WHERE id = ?', [intval($json['id'])])['parentId']));
            $title = preg_replace('#[/\\\\]#u', '_', '' . $json['title']);
            $key = 'user/xnzx_weekly/pid' . $pid . '_' . $title . '_' . time_microtime() . '.' . $ext;
            break;
        case 'tmp20230813':
            if (! app_getAdminLevel($uid)) {
                api_callback(0, '你的权限不足哦！~');
            }
            // 内联校验（app_check 不带 x 会要求登录）
            if (! isset($json['fname']) || $json['fname'] === '') {
                api_callback(0, '没有填完信息呢~');
            }
            $fname = basename('' . $json['fname']);
            if (! $fname) {
                api_callback(0, '参数错误呢~');
            }
            $key = 'user/tmp/20230813/' . $fname;
            break;
        default:
            api_callback(0, '未知模块呢~');
    }
    
    api_callback(1, '', [
        'uploadUrl' => staticcs_presign($key, $mime),
        'key' => $key,
        'url' => u('static://') . staticcs_key($key)
    ]);
?>
