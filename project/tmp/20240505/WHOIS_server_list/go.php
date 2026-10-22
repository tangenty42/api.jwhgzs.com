<?php
    
    $url_iana_db = 'https://www.iana.org/domains/root/db';
    $url_iana_whois = 'https://www.iana.org/whois';
    $headers = [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
    ];
    
    function request($url = '', $data = null, $header = [], $cookie = [], $auto2JSON = true) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
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
    
    if (file_exists('list.dat')) {
        $olds = explode(PHP_EOL, file_get_contents('list.dat'));
        for ($i = count($olds); $i > 0; $i --) {
            if ($olds[$i]) {
                [$ext, ] = explode(' ', $olds[$i]);
                $start_flag = $ext;
                break;
            }
        }
    }
    
    $res_json = [];
    $res_php = '';
    $file = fopen('list.dat', 'a');
    
    $base_raw = request($url_iana_db, null, $headers);
    preg_match_all('/' . preg_quote('">', '/') . '([\\.a-z]+?)' . preg_quote('</a></span></td>', '/') . '/im', $base_raw, $base_matches);
    $matched_start = ! $start_flag;
    foreach ($base_matches[1] as $ext) {
        if ($ext == $start_flag) {
            $matched_start = true;
            continue;
        }
        if (! $matched_start)
            continue;
        
        $whois_raw = request($url_iana_whois . '?q=' . $ext, null, $headers);
        preg_match('/^' . preg_quote('whois:') . '[ \f\t\v]+(\\S*?)$/im', $whois_raw, $whois_matches);
        $whois_server = $whois_matches[1];
        
        if ($whois_server)
            fwrite($file, "$ext $whois_server\n");
    }
    
    fclose($file);
?>