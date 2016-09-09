# PHPSimplePageCrawler
Simple php crawler using multiple-process feature of swoole

###Usage
1. install [swoole](https://github.com/swoole/swoole-src)
2. PHP need `pcntl` `posix` extension support
3. Write a custom crawler in `/crawlers`(refering to JYCrawler as an example)
4. Add a new line of your custom class in `/crawlers.conf`
5. `php main.php`
