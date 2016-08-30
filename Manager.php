<?php

/**
 * Manager Class: registers crawler here and forks a process per crawler.
 * User: sunshengbo
 * Date: 2016/8/30
 * Time: 9:17
 */

class Manager {

    private $crawlerList = [];

    public function __construct() {

    }

    public function register($crawler) {
        if (class_exists($crawler)) {
            array_push($this->crawlerList, $crawler);
        }
    }

    public function readRegisterList($file='crawlers.conf') {
        $fp = fopen($file, 'r+');
        if ($fp === false) {
            throw new Exception("can not open $file");
        }

        $crawlers = [];
        while ($crawler = fgets($fp, 20)) {
            array_push($crawlers, trim($crawler));
        }

        if (!feof($fp)) {
            throw new Exception("$file read error");
        }

        fclose($fp);

        return $crawlers;
    }

    public function getCrawlerList() {
        return $this->crawlerList;
    }

    public function start() {

        function process(swoole_process $process) {

            $crawler = new $process->crawlerName;

            while (true) {
                try {
                    $crawler->setConfig();
                    $content = $crawler->getContent();
                    $objs = $crawler->parse($content);

                    if (is_array($objs) && !empty($objs)) {
                        foreach ($objs as  $obj) {
                            $process->push($crawler->each($obj));
                        }
                    }
                } catch (Exception $e) {
                    echo $e->getMessage(), PHP_EOL;
                }
                usleep($crawler->getInterval());
            }

        }

        if (empty($this->crawlerList)) {
            throw new Exception("no crawler specified");
        }

        $crawlers = [];
        foreach ($this->crawlerList as $k => $v) {
            $process = new swoole_process('process', false, false);
            $process->useQueue(0, 1);
            $process->crawlerName = $v;
            $process->start();
            $crawlers[$k] = $process;
        }

        while (true) {
            foreach ($crawlers as $k => $process) {
                echo $process->pop();
            }
        }

    }

}

