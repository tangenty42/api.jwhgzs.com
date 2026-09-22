<?php
    /* 用户头像上传API */
    
    list($uid, $json, $file) = app_check('v');
    
    if ($json['ossKey']) {
        // OSS 直传模式：对象已上传，校验后仅更新数据
        if (! preg_match('#^user/avatar/' . $uid . '\\.(jpg|jpeg|png|gif|webp)$#iu', '' . $json['ossKey'], $m)) {
            api_callback(0, '操作失败了呢~');
        }
        if (! staticcs_verify_uploaded($json['ossKey'])) {
            api_callback(0, '上传图片失败！~');
        }
        $ext = strtolower($m[1]);
        $oldExt = sql_query1('SELECT avatarExt FROM user WHERE id = ?', [$uid])['avatarExt'];
        $result1 = true;
        $result2 = sql_exec_count('UPDATE user SET avatarVersion = avatarVersion + 1, avatarExt = ? WHERE id = ?', [$ext, $uid]);
        if ($result2 && $oldExt && $oldExt != $ext) {
            staticcs_del('user/avatar/' . $uid . '.' . $oldExt);
        }
    } else {
        if (! $file) {
            api_callback(0, '你的文件呢？');
        }
        $oriUrl = $file['tmp_name'];
        $imgUrl = $file['tmp_name'] . '_formatted';
        if (! image_toJpeg($oriUrl, $imgUrl)) {
            api_callback(0, '你上传的图片损坏了呢~（也有可能是格式不标准哦）');
        }
        
        $result1 = staticcs_upload($imgUrl, u('static_user://avatar') . '/' . $uid . '.jpg');
        if ($result1)
            $result2 = sql_exec_count('UPDATE user SET avatarVersion = avatarVersion + 1, avatarExt = \'jpg\' WHERE id = ?', [$uid]);
        
        unlink($oriUrl);
        unlink($imgUrl);
    }
    if ($result1 && $result2) {
        api_callback(1, '');
    } else {
        api_callback(0, '操作失败了呢~');
    }
?>