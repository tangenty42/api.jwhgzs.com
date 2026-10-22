<?php
    /* 用户登录API */
    
    list($uid, $json) = app_check('xva', ['name', 'pass']);
    app_rateLimit_guard('login', 0, 5, 600000, $json['name'], false, 10);
    $fail = function ($msg) use ($json) {
        app_rateLimit_hit('login', 0, false, $json['name']);
        api_callback(0, $msg);
    };

    $data = sql_query1('SELECT * FROM user WHERE name = ?', [$json['name']]);
    if (! $data) {
        $fail('用户不存在哦~');
    }
    if (! app_verifyUserPassword($json['pass'], $data['pass'])) {
        $fail('密码错了呢~');
    }
    // 存量明文或哈希算法需升级时，登录成功即自动转为哈希
    if (app_passwordNeedsUpgrade($data['pass'])) {
        sql_exec('UPDATE user SET pass = ? WHERE id = ?', [password_hash($json['pass'], PASSWORD_DEFAULT), $data['id']]);
    }
    
    $token = app_genUserToken(intval($data['id']));
    if (! $token) {
        api_callback(0, '数据操作失败了~');
    }
    app_rateLimit_hit('login', 0, true, $json['name']);
    
    api_callback(1, '', ['userToken' => $token]);
?>