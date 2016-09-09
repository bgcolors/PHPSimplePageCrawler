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

    public function readRegisterList($file=__DIR__.'/crawlers.conf') {
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
            cli_set_process_title("php crawler {$crawler->name} process");

            swoole_timer_tick($crawler->getInterval(), function($interval) use ($crawler, $process) {
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
                    echo $e->getMessage().PHP_EOL;
                }
            });

        }

        if (empty($this->crawlerList)) {
            throw new Exception("no crawler specified");
        }

        $crawlers = [];
        global $subPids;
        foreach ($this->crawlerList as $k => $v) {
            $process = new swoole_process('process', false, false);
            $process->useQueue();
            $process->crawlerName = $v;
            $pid = $process->start();
            array_push($subPids, $pid);
            $crawlers[$k] = $process;
        }

        function shutdown() {
            global $subPids;
            foreach ($subPids as $pid) {
                if ($pid) {
                    posix_kill($pid, SIGKILL);
                }
            }
            posix_kill(posix_getpid(), SIGKILL);
        }

        pcntl_signal(SIGHUP,  "shutdown");
        pcntl_signal(SIGTERM,  "shutdown");

        cli_set_process_title('php crawler manager process');

        while (true) {
            foreach ($crawlers as $k => $process) {
                echo $process->pop();
            }
        }

    }

}

