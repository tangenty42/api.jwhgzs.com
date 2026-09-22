<?php
    /* 新宁空间-珍贵档案 照片删除API */
    
    list($uid, $json) = app_check('sv');
    
    $tname = $json['isClass'] ? 'xnzx_class_table' : 'xnzx_student_table';
    
    $dir = u('static_user://PA') . '/' . $json['id'];
    // 无扩展名的为旧数据，补 .jpg
    $fname = (strpos($json['name'], '.') === false ? $json['name'] . '.jpg' : $json['name']);
    $result1 = staticcs_del($dir . '/' . $fname);
    
    if (! $json['isClass'])
        $d = sql_query1('SELECT id, PA_photosName FROM ' . $tname . ' WHERE id = ?', [$json['id']]);
    else {
        $ids = explode('_', $json['id']);
        $d = sql_query1('SELECT id, PA_photosName FROM ' . $tname . ' WHERE year = ? AND class = ?', [$ids[0], $ids[1]]);
    }
    $names = ($d['PA_photosName'] ? explode(',', $d['PA_photosName']) : []);
    foreach ($names as $k => $v) {
        if (strtolower($v) == strtolower($json['name'])) {
            unset($names[$k]);
            break;
        }
    }
    $names = implode(',', $names);
    if (! $json['isClass'])
        $result2 = sql_exec_count('UPDATE ' . $tname . ' SET PA_photosName = ?, PA_photosVersion = PA_photosVersion + 1 WHERE id = ?', [$names, $json['id']]);
    else {
        $result2 = sql_exec_count('UPDATE ' . $tname . ' SET PA_photosName = ?, PA_photosVersion = PA_photosVersion + 1 WHERE year = ? AND class = ?', [$names, $ids[0], $ids[1]]);
    }
    
    if ($result1 && $result2) {
        api_callback(1, '');
    } else {
        api_callback(0, '操作失败了呢~');
    }
?>