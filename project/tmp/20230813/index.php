<?php
    /* 临时文件上传API */
    
    list($uid, $json) = app_check('sa', ['fname']);
    
    // basename 防路径穿越
    $fname = basename('' . $json['fname']);
    $expect = 'user/tmp/20230813/' . $fname;
    if ($json['ossKey']) {
        // OSS 直传模式：对象已上传，仅校验 key 与对象存在性
        if ($json['ossKey'] !== $expect || ! staticcs_verify_uploaded($json['ossKey'], false)) {
            api_callback(0, '上传失败了呢~');
        }
    } else {
        api_callback(0, '请使用 OSS 直传上传文件呢~');
    }
    sql_exec('INSERT INTO tmp_20230813 (name, uploadTime) VALUES (?, ?) ON DUPLICATE KEY UPDATE uploadTime = VALUES(uploadTime)', [$fname, time_microtime()]);
    api_callback(1, '');
?>