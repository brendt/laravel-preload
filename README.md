This Laravel project demonstrates how preloading can be used to preload the whole Laravel framework. Unfortunately, it results in a segfault.

## Setup

```
git clone git@github.com:brendt/laravel-preload.git
cd laravel-preload
composer install
```


Configure `opcache.preload` in your PHP's ini file to [`preload.php`](./preload.php), 
and restart php-fpm or whatever server you're using.
