# PHPSimplePageCrawler
Simple php crawler using multiple-process feature of swoole

###Usage
1. install [swoole](https://github.com/swoole/swoole-src)
2. write a custom crawler in `/crawlers`(refer to JYCrawler as an example)
3. add a new line of your custom class in `/crawlers.conf`
4. php main.php
