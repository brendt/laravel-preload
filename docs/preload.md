## Compiling PHP (on Mac)

```
export PATH="/usr/local/opt/bison/bin:$PATH"
export LDFLAGS="-L/usr/local/opt/bison/lib"
export PKG_CONFIG_PATH="$PKG_CONFIG_PATH:/usr/local/opt/libxml2/lib/pkgconfig"
export PKG_CONFIG_PATH="$PKG_CONFIG_PATH:/usr/local/opt/openssl/lib/pkgconfig"

./configure \
    '--prefix=/Users/brent/dev/php/build/php-74' \
    '--with-config-file-path=/Users/brent/dev/php/build/php-74/etc' \
    '--with-config-file-scan-dir=/Users/brent/dev/php/build/php-74/etc/conf.d' \
    '--with-curl=/usr/local/opt/curl-openssl' \
    '--with-layout=GNU' \
    '--with-mysql-sock=/tmp/mysql.sock' \
    '--with-mysqli=mysqlnd' \
    '--with-openssl=shared' \
    '--with-openssl-dir=/usr/local/opt/openssl' \
    '--with-password-argon2=/usr/local/opt/argon2' \
    '--with-pdo-mysql=mysqlnd' \
    '--with-libxml' \
    '--with-pdo-sqlite=/usr/local/opt/sqlite' \
    '--with-sodium=/usr/local/opt/libsodium' \
    '--with-sqlite3=/usr/local/opt/sqlite' \
    '--with-iconv=/usr/local/opt/libiconv' \
    '--enable-bcmath' \
    '--enable-calendar' \
    '--enable-dba' \
    '--enable-exif' \
    '--enable-fpm' \
    '--enable-mysqlnd' \
    '--enable-pcntl' \
    '--enable-mbstring' \
```

## FPM config

```conf
; ./build/php-74/etc/php-fpm.d/www.conf
; …

listen = /Users/brent/dev/php/build/php-74/php-fpm.sock
```

```conf
; ./build/php-74/etc/php-fpm.conf
; …

include=/Users/brent/dev/php/build/php-74/etc/php-fpm.d/*.conf
```

```conf
; ./build/php-74/etc/php.ini
; …

extension=/Users/brent/dev/php/build/php-74/lib/php/<timestamp>/openssl.so
zend_extension=/Users/brent/dev/php/build/php-74/lib/php/<timestamp>/opcache.so

opcache.preload=<project_path>/laravel-preload/preload.php
```

## Nginx config (valet)

```
server {
    listen 80;
    listen [::]:80;
    server_name laravel-preload.test;
    root <project_path>/laravel-preload/public;

    index index.html index.htm index.php;

    charset utf-8;

    location = / {
        try_files $uri /index.php?$query_string;
    }

    location / {
        try_files $uri /index.php?$query_string;
    }

    access_log off;

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/Users/brent/dev/php/build/php-74/php-fpm.sock;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Running php-fpm 

```sh
# ./build/php-74/sbin

./php-fpm --nodaemonize
```
