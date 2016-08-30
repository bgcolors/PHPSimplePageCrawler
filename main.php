<?php
/**
 * Main entrance of the program
 * User: sunshengbo
 * Date: 2016/8/30
 * Time: 11:04
 */

include_once 'Crawler.php';

$crawler_dir = './crawlers';
$d = dir($crawler_dir);
while (false !== ($entry = $d->read())) {
    if ('.php' == substr($entry, -4)) {
        include_once $crawler_dir.DIRECTORY_SEPARATOR.$entry;
    }
}
include_once 'Manager.php';

$manager = new Manager();

$read = $manager->readRegisterList();
foreach ($read as $k => $crawler) {
    $manager->register($crawler);
}
$get = $manager->getCrawlerList();
foreach ($get as $crawler) {
    echo $crawler, ' has been registered', PHP_EOL;
}

try {
    $manager->start();
} catch (Exception $e) {
    echo $e->getMessage(), PHP_EOL;
}


