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
        // 阿里云短信 RAM 用户（仅 dypnsapi 验证码，勿与 OSS 混用同一 AK）
        public static $ALIYUN_SMS_CONFIG = [
            'accessKeyId' => '',
            'accessKeySecret' => '',
            'regionId' => 'cn-hongkong',
            'endpoint' => 'https://dypnsapi.aliyuncs.com/',
            'schemeName' => '',
            'signName' => '',
            'templateCode' => ''
        ];
        // 阿里云 OSS RAM 用户（仅 bucket user/* 的 Put/Get/Delete，勿与短信混用同一 AK）
        public static $OSS_CONFIG = [
            'accessKeyId' => '',
            'accessKeySecret' => ''
        ];
    }
?>