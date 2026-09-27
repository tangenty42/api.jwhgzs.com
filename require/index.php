<?php

    error_reporting(0);
    
    /* 公共处理 */
    // 跨域检测，参考：https://www.gxlcms.com/PHPjiqiao-375366.html
    /*
    $__origin = $_SERVER['HTTP_ORIGIN'];
    $__origin_domain = text_url2host(isset($__origin) ? $__origin : '');
    if ($__origin_domain && __parseDomain($__origin_domain)) {
        header('Access-Control-Allow-Origin: ' . $__origin);
    }
    */
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    // 正则表达式长度限制解除，参考：https://blog.csdn.net/leafgw/article/details/50381298
    ini_set('pcre.backtrack_limit', -1);
    
    /* 函数集 */
    function i($dir = '') {
        global $__model_url;
        require_once $__model_url . '/' . $dir;
    }
    function u($dir = '') {
        $table = c::$URLS_TABLE;
        $tmp = explode('://', $dir);
        $res = $table[$tmp[0]];
        $arr = explode('/', $tmp[1]);
        $result = $res[0];
        
        if (! $res)
            return $dir;
        
        foreach ($arr as $v) {
            $res = $res[$v];
            if (is_array($res) && $res[0]) {
                $result .= $res[0];
            }
            elseif ($res && (! is_array($res))) {
                $result .= $res;
            }
            elseif (! $res) {
                $result .= '/' . $v;
            }
        }
        
        return $result;
    }
    
    function text_format_phone($phone = '', $hide = false) {
        $phone .= '';
        if (strlen($phone) != 11) return '未知手机号';
        if (! $hide)
            return substr($phone, 0, 3) . ' ' . substr($phone, 3, - 4) . ' ' . substr($phone, - 4);
        return substr($phone, 0, 3) . ' **** ' . substr($phone, - 4);
    }
    function text_format_IP($ip = '', $hide = false) {
        $arr = explode('.', $ip);
        if (count($arr) != 4) return '未知IP';
        if (! $hide) return $ip;
        return $arr[0] . '.*.' . $arr[2] . '.' . $arr[3];
    }
    function text_random($dig = 16, $md5mode = false) {
        $str = $md5mode ? 'abcdef0123456789' : 'abcdefghijklmnopqrstuvwxyz0123456789';
        $res = '';
        for ($i = 0; $i < $dig; $i ++) {
            $res .= $str[rand(0, strlen($str) - 1)];
        }
        return $res;
    }
    function text_parseData2SQL_add($data) {
        $keys = ['id'];
        $values = [null];
        $values2 = ['?'];
        foreach ($data as $k => $v) {
            $keys[] = $k;
            $values[] = $v;
            $values2[] = '?';
        }
        return ['(' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values2) . ')', $values];
    }
    function text_parseData2SQL_edit($json = []) {
        $res = [];
        $values = [];
        foreach ($json['data'] as $k => $v) {
            $res[] = $k . ' = ?';
            $values[] = $v;
        }
        $values[] = $json['id'];
        return [implode(', ', $res) . ' WHERE id = ? LIMIT 1', $values];
    }
    function time_microtime() {
        // 这个命名错了哈（应为 毫秒），但是已经用在老多地方了，就懒得改了，有这意思就行
        return intval(microtime(true) * 1000);
    }
    /* 因为国际化（多语言）原因，时间戳 UI 化统一在前端进行 */
    /*
    function time_date_desc($ms) {
        if (date('Y', $ms / 1000) == date('Y') && date('m', $ms / 1000) == date('m')) {
            $d = date('d', $ms / 1000);
            $cd = date('d');
            if ($d == $cd)
                return '今天';
            elseif ($d == $cd - 1)
                return '昨天';
            elseif ($d == $cd + 1)
                return '明天';
        }
        return date('Y/m/d', $ms / 1000);
    }
    function time_desc($ms = 0, $withoutS = true) {
        if (! $ms)
            return '';
        return time_date_desc($ms) . date(' H:i' . ($withoutS ? '' : ':s'), $ms / 1000);
    }
    */
    function arr_sortPart($arr = [], $key = '', $isDESC = false) {
        $arr2 = [];
        // 拆成 键=>值 形式
        foreach ($arr as $k => $v) {
            $arr2[$k] = $v[$key];
        }
        // 用 值 来排序，使右边的 值 的顺序对了（注意要保留键值配对）
        if (! $isDESC)
            asort($arr2);
        else
            arsort($arr2);
        
        // 读出正确的 键 的顺序
        $res = [];
        foreach ($arr2 as $k => $v) {
            $res[] = $k;
        }
        
        return $res;
    }
    function arr_sort($arr = [], $key = '', $isDESC = false, $key2 = '', $isDESC2 = false) {
        // 妈的，这个东东搞了我20分钟（主要是以为有简便方法，于是各种查）
        
        // 按 主键 排序
        $_res = arr_sortPart($arr, $key, $isDESC);
        // 末尾必须加一项（多一次foreach），不然最后一项无法处理
        $_res[] = null;
        
        //  主值 相同的，用 副值 排序
        $res = $tmp = [];
        $sameVal = $arr[0][$key];
        foreach ($_res as $v) {
            // 这里比较必须得强类型比较。不然0 == null，就过了。这个问题坑了老久
            if ($arr[$v][$key] !== $sameVal) {
                $tmp = arr_sortPart($tmp, $key2, $isDESC2);
                foreach ($tmp as $v2) {
                    $res[] = $arr[$v2];
                }
                
                if ($v === null) break;
                $tmp = [];
                $tmp[$v] = $arr[$v];
                $sameVal = $arr[$v][$key];
            }
            else {
                $tmp[$v] = $arr[$v];
            }
        }
        
        return $res;
    }
    
    function image_toJpeg($from_path = '', $to_path = '') {
        $img = imagecreatefromjpeg($from_path);
        if (! $img)
            $img = imagecreatefrompng($from_path);
        if (! $img)
            $img = imagecreatefromgif($from_path);
        if (! $img)
            return false;
        imagejpeg($img, $to_path);
        return true;
    }
    
    $_sql_pdo = new PDO('mysql:host=' . s::$SQL_CONFIG['host'] . ';dbname=' . s::$SQL_CONFIG['dbname'], s::$SQL_CONFIG['user'], s::$SQL_CONFIG['pass']);
    // 注意加了这个才能在sql执行错误时抛出Exception
    $_sql_pdo -> setAttribute(PDO :: ATTR_ERRMODE, PDO :: ERRMODE_EXCEPTION);
    function sql_exec($sql = '', $param = []) {
        try {
            global $_sql_pdo;
            $psm = $_sql_pdo -> prepare($sql);
            $psm -> execute($param);
            return $psm;
        }
        catch (Exception $ex) {
            api_callback(0, '操作数据库失败了呢~' /* . $ex -> getMessage() */ );
        }
    }
    function sql_exec_count($sql = '', $param = []) {
        return sql_exec($sql, $param) -> rowCount();
    }
    function sql_query($sql = '', $param = []) {
        // 不输出含num => value（只有key => value）形式的结果数组的秘密在这：(PDO :: FETCH_ASSOC)参数。
        return sql_exec($sql, $param) -> fetchAll(PDO :: FETCH_ASSOC);
    }
    function sql_query1($sql = '', $param = []) {
        return sql_exec($sql, $param) -> fetch(PDO :: FETCH_ASSOC);
    }
    function sql_query_count($sql = '', $param = []) {
        // TNND被坑了，columnCount拿的是列数，就是字段名数，不是行数。脑塞了，一开始竟然用columnCount取了行数
        return count(sql_exec($sql, $param) -> fetchAll());
    }
    function sql_newId($table = '', $key = 'id', $firstval = 1) {
        return sql_query1('SELECT MAX(' . $key . ') FROM ' . $table)['MAX(' . $key . ')'] + $firstval;
    }
    function sql_fieldsExcept($table = '', $exceptFields = []) {
        // 查询表的所有字段名，参考：https://www.cnblogs.com/TTonly/p/12132651.html
        $result = sql_query('SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA LIKE ? AND TABLE_NAME LIKE ? ORDER BY ORDINAL_POSITION', [s::$SQL_CONFIG['dbname'], $table]);
        $return = [];
        foreach ($result as $k => $v) {
            $n = $v['COLUMN_NAME'];
            if (! in_array($n, $exceptFields)) {
                $return[] = $n;
            }
        }
        return implode(', ', $return);
    }
    function sql_errInfo($psm = null) {
        return $psm -> errorInfo();
    }
    
    function http($url = '', $data = null, $header = [], $cookie = [], $auto2JSON = true) {
        /* 来源：https://www.cnblogs.com/dadiaomengmei/p/11447689.html */
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (! empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        if (! empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        if (! empty($cookie)) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        
        if ($auto2JSON) {
            $json = json_decode($result, true);
            if ($json)
                return $json;
        }
        return $result;
    }
    function http_json($url = '', $dataArray = [], $header = [], $cookie = []) {
        $jsonHeader = [
            'Content-type: application/json; charset=\'utf-8\'',
            'Accept: application/json'
        ];
        return http($url, json_encode($dataArray), array_merge($jsonHeader, $header), $cookie);
    }
    function http_prosi($url = '', $data = [], $header = [], $cookie = []) {
        echo(http($url, $data, $header, $cookie, false));
    }
    function http_locate() {
        return 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    
    function api_input($isGET = false) {
        if (! $isGET)
            return json_decode($_POST['json'], true);
        else
            return $_GET;
    }
    function api_inputFile() {
        return $_FILES['file'];
    }
    function api_callback($status = 1, $msg = '', $data = null) {
        header('Content-type: application/json;');
        exit(json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]));
    }
    function api_admin($table_prefix = '', $admin_level = 1) {
        list($uid, $json) = app_check('as', ['action', 'table'], $admin_level);
        $json['table'] = $table_prefix . $json['table'];
        if ($json['action'] == 'add') {
            app_check('a', ['data']);
            list($sql, $params) = text_parseData2SQL_add($json['data']);
            sql_exec('INSERT INTO ' . $json['table'] . ' ' . $sql, $params);
        }
        elseif ($json['action'] == 'delete') {
            app_check('a', ['id']);
            sql_exec('DELETE FROM ' . $json['table'] . ' WHERE id = ?', [$json['id']]);
        }
        elseif ($json['action'] == 'edit') {
            app_check('a', ['id', 'data']);
            list($sql, $params) = text_parseData2SQL_edit($json);
            sql_exec('UPDATE ' . $json['table'] . ' SET ' . $sql, $params);
        }
        api_callback(1, '');
    }
    
    function app_getUserIP() {
        return $_SERVER['REMOTE_ADDR'];
    }
    function app_getUserUA() {
        return $_SERVER['HTTP_USER_AGENT'];
    }
    function app_genUserToken($uid = 0) {
        $token = text_random();
        if (! sql_exec_count('INSERT INTO userToken (id, userId, token, loginTime, loginIP, loginUA) VALUES (?, ?, ?, ?, ?, ?)', [sql_newId('userToken'), $uid, $token, time_microtime(), app_getUserIP(), app_getUserUA()])) {
            return null;
        }
        return $token;
    }
    function app_verifyUserToken($token = '', $verifyIsActive = true, $verifyIsMe = true) {
        if (! $token) return false;
        if ($verifyIsMe) {
            // loginTime + exp >= time, 故loginTime >= time - exp
            $result = sql_query1('SELECT userId FROM userToken WHERE BINARY token LIKE ? AND loginTime >= ? AND loginIP LIKE ? AND loginUA LIKE ?', [$token, time_microtime() - c::$USERTOKEN_EXPTIME, app_getUserIP(), app_getUserUA()]);
        } else {
            $result = sql_query1('SELECT userId FROM userToken WHERE BINARY token LIKE ? AND loginTime >= ?', [$token, time_microtime() - c::$USERTOKEN_EXPTIME]);
        }
        if ($verifyIsActive && (! app_isTokenActive($token))) {
            return false;
        }
        $uid = intval($result['userId']);
        return ($uid ? $uid : false);
    }
    function app_isUserOnline($uid = 0) {
        $data = sql_query('SELECT onlineTime, onlineIP, onlineUA FROM userToken WHERE userId = ?', [$uid]);
        foreach ($data as $k => $v) {
            if (intval($v['onlineTime']) + c::$USERONLINE_INTERVALTIME >= time_microtime()) {
                return true;
            }
        }
        return false;
    }
    function app_isTokenActive($token = '') {
        $data = sql_query1('SELECT onlineTime, onlineIP, onlineUA FROM userToken WHERE BINARY token LIKE ?', [$token]);
        if (! $data) {
            return false;
        }
        if (intval($data['onlineTime']) + c::$USERONLINE_INTERVALTIME < time_microtime()) {
            return false;
        }
        return true;
    }
    function app_getUserTags($udata) {
        $res1 = $res2 = [];
        $pdata = sql_query1('SELECT * FROM xnzx_student_table WHERE uid = ?', [$udata['id']]);
        if ($udata['userGroup'])
            $res1[] = $res2[] = $udata['userGroup'];
        if ($pdata)
            $res2[] = '新中 ' . $pdata['name'];
        if ($pdata)
            $res1[] = '台山市新宁中学  ' . $pdata['year'] . ' 秋届 ' . $pdata['class'] . ' 班 ' . $pdata['sid'] . ' 号  ' . $pdata['name'];
        return [$res1, $res2];
    }
    function app_getUserData($uid, $json, $uid_to_query) {
        if (intval($uid_to_query) === $uid) {
            $uid_to_query = null;
        }
        
        $real_uid = ($uid_to_query ? intval($uid_to_query) : $uid);
        if (! $uid_to_query) {
            $count = sql_exec_count('UPDATE userToken SET onlineTime = ?, onlineIP = ?, onlineUA = ?, rand = ? WHERE BINARY token LIKE ?', [time_microtime(), app_getUserIP(), app_getUserUA(), '' . rand(), $json['userToken']]);
            if ($count !== 1) {
                return null;
            }
        }
        $udata = sql_query1('SELECT ' . sql_fieldsExcept('user', ['pass']) . ' FROM user WHERE id = ?', [$real_uid]);
        if (! $udata) {
            return null;
        }
        
        if (! $uid_to_query) {
            $loginDetails = sql_query('SELECT id, token, loginTime, loginIP, onlineTime, onlineIP, onlineUA FROM userToken WHERE userId = ? ORDER BY id DESC LIMIT ' . c::$USERLOGINDETAILS_LIMIT, [$real_uid]);
            $udata['phone'] = text_format_phone($udata['phone']);
            foreach ($loginDetails as $k => $v) {
                $time = $loginDetails[$k]['onlineTime'];
                if (time_microtime() - intval($time) <= c::$USERONLINE_INTERVALTIME) {
                    $time = 0;
                } else {
                    $time = $time;
                }
                $loginDetails[$k]['onlineTime'] = $time;
                $loginDetails[$k]['loginTime'] = $loginDetails[$k]['loginTime'];
                $loginDetails[$k]['tokenActive'] = app_verifyUserToken($v['token'], false, false);
                $loginDetails[$k]['isMe'] = ($v['token'] == $json['userToken']);
                unset($loginDetails[$k]['token']);
                unset($loginDetails[$k]['onlineIP']);
                unset($loginDetails[$k]['onlineUA']);
            }
            $udata['loginDetails'] = $loginDetails;
            $udata['isMe'] = true;
        }
        else {
            $isAdmin = (app_getAdminLevel($uid) < 100);
            $udata['phone'] = text_format_phone($udata['phone'], $isAdmin);
            $udata['signupIP'] = text_format_IP($udata['signupIP'], $isAdmin);
            $udata['isOnline'] = app_isUserOnline($udata['id']);
            $lastOnline = sql_query1('SELECT MAX(onlineTime) FROM userToken WHERE userId = ?', [$udata['id']]);
            $udata['lastOnlineTime'] = $lastOnline['MAX(onlineTime)'];
        }
        $udata['userTags'] = app_getUserTags($udata);
        $udata['adminUids'] = app_getAdminTable(0);
        
        return $udata;
    }
    function app_getUserData_mini($uid) {
        $udata = sql_query1('SELECT ' . implode(', ', c::$USER_PUBLICSQLKEYS) . ' FROM user WHERE id = ? LIMIT 1', [$uid]);
        $udata['userTags'] = app_getUserTags($udata);
        return $udata;
    }
    function app_getAdminTable($orgin = false, $minLevel = 1) {
        $table = c::$ADMIN_UIDS;
        if ($orgin)
            return $table;
        $res = [];
        foreach ($table as $k => $v) {
            if ($k < $minLevel) continue;
            foreach ($v as $k2 => $v2) {
                $res[] = $v2;
            }
        }
        return $res;
    }
    function app_getAdminLevel($uid = 0) {
        $table = app_getAdminTable(true);
        foreach ($table as $k => $v) {
            if (in_array($uid, $v))
                return $k;
        }
        return false;
    }
    function app_verifyPhone($phone = '', $verifyCode = '') {
        $smsConfig = c::$SMS_VERIFY_CONFIG;
        $data = sql_query1('SELECT id FROM phoneVerify WHERE phone = ? AND sendTime >= ? AND used IS NULL AND userIP LIKE ? AND userUA LIKE ? ORDER BY sendTime DESC LIMIT 1', [intval($phone), time_microtime() - $smsConfig['verifyCodeExpTime'], app_getUserIP(), app_getUserUA()]);
        if (! $data) {
            return ['status' => false, 'code' => 'LOCAL_NOT_FOUND'];
        }
        $smsResult = sms_check_verify_code($phone, $verifyCode, $data['id']);
        if (! $smsResult) {
            return ['status' => false, 'id' => null];
        }
        return ['status' => true, 'id' => intval($data['id'])];
    }
    function app_setPhoneVerifyUsed($id = 0, $intent = 'default') {
        // 顺便把之前未使用的废掉
        $a = sql_exec_count('UPDATE phoneVerify SET used = ? WHERE id = ? AND used IS NULL', [$intent, $id]);
        $b = sql_exec_count('UPDATE phoneVerify SET used = \'expire\' WHERE id = ? AND used IS NULL', [$id]);
        return ($a && $b);
    }
    function app_check($config_text = 'd', $fields = [], $minAdminLevel = 1, $isGET = false) {
        $config = str_split($config_text);
        $json = api_input($isGET);
        $pvr = 0;
        
        // x标记的逻辑是，不强制登录，但是登录了的还是取登录信息
        if ((! $json['userToken']) && (! in_array('x', $config))) {
            api_callback(0, '你还没登录呢~');
        } elseif (! $json['userToken']) { } else {
            $uid = app_verifyUserToken($json['userToken'], false);
            if (! $uid) {
                api_callback(0, '用户登录失效，请重新登录哦~');
            }
        }
        if (in_array('s', $config)) {
            if (app_getAdminLevel($uid) < $minAdminLevel) {
                api_callback(0, '你的权限不足哦！~');
            }
        }
        
        if (c::$VAPTCHA_CONFIG['status'] && in_array('v', $config)) {
            if ($isGET)
                $json['vaptchaData'] = json_decode($json['vaptchaData'], true);
            if (! vaptcha_verify($json['vaptchaData'])) {
                api_callback(0, '人机验证不通过哦~');
            }
        }
        if (in_array('a', $config)) {
            foreach ($fields as $v) {
                if (! isset($json[$v]) || $json[$v] === '') {
                    api_callback(0, '没有填完信息呢~');
                }
            }
        }
        if (in_array('p', $config)) {
            $smsConfig = c::$SMS_VERIFY_CONFIG;
            if (strlen($json['phoneVerify']) != intval($smsConfig['codeLength'])) {
                api_callback(0, '手机验证码是' . intval($smsConfig['codeLength']) . '位的，蠢！');
            }
            $phoneVerify = app_verifyPhone($json['phone'], $json['phoneVerify']);
            if (! $phoneVerify['status']) {
                api_callback(0, app_sms_error_message($phoneVerify, 'check'));
            }
            $pvr = intval($phoneVerify['id']);
        }
        $file = api_inputFile();
        if (in_array('f', $config)) {
            if (! $file) {
                api_callback(0, '你的文件呢？');
            }
        }
        
        return [$uid, $json, $file, $pvr];
    }
    
    function app_quill_format($oriContent = '') {
        $content = $oriContent;
        
        $firstImg = '';
        /* img标签格式化 */
        // 先取出所有的img标签
        preg_match_all('/<img.*?src=["\'](.*?)["\'].*?>/iu', $content, $matchImg);
        // 再一个一个看是否是base64类型。判base64图片正则参考：https://www.cnblogs.com/xianhuiwang/p/7500875.html
        foreach ($matchImg[1] as $v) {
            $ori_v = $v;
            $v = preg_replace('/\s/iu', '', $v);
            $isBase64 = preg_match('/data:.*;base64,(.*)/iu', $v, $matchSrc);
            if ($isBase64) {
                $base64 = $matchSrc[1];
                $tmpName = text_random();
                $imgTmpUrl = sys_get_temp_dir() . '/' . $tmpName;
                $imgToUrl = u('static_user://forum') . '/' . $tmpName . '.jpg';
                $imgContent = base64_decode($base64);
                $img = imagecreatefromstring($imgContent);
                imagejpeg($img, $imgTmpUrl);
                $result1 = staticcs_upload($imgTmpUrl, $imgToUrl);
                if (! $result1) {
                    api_callback(0, '上传图片失败！~');
                }
                $realUrl = 'static://' . ltrim($imgToUrl, '/');
                $content = str_ireplace($ori_v, $realUrl, $content);
                if (! $firstImg) $firstImg = $realUrl;
            }
            else {
                if (! $firstImg) $firstImg = $v;
            }
        }
        // 历史完整URL归一为 static:// 协议，并压缩域名后的多余斜杠
        $pattern = '#https?://' . preg_quote(explode('/', u('static://'))[2], '#') . '/+#iu';
        $content = preg_replace($pattern, 'static://', $content);
        $firstImg = preg_replace($pattern, 'static://', $firstImg);
        return [$content, $firstImg];
    }
    
    /* 输出时将 static:// 协议解析为当前静态资源URL（u('static://') 自带尾斜杠） */
    function app_staticcs_resolve($str = '') {
        return str_ireplace('static://', u('static://'), '' . $str);
    }
    
    function app_xnzx_sid2name($year = 0, $class = 0, $sid = 0) {
        $data = sql_query1('SELECT name, uid FROM xnzx_student_table WHERE year = ? AND class = ? AND type = 0 AND sid = ?', [$year, $class, $sid]);
        return [$sid, $data['name'], $data['uid']];
    }
    function app_xnzx_sids2names($year = 0, $class = 0, $sids = []) {
        $result = [];
        foreach ($sids as $k => $v) {
            $result[] = app_xnzx_sid2name($year, $class, $v);
        }
        return $result;
    }
    function app_xnzx_getStudents_byYear($year = 0, $getClasses = 0) {
        $students = $classes = [];
        $_classes = sql_query('SELECT DISTINCT class FROM xnzx_student_table WHERE type = 0 AND year = ?', [$year]);
        foreach ($_classes as $k => $v) {
            $_students = sql_query('SELECT sid, name FROM xnzx_student_table WHERE year = ? AND class = ? AND type = 0 AND disabled = 0', [$year, $v['class']]);
            foreach ($_students as $k2 => $v2) {
                $_students[$k2][0] = $v2['sid'];
                $_students[$k2][1] = $v2['name'];
                unset($_students[$k2]['sid']);
                unset($_students[$k2]['name']);
            }
            $students[$v['class']] = $_students;
            $classes[] = $v['class'];
        }
        return ($getClasses ? $classes : $students);
    }
    function app_xnzx_getStudents($getType = 0) {
        $res = [];
        $years = sql_query('SELECT DISTINCT year FROM xnzx_class_table');
        if ($getType == 0 || $getType == 1) {
            foreach ($years as $v) {
                $res[$v['year']] = app_xnzx_getStudents_byYear($v['year'], $getType);
            }
        }
        else if ($getType == 2) {
            foreach ($years as $v) {
                $res[] = $v['year'];
            }
        }
        return $res;
    }
    function app_xnzx_getWeeklyPeople($d = [], $isGetTypists = 0) {
        $authors = $typists = [];
        foreach ($d as $k2 => $v2) {
            $authors[] = $v2['author'];
            $typists[] = $v2['typist'];
        }
        return ($isGetTypists ? $typists : $authors);
    }
    
    function app_xnzx_PA_getProperties($pid = 0, $uid = 0) {
        $likes = 0;
        $list = sql_query('SELECT * FROM xnzx_student_PA WHERE pid = ? ORDER BY type', [$pid]);
        foreach ($list as $k => $v) {
            $list[$k]['detail'] = explode(PHP_EOL, $v['detail']);
            $like = app_getLikes('PA', $v['id']);
            $list[$k]['likes'] = $like;
            $list[$k]['liked'] = app_isLiked('PA', $v['id'], $uid);
            $likes += $like;
        }
        $list = arr_sort($list, 'type', false, 'likes', true);
        return [$list, $likes];
    }
    
    function app_like($parent = '', $id = 0, $uid = 0) {
        if (! sql_query_count('SELECT id FROM action WHERE parent LIKE ? AND pid = ? AND uid = ? AND type = 2', [$parent, intval($id), $uid])) {
            if (! sql_exec_count('INSERT INTO action (id, parent, type, value, pid, uid, actionTime, userIP, userUA) VALUES (?, ?, 2, 1, ?, ?, ?, ?, ?)', [sql_newId('action'), $parent, intval($id), $uid, time_microtime(), app_getUserIP(), app_getUserUA()])) {
                api_callback(0, '操作数据失败了哦~');
            }
        } else {
            if (! sql_exec_count('UPDATE action SET value = ABS(value - 1), actionTime = ?, userIP = ?, userUA = ? WHERE parent LIKE ? AND pid = ? AND uid = ? AND type = 2', [time_microtime(), app_getUserIP(), app_getUserUA(), $parent, intval($id), $uid])) {
                api_callback(0, '操作数据失败了哦~');
            }
        }
    }
    function app_look($parent = '', $id = 0, $uid = 0) {
        if (! sql_query_count('SELECT id FROM action WHERE parent LIKE ? AND pid = ? AND uid = ? AND type = 1 LIMIT 1', [$parent, $id, $uid])) {
            sql_exec('INSERT INTO action (id, parent, type, pid, uid, actionTime, userIP, userUA) VALUES (?, ?, 1, ?, ?, ?, ?, ?)', [sql_newId('action'), $parent, $id, $uid, time_microtime(), app_getUserIP(), app_getUserUA()]);
        }
    }
    function app_getLooks($parent = '', $id = 0) {
        return sql_query_count('SELECT id FROM action WHERE parent LIKE ? AND pid = ? AND type = 1', [$parent, $id]);
    }
    function app_getLikes($parent = '', $id = 0) {
        return sql_query_count('SELECT id FROM action WHERE parent LIKE ? AND pid = ? AND type = 2 AND value = 1', [$parent, $id]);
    }
    function app_isLiked($parent = '', $id = 0, $uid = 0) {
        return !! sql_query1('SELECT id FROM action WHERE parent LIKE ? AND pid = ? AND uid = ? AND type = 2 AND value = 1', [$parent, $id, $uid]);
    }
    
    // 还有牛头人！Claude 重写了功能，但是没改名字
    function vaptcha_verify($data = []) {
        $captcha_id = c::$VAPTCHA_CONFIG['appId'];
        $captcha_key = s::$VAPTCHA_CONFIG['appKey'];
        
        $lot_number = $data['lot_number'] ?? '';
        $captcha_output = $data['captcha_output'] ?? '';
        $pass_token = $data['pass_token'] ?? '';
        $gen_time = $data['gen_time'] ?? '';
        
        $sign_token = hash_hmac('sha256', $lot_number, $captcha_key);
        
        $query = [
            'lot_number' => $lot_number,
            'captcha_output' => $captcha_output,
            'pass_token' => $pass_token,
            'gen_time' => $gen_time,
            'sign_token' => $sign_token,
        ];
        
        $url = 'https://captcha.alicaptcha.com/validate?captcha_id=' . urlencode($captcha_id);
        try {
            $result = http($url, http_build_query($query), ['Content-Type: application/x-www-form-urlencoded']);
            if (! $result) {
                return true;
            }
        } catch (Exception $e) {
            return true;
        }
        return isset($result['result']) && $result['result'] === 'success';
    }
    
    function aliyun_rpc_percent_encode($str = '') {
        return str_replace(['+', '*', '%7E'], ['%20', '%2A', '~'], rawurlencode($str));
    }
    function aliyun_rpc_request($action = '', $params = []) {
        $secretConfig = s::$ALIYUN_SMS_CONFIG;
        $query = array_merge([
            'AccessKeyId' => $secretConfig['accessKeyId'],
            'Action' => $action,
            'Format' => 'JSON',
            'RegionId' => $secretConfig['regionId'],
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce' => md5(uniqid('', true) . mt_rand(1000, 9999)),
            'SignatureVersion' => '1.0',
            'Timestamp' => gmdate('Y-m-d\\TH:i:s\\Z'),
            'Version' => '2017-05-25'
        ], $params);
        ksort($query);
        $canonicalized = [];
        foreach ($query as $k => $v) {
            if ($v === null) continue;
            if (is_bool($v)) {
                $v = ($v ? 'true' : 'false');
            }
            $canonicalized[] = aliyun_rpc_percent_encode($k) . '=' . aliyun_rpc_percent_encode($v);
        }
        $stringToSign = 'POST&%2F&' . aliyun_rpc_percent_encode(implode('&', $canonicalized));
        $query['Signature'] = base64_encode(hash_hmac('sha1', $stringToSign, $secretConfig['accessKeySecret'] . '&', true));
        return http($secretConfig['endpoint'], http_build_query($query), ['Content-Type: application/x-www-form-urlencoded'], []);
    }
    function app_sms_build_template_params() {
        $config = c::$SMS_VERIFY_CONFIG;
        $templateParams = $config['templateParams'];
        if (! is_array($templateParams)) {
            $templateParams = [];
        }
        $verifyMinutes = max(1, intval(ceil($config['verifyCodeExpTime'] / 1000 / 60)));
        foreach ($templateParams as $k => $v) {
            if ($v === '__VERIFY_MINUTES__') {
                $templateParams[$k] = '' . $verifyMinutes;
            }
        }
        return $templateParams;
    }
    function app_sms_error_message($result = [], $scene = 'send') {
        $code = strtoupper(isset($result['code']) ? $result['code'] : '');
        $map = [
            'LOCAL_CONFIG_MISSING' => '短信服务配置还没填好，请联系管理员处理~',
            'LOCAL_TEMPLATE_INVALID' => '短信模板配置不对，请联系管理员处理~',
            'LOCAL_HTTP_ERROR' => '短信服务响应异常，请稍后再试试~',
            'LOCAL_NOT_FOUND' => '手机验证码错误或失效~',
            'MOBILE_NUMBER_ILLEGAL' => '你的手机号不对劲呢~',
            'BUSINESS_LIMIT_CONTROL' => '这个手机号今天发送验证码太多次啦~',
            'FREQUENCY_FAIL' => '发送太频繁啦，稍后再试试~',
            'INVALID_PARAMETERS' => '短信服务参数配置不对，请联系管理员处理~',
            'FUNCTION_NOT_OPENED' => '短信服务还没有开通哦，请联系管理员处理~',
            'RAMPERMISSIONDENY' => '短信服务权限配置不对，请联系管理员处理~',
            'UNAUTHORIZEDOPERATION' => '短信服务鉴权失败，请联系管理员处理~',
            'UNKNOWN' => '手机验证码错误或失效~'
        ];
        if (isset($map[$code])) {
            return $map[$code];
        }
        if ($scene == 'check') {
            return '手机验证码错误或失效~';
        }
        return (! empty($result['message']) ? '短信服务异常：' . $result['message'] : '发送手机验证码失败了……');
    }
    function sms_send_verify_code($phone = '', $outId = '') {
        $secretConfig = s::$ALIYUN_SMS_CONFIG;
        $smsConfig = c::$SMS_VERIFY_CONFIG;
        if (! ($secretConfig['accessKeyId'] && $secretConfig['accessKeySecret'] && $secretConfig['signName'] && $secretConfig['templateCode'])) {
            return ['status' => false, 'code' => 'LOCAL_CONFIG_MISSING'];
        }
        $templateParams = app_sms_build_template_params();
        if (! in_array('##code##', $templateParams, true)) {
            return ['status' => false, 'code' => 'LOCAL_TEMPLATE_INVALID'];
        }
        $params = [
            'CountryCode' => $smsConfig['countryCode'],
            'PhoneNumber' => $phone,
            'SignName' => $secretConfig['signName'],
            'TemplateCode' => $secretConfig['templateCode'],
            'TemplateParam' => json_encode($templateParams, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'CodeLength' => intval($smsConfig['codeLength']),
            'ValidTime' => intval(ceil($smsConfig['verifyCodeExpTime'] / 1000)),
            'DuplicatePolicy' => intval($smsConfig['duplicatePolicy']),
            'Interval' => intval($smsConfig['interval']),
            'CodeType' => intval($smsConfig['codeType'])
        ];
        if ($secretConfig['schemeName']) {
            $params['SchemeName'] = $secretConfig['schemeName'];
        }
        if ($outId !== '') {
            $params['OutId'] = '' . $outId;
        }
        $result = aliyun_rpc_request('SendSmsVerifyCode', $params);
        if (! is_array($result)) {
            return ['status' => false, 'code' => 'LOCAL_HTTP_ERROR'];
        }
        $code = (isset($result['Code']) ? $result['Code'] : '');
        $message = (isset($result['Message']) ? $result['Message'] : '');
        $success = (! empty($result['Success']) && $code == 'OK');
        return [
            'status' => $success,
            'code' => $code,
            'message' => $message,
            'data' => $result
        ];
    }
    function sms_check_verify_code($phone = '', $verifyCode = '', $outId = '') {
        $secretConfig = s::$ALIYUN_SMS_CONFIG;
        $smsConfig = c::$SMS_VERIFY_CONFIG;
        if (! ($secretConfig['accessKeyId'] && $secretConfig['accessKeySecret'])) {
            return ['status' => false, 'code' => 'LOCAL_CONFIG_MISSING'];
        }
        $params = [
            'CountryCode' => $smsConfig['countryCode'],
            'PhoneNumber' => $phone,
            'VerifyCode' => $verifyCode,
            'CaseAuthPolicy' => intval($smsConfig['caseAuthPolicy'])
        ];
        if ($secretConfig['schemeName']) {
            $params['SchemeName'] = $secretConfig['schemeName'];
        }
        if ($outId !== '') {
            $params['OutId'] = '' . $outId;
        }
        $result = aliyun_rpc_request('CheckSmsVerifyCode', $params);
        if (! is_array($result)) {
            return ['status' => false, 'code' => 'LOCAL_HTTP_ERROR'];
        }
        $code = (isset($result['Code']) ? $result['Code'] : '');
        $message = (isset($result['Message']) ? $result['Message'] : '');
        $verifyResult = strtoupper(isset($result['Model']['VerifyResult']) ? $result['Model']['VerifyResult'] : 'UNKNOWN');
        return (! empty($result['Success']) && $code == 'OK' && $verifyResult == 'PASS');
    }
    
    /* S3 兼容对象存储（AWS SigV4 签名，虚拟主机风格 bucket.endpoint） */
    function staticcs_key($path = '') {
        return c::$STATICCS_CONFIG['prefix'] . ltrim($path, '/');
    }
    function staticcs_request($method = 'GET', $key = '', $filePath = null, $headersExtra = [], $query = [], $head = false) {
        $conf = c::$STATICCS_CONFIG;
        $secret = s::$OSS_CONFIG;
        $host = $conf['bucket'] . '.' . preg_replace('/^https?:\\/\\//iu', '', $conf['endpoint']);
        $uri = '/' . implode('/', array_map('rawurlencode', explode('/', staticcs_key($key))));
        
        ksort($query);
        $canonicalQuery = '';
        foreach ($query as $k => $v) {
            $canonicalQuery .= ($canonicalQuery ? '&' : '') . rawurlencode($k) . '=' . rawurlencode($v);
        }
        
        $payloadHash = $filePath ? hash_file('sha256', $filePath) : hash('sha256', '');
        $date = gmdate('Ymd');
        $amzDate = gmdate('Ymd\THis\Z');
        $headers = array_merge([
            'host' => $host,
            'x-amz-content-sha256' => $payloadHash,
            'x-amz-date' => $amzDate
        ], $headersExtra);
        ksort($headers);
        $canonicalHeaders = '';
        $signedHeaders = '';
        foreach ($headers as $k => $v) {
            $canonicalHeaders .= $k . ':' . trim($v) . "\n";
            $signedHeaders .= ($signedHeaders ? ';' : '') . $k;
        }
        
        $canonicalRequest = implode("\n", [$method, $uri, $canonicalQuery, $canonicalHeaders, $signedHeaders, $payloadHash]);
        $scope = $date . '/' . $conf['region'] . '/s3/aws4_request';
        $stringToSign = implode("\n", ['AWS4-HMAC-SHA256', $amzDate, $scope, hash('sha256', $canonicalRequest)]);
        $kSigning = hash_hmac('sha256', 'aws4_request', hash_hmac('sha256', 's3', hash_hmac('sha256', $conf['region'], hash_hmac('sha256', $date, 'AWS4' . $secret['accessKeySecret'], true), true), true), true);
        $signature = hash_hmac('sha256', $stringToSign, $kSigning);
        
        $headerLines = ['Authorization: AWS4-HMAC-SHA256 Credential=' . $secret['accessKeyId'] . '/' . $scope . ', SignedHeaders=' . $signedHeaders . ', Signature=' . $signature];
        foreach ($headers as $k => $v) {
            $headerLines[] = $k . ': ' . trim($v);
        }
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://' . $host . $uri . ($canonicalQuery ? '?' . $canonicalQuery : ''));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headerLines);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if ($head) {
            curl_setopt($curl, CURLOPT_NOBODY, 1);
            curl_setopt($curl, CURLOPT_HEADER, 1);
        }
        $fp = null;
        if ($filePath) {
            $fp = fopen($filePath, 'r');
            curl_setopt($curl, CURLOPT_UPLOAD, 1);
            curl_setopt($curl, CURLOPT_INFILE, $fp);
            curl_setopt($curl, CURLOPT_INFILESIZE, filesize($filePath));
        }
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($fp) fclose($fp);
        return [$status, $result];
    }
    
    function staticcs_dir($name = '') {
        // 对象存储无目录概念
        return true;
    }
    /* 生成 PUT 预签名URL（SigV4 query 签名，Content-Type 和 Content-Length 参与签名） */
    function staticcs_presign($key = '', $contentType = '', $contentLength = 0, $expires = 600) {
        $conf = c::$STATICCS_CONFIG;
        $secret = s::$OSS_CONFIG;
        $host = $conf['bucket'] . '.' . preg_replace('/^https?:\\/\\//iu', '', $conf['endpoint']);
        $uri = '/' . implode('/', array_map('rawurlencode', explode('/', staticcs_key($key))));
        $date = gmdate('Ymd');
        $amzDate = gmdate('Ymd\THis\Z');
        $scope = $date . '/' . $conf['region'] . '/s3/aws4_request';
        
        $headers = ['content-length' => '' . intval($contentLength), 'host' => $host];
        if ($contentType) $headers['content-type'] = trim($contentType);
        ksort($headers);
        $signedHeaders = implode(';', array_keys($headers));
        $query = [
            'X-Amz-Algorithm' => 'AWS4-HMAC-SHA256',
            'X-Amz-Credential' => $secret['accessKeyId'] . '/' . $scope,
            'X-Amz-Date' => $amzDate,
            'X-Amz-Expires' => '' . intval($expires),
            'X-Amz-SignedHeaders' => $signedHeaders
        ];
        ksort($query);
        $canonicalQuery = '';
        foreach ($query as $k => $v) {
            $canonicalQuery .= ($canonicalQuery ? '&' : '') . rawurlencode($k) . '=' . rawurlencode($v);
        }
        
        $canonicalHeaders = '';
        foreach ($headers as $k => $v) {
            $canonicalHeaders .= $k . ':' . trim($v) . "\n";
        }
        $canonicalRequest = implode("\n", ['PUT', $uri, $canonicalQuery, $canonicalHeaders, $signedHeaders, 'UNSIGNED-PAYLOAD']);
        $stringToSign = implode("\n", ['AWS4-HMAC-SHA256', $amzDate, $scope, hash('sha256', $canonicalRequest)]);
        $kSigning = hash_hmac('sha256', 'aws4_request', hash_hmac('sha256', 's3', hash_hmac('sha256', $conf['region'], hash_hmac('sha256', $date, 'AWS4' . $secret['accessKeySecret'], true), true), true), true);
        $signature = hash_hmac('sha256', $stringToSign, $kSigning);
        
        return 'https://' . $host . $uri . '?' . $canonicalQuery . '&X-Amz-Signature=' . $signature;
    }
    /* HEAD 对象，返回大小（字节）；不存在返回 false */
    function staticcs_stat($key = '') {
        list($status, $header) = staticcs_request('HEAD', $key, null, [], [], true);
        if ($status < 200 || $status >= 300) return false;
        preg_match('#content-length:\s*(\d+)#iu', $header, $m);
        return intval(isset($m[1]) ? $m[1] : 0);
    }
    /* 校验直传对象存在且大小合规（传 false 可跳过大小限制），超限则删除 */
    function staticcs_verify_uploaded($key = '', $sizeLimit = null) {
        $size = staticcs_stat($key);
        if ($size === false) return false;
        if ($sizeLimit !== false && $size > ($sizeLimit === null ? c::$UPLOAD_SIZELIMIT : $sizeLimit)) {
            staticcs_del($key);
            return false;
        }
        return true;
    }
    function staticcs_upload($from = '', $to = '') {
        $mime = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif',
            'webp' => 'image/webp', 'svg' => 'image/svg+xml', 'ico' => 'image/x-icon', 'mp4' => 'video/mp4',
            'ttf' => 'font/ttf', 'css' => 'text/css', 'js' => 'text/javascript', 'json' => 'application/json'
        ];
        $ext = strtolower(pathinfo($to, PATHINFO_EXTENSION));
        $contentType = isset($mime[$ext]) ? $mime[$ext] : 'application/octet-stream';
        list($status) = staticcs_request('PUT', $to, $from, ['content-type' => $contentType]);
        return $status >= 200 && $status < 300;
    }
    function staticcs_rename($from = '', $to = '') {
        // S3 无 rename：CopyObject 后删除源对象
        $copySource = c::$STATICCS_CONFIG['bucket'] . '/' . implode('/', array_map('rawurlencode', explode('/', staticcs_key($from))));
        list($status) = staticcs_request('PUT', $to, null, ['x-amz-copy-source' => $copySource]);
        if ($status < 200 || $status >= 300) return false;
        return staticcs_del($from);
    }
    function staticcs_del($url = '') {
        list($status) = staticcs_request('DELETE', $url);
        return $status >= 200 && $status < 300;
    }
    function staticcs_list($url = '') {
        $res = [];
        $prefix = staticcs_key($url);
        if ($prefix && substr($prefix, -1) != '/') $prefix .= '/';
        $token = '';
        do {
            $query = ['list-type' => '2', 'delimiter' => '/', 'prefix' => $prefix];
            if ($token) $query['continuation-token'] = $token;
            list($status, $body) = staticcs_request('GET', '', null, [], $query);
            if ($status < 200 || $status >= 300) return $res;
            $xml = simplexml_load_string($body);
            foreach ($xml->CommonPrefixes as $v) {
                $res[] = ['name' => basename(rtrim((string)$v->Prefix, '/')), 'type' => true, 'size' => 0, 'time' => 0];
            }
            foreach ($xml->Contents as $v) {
                $key = (string)$v->Key;
                if ($key == $prefix) continue;
                $res[] = ['name' => basename($key), 'type' => false, 'size' => intval((string)$v->Size), 'time' => strtotime((string)$v->LastModified) * 1000];
            }
            $token = isset($xml->NextContinuationToken) ? (string)$xml->NextContinuationToken : '';
        } while ($token);
        return $res;
    }
?>