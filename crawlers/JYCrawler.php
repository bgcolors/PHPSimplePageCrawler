<?php
class JYCrawler extends Crawler {

    private $interval = 2000000;

    protected $name = 'Jinya';

    public function setConfig(){
        $this->url = 'http://118.145.4.149:16914/hqweb/hq/hqV_gnnt.jsp';
    }

    public function getContent() {
        $content = file_get_contents($this->url);
        return false ===  $content? '' : utf8_encode($content);
    }

    public function parse($content) {
        $tmp = explode(PHP_EOL, $content);
        $result = [];
        foreach($tmp as $v){
            if (!empty($v) && strpos($v, ',')) {
                array_push($result, $v);
            }
        }
        return empty($result) ? [] : $result;
    }

    public function each($obj){
        $tmp = explode(',', $obj);
        if (empty($tmp)) {
            return 'item parse fail'.PHP_EOL;
        }

        $result = [];
        $result['code'] = $tmp[1];
        $result['name'] = $tmp[0];
        $result['last'] = $tmp[7];
        $result['high'] = $tmp[5];
        $result['low'] = $tmp[6];
        $result['open'] = $tmp[4];
        $result['lastClose'] = $tmp[2];
        $result['volume'] = 1;
        $result['quoteTime'] = $tmp[39];
        $result['swing'] = 13;
        $result['swingRate'] = number_format($result['swing'] / $result['open'] * 100, 2, '.', '').'%';
        return parent::each(json_encode($result));
    }

    public function getInterval() {
        return $this->interval;
    }
}