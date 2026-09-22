<?php
    /* 新宁空间-珍贵档案 照片上传API */
    
    list($uid, $json, $file) = app_check('s');
    
    $tname = $json['isClass'] ? 'xnzx_class_table' : 'xnzx_student_table';
    
    if ($json['ossKey']) {
        // OSS 直传模式：对象已上传，校验 key 归属与大小
        $prefix = 'user/PA/' . $json['id'] . '/';
        if (strpos('' . $json['ossKey'], $prefix) !== 0 || ! preg_match('#^[^/]+\\.(jpg|jpeg|png|gif|webp)$#iu', substr($json['ossKey'], strlen($prefix)))) {
            api_callback(0, '操作失败了呢~');
        }
        if (! staticcs_verify_uploaded($json['ossKey'])) {
            api_callback(0, '上传图片失败！~');
        }
        $name = substr($json['ossKey'], strlen($prefix));
        $result1 = true;
    } else {
        if (! $file) {
            api_callback(0, '你的文件呢？');
        }
        $oriUrl = $file['tmp_name'];
        $imgUrl = $file['tmp_name'] . '_formatted';
        if (! image_toJpeg($oriUrl, $imgUrl)) {
            api_callback(0, '你上传的图片损坏了呢~（也有可能是格式不标准哦）');
        }
        
        $dir = u('static_user://PA') . '/' . $json['id'];
        if ($json['mode'] == 0) $name = md5(time_microtime());
        elseif ($json['mode'] == 1) $name = '0';
        $result1 = staticcs_upload($imgUrl, $dir . '/' . $name . '.jpg');
        unlink($oriUrl);
        unlink($imgUrl);
    }
    if ($result1) {
        if (! $json['isClass'])
            $d = sql_query1('SELECT id, PA_photosName FROM ' . $tname . ' WHERE id = ?', [$json['id']]);
        else {
            $ids = explode('_', $json['id']);
            $d = sql_query1('SELECT id, PA_photosName FROM ' . $tname . ' WHERE year = ? AND class = ?', [$ids[0], $ids[1]]);
        }
        $names = ($d['PA_photosName'] . '' ? explode(',', $d['PA_photosName']) : []);
        if ($json['mode'] == 1) {
            // 更换头像：清理旧头像条目；旧对象仅在与新对象不同名时删除
            $cur = (strpos($name, '.') === false ? $name . '.jpg' : $name);
            foreach ($names as $k => $v) {
                if ($v == '0' || strpos($v, '0.') === 0) {
                    $old = (strpos($v, '.') === false ? $v . '.jpg' : $v);
                    if ($old != $cur)
                        staticcs_del(u('static_user://PA') . '/' . $json['id'] . '/' . $old);
                    unset($names[$k]);
                }
            }
        }
        $names[] = $name;
        $names = implode(',', $names);
        if (! $json['isClass'])
            $result2 = sql_exec_count('UPDATE ' . $tname . ' SET PA_photosName = ?, PA_photosVersion = PA_photosVersion + 1 WHERE id = ?', [$names, $json['id']]);
        else
            $result2 = sql_exec_count('UPDATE ' . $tname . ' SET PA_photosName = ?, PA_photosVersion = PA_photosVersion + 1 WHERE year = ? AND class = ?', [$names, $ids[0], $ids[1]]);
    }
    
    if ($result1 && $result2) {
        api_callback(1, '');
    } else {
        api_callback(0, '操作失败了呢~');
    }
?>