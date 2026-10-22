<?php
    /* 用户数据设置API */
    
    list($uid, $json) = app_check('a', ['key']);
    if (! in_array($json['key'], c::$USER_EDITABLESQLKEYS)) {
        api_callback(0, '这项数据不能编辑哦~');
    }
    if ($json['key'] == 'pass') {
        if (! $json['value']) {
            api_callback(0, '密码不能为空哦~');
        }
        $pass_min = c::$USERINF_LENGTH['pass_min'];
        $pass_max = c::$USERINF_LENGTH['pass_max'];
        if (! (strlen($json['value']) >= $pass_min && strlen($json['value']) <= $pass_max)) {
            api_callback(0, '密码长度必须在' . $pass_min . '~' . $pass_max . '之间~');
        }
    }
    
    if ($json['key'] == 'pass') {
        // 改密防爆破：失败速率限制（rateLimit 表）+ 提交人机验证
        app_rateLimit_guard('setPass', $uid, 5, 600000, '', false, 10);
        if (c::$VAPTCHA_CONFIG['status'] && ! vaptcha_verify($json['vaptchaData'])) {
            api_callback(0, '人机验证不通过哦~');
        }
        $fail = function ($msg) use ($uid) {
            app_rateLimit_hit('setPass', $uid, false);
            api_callback(0, $msg);
        };
        $udata = sql_query1('SELECT pass, phone FROM user WHERE id = ?', [$uid]);
        $oldPass = $udata['pass'];
        // 改密需二次校验：短信验证码 或 原密码，任一通过即可（M-04）
        $verified = false;
        if (isset($json['oldPass']) && $json['oldPass'] !== '') {
            if (! app_verifyUserPassword($json['oldPass'], $oldPass)) {
                $fail('原密码不正确哦~');
            }
            $verified = true;
        } elseif (isset($json['phoneVerify']) && $json['phoneVerify'] !== '') {
            // 短信必须发往当前账号绑定的手机号，防止用他人手机号绕过
            if (intval($json['phone']) !== intval($udata['phone'])) {
                $fail('手机号与当前账号绑定的不一致哦~');
            }
            $phoneVerify = app_verifyPhone($json['phone'], $json['phoneVerify']);
            if (! $phoneVerify['status']) {
                $fail(app_sms_error_message($phoneVerify, 'check'));
            }
            app_setPhoneVerifyUsed(intval($phoneVerify['id']), 'setPass');
            $verified = true;
        }
        if (! $verified) {
            api_callback(0, '请通过短信验证码或原密码验证身份哦~');
        }
        if (app_verifyUserPassword($json['value'], $oldPass)) {
            api_callback(0, '你的密码还是跟原来的一模一样啦~');
        }
        $json['value'] = password_hash($json['value'], PASSWORD_DEFAULT);
    }
    if (! sql_exec_count('UPDATE user SET ' . $json['key'] . ' = ? WHERE id = ?', [$json['value'], $uid])) {
        api_callback(0, '操作数据失败了呢~');
    }
    if ($json['key'] == 'pass') {
        app_rateLimit_hit('setPass', $uid, true);
        // 改密后吊销其它会话，保留当前设备（M-04）
        sql_exec('UPDATE userToken SET token = \'\' WHERE userId = ? AND BINARY token <> ?', [$uid, $json['userToken']]);
    }
    
    api_callback(1, '');
?>