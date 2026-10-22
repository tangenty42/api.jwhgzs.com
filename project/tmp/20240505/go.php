<?php
    
    include_once './WHOIS_server_list/list.php';
    
    $no_date_exts = [
        '.gg', '.je'
    ];
    $ugly_whois_exts = [
        '.to'
    ];
    foreach ($ugly_whois_exts as $v)
        $whois_servers[$v] = false;
    $standard_exts = [
        '.sh' => 'Registry Expiry Date:',
        '.im' => 'The domain', 'Expiry Date:',
        '.ws' => 'Registrar Registration Expiration Date:',
        '.st' => 'Expiration Date:',
        '.su' => 'free-date:',
        
    ];
    
    $name = $_GET['name'];
    $ext = $_GET['ext'];
    
    if ($name) {
        function request() {
            global $whois_servers, $standard_exts, $name, $ext;
            $server = $whois_servers[$ext];
            if ($server) {
                $out = $name . $ext . "\r\n";
                $out .= "Connection: Close\r\n\r\n";
                $socket = fsockopen($server, 43, $errno, $errstr, 30);
                if (! $socket){
                    echo $errstr;
                    die;
                }
                fputs($socket, $out);
                $res = '';
                while (! feof($socket))
                    $res .= nl2br(fgets($socket, 255));
                fclose($socket);
                
                if ($ext == '.gg' || $ext == '.je') {
                    if (preg_match('/^' . preg_quote('NOT FOUND') . '/ims', $res))
                        return false;
                    else
                        return date('Y') . '/12/01';
                }
                elseif ($standard_exts[$ext]) {
                    $prefix = $standard_exts[$ext];
                    preg_match('/^' . preg_quote($prefix, '/') . '\\s+(.+?)\\s*' . preg_quote('<br') . '/ims', $res, $matches);
                    if (! $matches[1])
                        return false;
                    else
                        return date('Y/m/d', strtotime($matches[1]));
                }
                // else {
                //     die($res);
                // }
            }
            elseif ($server === false) {
                if ($ext == '.to') {
                    $res = http('https://www.tonic.to/whois?' . $name . $ext);
                    preg_match('/^' . preg_quote('Expires on:', '/') . '\\s+(.+?)$/ims', $res, $matches);
                    if (! $matches[1])
                        return false;
                    else
                        return date('Y/m/d', strtotime($matches[1]));
                }
            }
            die('后缀不支持！');
        }
        
        $res = request();
        if ($res) {
            if ($res == '1970/01/01') {
                $res = '注册局保留域名';
            }
            else {
                if (strtotime($res) <= time()) {
                    $res = '<strong>' . $res . '</strong>';
                    $strong = true;
                }
                $res = (in_array($ext, $no_date_exts) ? '估计过期时间：' : '过期时间：') . $res;
            }
        }
        else {
            $res = '<strong>♥♥♥可注册♥♥♥</strong>';
            $strong = true;
        }
        die(($strong ? '<!--*-->' : '') . '<li><strong>' . htmlspecialchars($name . $ext) . '</strong>：' . $res . '</li>');
    }
?>

<html>
    <head>
        <meta charset="utf-8">
        <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    </head>
    <body>
        <div id="app">
            <center style="font-size: 150%">进度：{{ a }} / {{ b }}（{{ (a / b * 100).toFixed(0) }} %）</center>
            <center>
                <button @click="control">{{ status ? '暂停' : '开始' }}</button>
                <button @click="clear_">清空数据</button>
            </center>
            <hr/>
            <center style="margin-top: 20px">
                <div v-for="v in list" v-html="v"></div>
            </center>
        </div>
        <script>
            const { createApp } = Vue
            
            const request = async (url) => await (await fetch(url)).text()
            
            const app = createApp({
                data() {
                    return {
                        status: false,
                        a: 0,
                        b: 0,
                        aim_list: [],
                        list: []
                    }
                },
                mounted() {
                    this.restore()
                },
                methods: {
                    cookie() {
                        localStorage.setItem('20240505_list', JSON.stringify(this.list))
                    },
                    async restore() {
                        this.list = JSON.parse(localStorage.getItem('20240505_list') || '[]')
                        this.a = this.list.length
                        const aim = JSON.parse(await request('aim.json'))
                        this.aim_list = aim.list
                        this.aim_ext = aim.ext
                        this.b = this.aim_list.length
                    },
                    async go() {
                        while (this.status && this.a < this.b) {
                            let v = await request('?name=' + this.aim_list[this.a] + '&ext=' + this.aim_ext)
                            if (v.indexOf('<!--*-->') == 0)
                                this.list.splice(0, 0, v)
                            else
                                this.list.push(v)
                            this.cookie()
                            this.a ++
                        }
                    },
                    control() {
                        this.status = ! this.status
                        if (this.status)
                            this.go()
                    },
                    clear_() {
                        const time = new Date().valueOf()
                        if (confirm('确定要清空数据吗？')) {
                            localStorage.setItem('20240505_list', '')
                            this.restore()
                        }
                    }
                }
            })
            app.config.compilerOptions.isCustomElement = (tag) => tag == 'center'
            app.mount('#app')
        </script>
    </body>
</html>