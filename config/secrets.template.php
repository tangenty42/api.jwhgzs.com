<?php
    class s {
        public static $SQL_CONFIG = [
            'host' => '',
            'dbname' => '',
            'user' => '',
            'pass' => ''
        ];
        // NTR moment！Claude 一直不舍得动 Vaptcha 的名字
        public static $VAPTCHA_CONFIG = [
            'appKey' => ''
        ];
        // public static $VAPTCHA_SMS_CONFIG = [
        //     'smsid' => '',
        //     'smskey' => ''
        // ];
        public static $ALIYUN_SMS_CONFIG = [
            'accessKeyId' => '',
            'accessKeySecret' => '',
            'regionId' => 'cn-hongkong',
            'endpoint' => 'https://dypnsapi.aliyuncs.com/',
            'schemeName' => '',
            'signName' => '',
            'templateCode' => ''
        ];
        public static $OSS_CONFIG = [
            'accessKeyId' => '',
            'accessKeySecret' => ''
        ];
    }
?>