<?php
    /* 用户注册API */
    
    list($uid, $json, $file, $pvr) = app_check('xvpa', ['name', 'pass', 'phone', 'phoneVerify'], 1, false, 'signup');
    
    $name_min = c::$USERINF_LENGTH['name_min'];
    $name_max = c::$USERINF_LENGTH['name_max'];
    $pass_min = c::$USERINF_LENGTH['pass_min'];
    $pass_max = c::$USERINF_LENGTH['pass_max'];
    if (! (mb_strlen($json['name']) >= $name_min && mb_strlen($json['name']) <= $name_max)) {
        api_callback(0, '用户名长度必须在' . $name_min . '~' . $name_max . '之间~');
    }
    if (! (mb_strlen($json['pass']) >= $pass_min && mb_strlen($json['pass']) <= $pass_max)) {
        api_callback(0, '密码长度必须在' . $pass_min . '~' . $pass_max . '之间~');
    }
    if (! preg_match('/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u', $json['name'])) {
        api_callback(0, '用户名只能包含汉字/字母/数字/下划线哦~');
    }
    $samePhoneCount = sql_query_count('SELECT id FROM user WHERE phone = ?', [intval($json['phone'])]);
    if ($samePhoneCount !== 0) {
        app_rateLimit_hit('signup', 0, false, $json['phone']);
        api_callback(0, '你的手机号已经注册了九尾狐账号了~');
    }
    $sameNameCount = sql_query_count('SELECT id FROM user WHERE name = ?', [$json['name']]);
    if ($sameNameCount !== 0) {
        app_rateLimit_hit('signup', 0, false, $json['phone']);
        api_callback(0, '你的用户名已经有人用了哦~');
    }
    
    $result = sql_exec_count('INSERT INTO user (id, name, pass, phone, signupTime, signupIP, signupUA) VALUES (?, ?, ?, ?, ?, ?, ?)', [sql_newId('user'), $json['name'], password_hash($json['pass'], PASSWORD_DEFAULT), $json['phone'], time_microtime(), app_getUserIP(), app_getUserUA()]);
    if (! $result) {
        api_callback(0, '数据操作失败~');
    }
    app_setPhoneVerifyUsed($pvr, 'signup');
    app_rateLimit_hit('signup', 0, true, $json['phone']);
    
    api_callback(1, '');
?>