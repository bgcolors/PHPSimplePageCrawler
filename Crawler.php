<?php

/**
 * Crawler class: base class of crawlers, all crawlers must implement this class.
 * User: sunshengbo
 * Date: 2016/8/30
 * Time: 10:27
 */
class Crawler {

    private $interval = 5000000;

    public $name = '';

    public function setConfig(){

    }

    public function getContent() {
        return '';
    }

    public function parse($content) {
        return [];
    }

    public function each($obj){
        return $this->name.' has got an item: '.$obj.PHP_EOL;
    }

    public function getInterval() {
        return $this->interval;
    }

}