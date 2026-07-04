# About update cycle on Github

`Not specified! Once a month is normal for sure.`

I wrote these ↑ stupid words almost 3 years ago. How time flies. My English boost. My ability to produce bugs too. Thanks to Claude, Deepseek, Kimi, ... —— me, 2026/7/4

# Requirement

- you run this project on `PHP 8.x`
- you have correct values in those config files. (esp. `$STATICCS_CONFIG`)
- you have proper cross-site settings
- you have installed the following extensions (actually just a few of that are needed😅)
  - `bcmath, ftp, gd, gettext, intl, mysqli, pcntl, pdo_mysql, shmop, soap, sockets, sysvsem, xmlrpc, zip, exif, igbinary, imagick, apcu, memcached, opcache, redis, bc, image, dom, iconv, mbstring, mysqlnd, openssl, pdo, tokenizer, xml, curl, bz2, yaf, imap, xdebug, swoole, pdo_pgsql, fileinfo, pgsql, calendar, gmp`
- you have set pseudo-static rules according to `pseudo-static.txt`
- you have adjusted your PHP & Nginx/OpenResty config to allow file uploading (limit file size as well)
