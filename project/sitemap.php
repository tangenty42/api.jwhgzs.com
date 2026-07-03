<?php header('Content-type: application/xml') ?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php
        /* sitemap 生成 */
        
        function root_of($url) {
            $parsed = parse_url($url, PHP_URL_HOST);
            return strtolower(implode('.', array_slice(explode('.', $parsed ? $parsed : $url), -2)));
        }
        function pass($url = '') {
            return root_of($url) == root_of($_GET['site']);
        }
        function go1($loc = '', $lastmod = 0, $changefreq = 'daily', $priority = '0.8') {
            $lastmod = $lastmod === 0 ? date('Y-m-d') : $lastmod;
            echo('<url><loc>' . $loc . '</loc><lastmod>' . $lastmod . '</lastmod><changefreq>' . $changefreq . '</changefreq><priority>' . $priority . '</priority></url>');
        }
        
        foreach (c::$SEO_INF['urls'] as $v)
            if (pass(u($v)))
                go1(u($v));
    ?>
</urlset>
