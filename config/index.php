<?php
    class c {
        public static $ROUTER = [
            'api.jwhgzs.com' => [
                0 => '/$',
                '/sitemap/[site]' => [
                    0 => '/sitemap?site=${site}'
                ]
            ],
            'jwh.su|www.jwh.su' => [
                0 => '/shortUrl',
                '/[tag]' => [
                    0 => '/shortUrl?tag=${tag}'
                ]
            ]
        ];
        
        public static $MAIN_SERVER = 'https://www.jwhgzs.com';
        public static $STATIC_SERVER = 'https://static.jwhgzs.com';
        public static $JSCOOKIE_MAINDOMAIN = '.jwhgzs.com';
        public static $UPLOAD_SIZELIMIT = 1024 * 1024 * 300;
        public static $USER_EDITABLESQLKEYS = [
            'pass', 'selfIntroduce'
        ];
        public static $USER_PUBLICSQLKEYS = [
            'id', 'name', 'userGroup', 'userAuth', 'avatarVersion', 'selfIntroduce'
        ];
        // $ADMIN_UIDS格式：level => uid
        public static $ADMIN_UIDS = [
            1 => [ 3, 109, 149 ],
            100 => [ 1 ]
        ];
        public static $USERINF_LENGTH = [
            'name_min' => 3,
            'name_max' => 10,
            'pass_min' => 8,
            'pass_max' => 32
        ];
        public static $USERTOKEN_EXPTIME = 60 * 60 * 48 * 1000;
        public static $USERONLINE_INTERVALTIME = 5 * 1000;
        public static $JSTHREAD_INTERVAL = 15000;
        public static $USERLOGINDETAILS_LIMIT = 20;
        public static $URLS_TABLE = [
            'local' => [
                'www' => [
                    0 => 'https://www.jwhgzs.com'
                ],
                'api' => [
                    0 => 'https://api.jwhgzs.com'
                ],
                'user' => [
                    0 => 'https://user.jwhgzs.com'
                ],
                'admin' => [
                    0 => 'https://admin.jwhgzs.com'
                ],
                'xnzx' => [
                    0 => 'https://xnzx.jwhgzs.com'
                ],
                'forum' => [
                    0 => 'https://forum.jwhgzs.com'
                ],
                'xfcl' => [
                    0 => 'https://xfcl.jwhgzs.com'
                ]
            ],
            // TODO: .su short url end of support
            'shortUrl' => [
                0 => 'https://jwh.su'
            ],
            'shortUrlPortal' => [
                0 => 'https://portal.jwh.su'
            ],
            'tzy' => [
                0 => 'https://www.tzy.su'
            ],
            'static' => [
                0 => 'https://static.jwhgzs.com',
                'public' => [
                    0 => '/public',
                    'js' => [
                        'ct4' => '/ct4.js'
                    ],
                    'img' => [
                        0 => '/img',
                        'logo_banner' => '/jwh/logo/banner_blue.svg?v=1',
                        'logo_ico' => '/jwh/logo/logo.ico?v=1',
                        'beian_icon' => '/beian_icon.png?v=1',
                        'sponsor_canva' => '/sponsors_logo/canva.svg?v=1',
                        'sponsor_tencentcloud' => '/sponsors_logo/tencent-cloud.svg?v=1',
                        'sponsor_dnspod' => '/sponsors_logo/dnspod.svg?v=1',
                        'sponsor_rainyun' => '/sponsors_logo/rainyun.png?v=2',
                        'sponsor_bt' => '/sponsors_logo/bt.svg?v=1',
                        'sponsor_vaptcha' => '/sponsors_logo/vaptcha.png?v=1',
                        'sponsor_ym163com' => '/sponsors_logo/ym163com.svg?v=5',
                        'sponsor_regery' => '/sponsors_logo/regery.svg?v=5',
                        'sponsor_spaceship' => '/sponsors_logo/spaceship.svg?v=1',
                        'bkg' => '/bkg.svg?v=2',
                        'bkg2' => '/bkg2.jpg?v=2',
                        'link_xnzx' => '/links_banner/xnzx.svg?v=5',
                        'link_forum' => '/links_banner/forum.svg?v=5',
                        'link_shortUrl' => '/links_banner/shortUrl.svg?v=5',
                        'link_xnzx_weekly' => '/links_banner/xnzx/weekly.svg?v=5',
                        'link_xnzx_PA' => '/links_banner/xnzx/PA.svg?v=5',
                        'link_xfcl' => '/links_banner/xfcl.svg?v=5',
                        'link_admin' => '/links_banner/admin.svg?v=5',
                        'link_tzy' => '/links_banner/tzy.svg?v=1',
                        'xfcl_logo' => '/jwh/logo/xfcl_logo.png?v=1',
                        'xfcl_banner' => '/jwh/logo/xfcl_banner.png?v=2',
                        'comp_lquot' => '/components/lquot.svg?v=1',
                        'comp_lquot_white' => '/components/lquot_white.svg?v=1',
                        'comp_rquot' => '/components/rquot.svg?v=1',
                        'comp_rquot_white' => '/components/rquot_white.svg?v=1',
                        'cuberin_logo' => '/cuberin/logo/logo.svg?v=1',
                        'tzy_banner' => '/tzy/banner.svg?v=1',
                        'tzy_bible' => '/tzy/links/bible.svg?v=2',
                        'tzy_cards' => '/tzy/links/cards.svg?v=2',
                        'tzy_memories' => '/tzy/links/memories.svg?v=2',
                        'PA' => [
                            0 => '/PA'
                        ]
                    ],
                    'font' => [
                        0 => '/font',
                        'bahnschrift' => '/bahnschrift.ttf'
                    ]
                ],
                'user' => [
                    0 => '/user',
                    'avatar' => '/avatar',
                    'forum' => '/forum',
                    'PA' => '/PA'
                ],
                'other' => [
                    'xnzx_ofof' => '/bin/新宁中学/2020秋11班/梗/ofof.mp4',
                    'xnzx_qgtyfj' => '/bin/新宁中学/2020秋11班/梗/全国统一放假.mp4'
                ]
            ],
            'static_user' => [
                0 => '/user'
            ],
            /*
            'beian' => [
                'icp' => 'https://beian.miit.gov.cn',
                'gong_an' => 'http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=44078102440947'
            ],
            */
            'sponsors' => [
                'canva' => [
                    0 => 'https://www.canva.com'
                ],
                'bt' => [
                    0 => 'https://www.bt.cn/?invite_code=MV96amRiYWI='
                ],
                'tencent-cloud' => [
                    0 => 'https://curl.qcloud.com/Sj9zH16y'
                ],
                'dnspod' => [
                    0 => 'https://www.dnspod.cn'
                ],
                'rainyun' => [
                    0 => 'https://www.rainyun.com/?ref=16719'
                ],
                'vaptcha' => [
                    0 => 'https://www.vaptcha.com'
                ],
                'ym163com' => [
                    0 => 'https://ym.163.com'
                ],
                'regery' => [
                    0 => 'https://www.regery.com/en?pr=jjjvhib'
                ],
                'spaceship' => [
                    0 => 'https://www.spaceship.com'
                ]
            ],
            'node_modules' => [
                0 => '/_nuxt/node_modules'
            ],
            'other' => [
                'whatIsSu' => 'https://www.youngpioneertours.com/su/',
                'darkmagic' => 'https://test2.jwhgzs.com/fuck',
                'doc_cards' => 'https://p.kdocs.cn/s/2WRKMBQANA',
                'doc_memories' => 'https://p.kdocs.cn/s/FCR2MBQA2U',
                'doc_bible' => 'https://p.kdocs.cn/s/4HF2MBQANA'
            ]
        ];
        
        public static $INF = [
            'year_from' => '2020',
            'year_to' => null
        ];
        public static $SEO_INF = [
            'desc' => '九尾狐工作室，数学、编程、魔方高手云集之地~',
            'keys' => '九尾狐,九尾狐工作室,xf,xf工作室,jwh,jwhgzs,xfgzs,台山市,新宁中学,数学,编程,魔方,2020秋届,11班',
            'urls' => [
                'local://www',
                'local://xnzx',
                'local://forum',
                'local://xfcl',
                'shortUrlPortal://'
            ]
        ];
        public static $CONTACT_INF = [
            'qq' => '725058854',
            'email' => 'admin@jwhgzs.com'
        ];
        public static $VAPTCHA_CONFIG = [
            'status' => true,
            'appId' => 'b89eee6fe2417070bbfecfaf604a752a',
            
            'scenes' => [
                'test' => 0,
                'login' => 1,
                'phoneLogin' => 2,
                'signup' => 3,
                'phoneVerify' => 4,
                'important' => 5,
                'blank' => 6
            ]
        ];
        /** 时代的眼泪 ~
         * Vaptcha 疑似跑路了。
         * 而且最近才发现国内 SMS 政策发生大便。。。
         * 应该是去年七月份的事了，tzy 简直跟断网了一样不知道
         * 难道说，这是九尾狐官网最近 N 个月无人问津之罪魁祸首？
         * ……
         * 于是九尾狐工作室改用阿里的验证服务，
         * 然后蠢猪官网直接上阿里全家桶。
         *                                  -- 2026/3/12 */
        // public static $VAPTCHA_SMS_CONFIG = [
        //     'templateIds' => [
        //         'default' => '1'
        //     ],
            
        //     'sendLimitPerDay' => 5,
        //     'verifyCodeExpTime' => 60 * 10 * 1000
        // ];
        public static $SMS_VERIFY_CONFIG = [
            'countryCode' => '86',
            'sendLimitPerDay' => 5,
            'verifyCodeExpTime' => 60 * 10 * 1000,
            'interval' => 60,
            'duplicatePolicy' => 1,
            'codeLength' => 6,
            'codeType' => 1,
            'caseAuthPolicy' => 1,
            'templateParams' => [
                'code' => '##code##',
                'min' => '__VERIFY_MINUTES__'
            ]
        ];
        public static $STATICCS_CONFIG = [
            // Stupid issue.
            // 'root' => '/www/wwwroot/static.jwhgzs.com'
            'root' => '/www/sites/static.jwhgzs.com/index'
        ];
        
        public static $QUILL_CONFIG = [
        ];
        public static $XNZX_WEEKLY_CONFIG = [
            'tijiMaxLength' => 100,
            'contentMaxLength' => 8000,
            'houjiMaxLength' => 100,
            'basicFormatter' => [
                ',' => '，',
                ':' => '：',
                ';' => '；',
                '?' => '？',
                '!' => '！',
                '(' => '（',
                ')' => '）',
                '\\' => '、',
                '〝' => '"',
                '〞' => '"',
                '″' => '"',
                '＂' => '"',
                '－' => '—',
                '∽' => '~',
                '<<' => '《',
                '>>' => '》'
            ],
            'terms' => ['七上', '七下', '八上', '八下', '九上', '九下']
        ];
        public static $XNZX_PA_CONFIG = [
            'classes' => [ 2020 => [ 11 ] ]
        ];
        public static $FORUM_CONFIG = [
            'classifies' => ['官方区', '首页区', '意见反馈区', '吹水区', '新宁足球冠军联赛区'],
            'defaultClassifyId' => 3,
            'adminClassifyIds' => [0, 1, 4],
            'carouselClassifyId' => 1
        ];

        public static function init() {
            self::$INF['year_to'] = date('Y');
        }
    }

    c::init();
    
    require_once __DIR__ . '/secrets.php';
?>