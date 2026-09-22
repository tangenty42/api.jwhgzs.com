<?php
    /* 临时文件上传API */
    
    list($uid, $json, $file) = app_check('sa', ['fname']);
    
    // basename 防路径穿越
    $fname = basename('' . $json['fname']);
    $expect = 'user/tmp/20230813/' . $fname;
    if ($json['ossKey']) {
        // OSS 直传模式：对象已上传，校验 key 与大小
        if ($json['ossKey'] !== $expect || ! staticcs_verify_uploaded($json['ossKey'])) {
            api_callback(0, '上传失败了呢~');
        }
    } else {
        if (! $file) {
            api_callback(0, '你的文件呢？');
        }
        staticcs_upload($file['tmp_name'], $expect);
    }
    sql_exec('INSERT INTO tmp_20230813 (name, uploadTime) VALUES (?, ?) ON DUPLICATE KEY UPDATE uploadTime = VALUES(uploadTime)', [$fname, time_microtime()]);
    api_callback(1, '');
?>