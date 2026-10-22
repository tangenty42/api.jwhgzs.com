<?php
    /* 发送手机验证码API（注册/找回密码） */
    
    list($uid, $json) = app_check('xva', ['phone']);
    // 发送即占额度：同一手机号每分钟 1 条、同一 IP 每分钟 3 条（另有每日总量限制）
    app_rateLimit_guard('smsSend', 0, 1, 60000, $json['phone'], true, 3);
    if (! (intval($json['phone']) && strlen($json['phone']) == 11)) {
        api_callback(0, '你的手机号不对劲呢~');
    }
    
    $count = sql_query_count('SELECT id FROM phoneVerify WHERE (phone = ? OR userIP = ?) AND sendTime >= ?', [intval($json['phone']), app_getUserIP(), strtotime(date('Y-m-d')) * 1000]);
    $limit = c::$SMS_VERIFY_CONFIG['sendLimitPerDay'];
    if ($count >= $limit) {
        api_callback(0, '你今天发送验证码太多次（已经' . $count . '次啦）了呢~');
    }
    
    $phoneVerifyId = sql_newId('phoneVerify');
    $smsResult = sms_send_verify_code($json['phone'], $phoneVerifyId);
    if (! $smsResult['status']) {
        app_rateLimit_hit('smsSend', 0, false, $json['phone']);
        api_callback(0, app_sms_error_message($smsResult, 'send'));
    }
    $result = sql_exec_count('INSERT INTO phoneVerify (id, phone, verifyCode, sendTime, userIP, userUA) VALUES (?, ?, ?, ?, ?, ?)', [$phoneVerifyId, intval($json['phone']), 0, time_microtime(), app_getUserIP(), app_getUserUA()]);
    if (! $result) {
        api_callback(0, '操作数据失败~');
    }
    app_rateLimit_hit('smsSend', 0, true, $json['phone']);
    
    api_callback(1, '发送手机验证码成功！请注意查收，你每天只能发送' . $limit . '次验证码哦~');
?>