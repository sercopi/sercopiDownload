<?php


class Scrapper
{
    protected $curl;
    public function __construct()
    {
        $this->curl = curl_init();
    }
    public function wget($url)
    {
        curl_setopt(
            $this->curl,
            CURLOPT_URL,
            $url
        );
        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        $User_Agent = 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31';
        $request_headers = array();
        $request_headers[] = 'User-Agent: ' . $User_Agent;
        $request_headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $request_headers);
        return curl_exec($this->curl);
    }
}
