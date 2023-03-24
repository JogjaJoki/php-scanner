<?php
    require_once './CrawlClass.php';
    require_once './simple_html_dom.php';

    class SqliDetector {
        private $url = '';
        private $type = '';
        private $method = '';
        private $text = '';
        private $payload;
        private $start;
        private $end;
        private $opt;
        private $res;
        private $conn;
        private $inband;
        private $blind;
        private $htmlObject;
        private $vulnerability = array();

        function __construct() {
        }

        private function make_inband_get_request($u){
            $this->conn = curl_init($u);
            curl_setopt($this->conn, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->conn, CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($this->conn, CURLOPT_TIMEOUT, 200);
            curl_setopt($this->conn, CURLOPT_HEADER, 1);
            curl_setopt($this->conn, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($this->conn, CURLOPT_REFERER, "http://google.com");
            curl_setopt($this->conn, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');
            $this->res = curl_exec($this->conn);
            $this->htmlObject = str_get_html($this->res);
        }

        private function make_inband_post_request($u){
            $this->conn = curl_init($u);
            curl_setopt($this->conn, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->conn, CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($this->conn, CURLOPT_TIMEOUT, 200);
            curl_setopt($this->conn, CURLOPT_HEADER, 1);
            curl_setopt($this->conn, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($this->conn, CURLOPT_REFERER, "http://google.com");
            curl_setopt($this->conn, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');
            curl_setopt($this->conn, CURLOPT_POST, 1);
            curl_setopt($this->conn, CURLOPT_POSTFIELDS, $this->opt);
            $this->res = curl_exec($this->conn);
            $this->htmlObject = str_get_html($this->res);
        }

        private function make_blind_get_request($u){
            $this->start = time();
            $this->conn = curl_init($u);
            curl_setopt($this->conn, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->conn, CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($this->conn, CURLOPT_TIMEOUT, 200);
            curl_setopt($this->conn, CURLOPT_HEADER, 1);
            curl_setopt($this->conn, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($this->conn, CURLOPT_REFERER, "http://google.com");
            curl_setopt($this->conn, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');
            $this->res = curl_exec($this->conn);
            $this->htmlObject = str_get_html($this->res);
        }

        private function make_blind_post_request($u){
            $this->start = time();
            $this->conn = curl_init($u);
            curl_setopt($this->conn, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->conn, CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($this->conn, CURLOPT_TIMEOUT, 200);
            curl_setopt($this->conn, CURLOPT_HEADER, 1);
            curl_setopt($this->conn, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($this->conn, CURLOPT_REFERER, "http://google.com");
            curl_setopt($this->conn, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');
            curl_setopt($this->conn, CURLOPT_POST, 1);
            curl_setopt($this->conn, CURLOPT_POSTFIELDS, "id=%27-sleep%285%29%23#");
            $this->res = curl_exec($this->conn);
            $this->htmlObject = str_get_html($this->res);
        }

        public function setPayload($payload){
            $this->payload = $payload;
        }

        private $sql_error = array(
            'Division by zero in',
            'supplied argument is not a valid MySQL result resource in',
            'Call to a member function',
            'Microsoft JET Database','ODBC Microsoft Access Driver',
            'Microsoft OLE DB Provider for SQL Server',
            'Unclosed quotation mark',
            'Microsoft OLE DB Provider for Oracle',
            '[Macromedia][SQLServer JDBC Driver][SQLServer]Incorrect',
            'Incorrect syntax near',
            'you have an error in your sql syntax;',
            'warning: mysql',
            'unclosed quotation mark after the character string',
            'quoted string not properly terminated'
        );

        public function setUrl($url){
            $this->url = $url;
        }

        public function setType($type){
            $this->type = $type;
        }

        public function setMethod($method){
            $this->method = $method;
        }

        public function setOption($opt){
            $this->opt = $opt;
        }

        public function execute(){
            $this->setPayload("'");
            //echo "[-] Tes inband injection ( GET Method )\n";
            $this->make_inband_get_request($this->url . $this->payload);
            if($this->res){
                foreach($this->sql_error as $error) {
                    $e = strtolower($error);
                    if(preg_match("/$e/", strtolower($this->res))) {
                        array_push($this->vulnerability, array("link" => $this->url, "type" => "inband", "method" => "get", "error" => $e, "payload" => $this->payload));
                    }
                }
            }
            $this->setOption("id='");
            //echo "[-] Tes inband injection ( POST Method )\n";
            $this->make_inband_post_request($this->url);
            if($this->res){
                foreach($this->sql_error as $error) {
                    $e = strtolower($error);
                    if(preg_match("/$e/", strtolower($this->res))) {
                        array_push($this->vulnerability, array("link" => $this->url, "type" => "inband", "method" => "post", "error" => $e, "payload" => $this->opt));
                    }
                }
            }     
            $this->setPayload("%27-sleep%285%29%23#");
            //echo "[-] Tes blind injection ( GET Method )\n";
            $this->make_blind_get_request($this->url . $this->payload);
            if($this->res){
                $this->end = time();
                if($this->end - $this->start > 9){
                    array_push($this->vulnerability, array("link" => $this->url, "type" => "blind", "method" => "get", "payload" => $this->payload));
                }
            }
            $this->setOption("id=%27-sleep%285%29%23#");
            //echo "[-] Tes blind injection ( POST Method )\n";
            $this->make_blind_post_request($this->url);
            if($this->res){
                $this->end = time();
                if($this->end - $this->start > 9){
                    array_push($this->vulnerability, array("link" => $this->url, "type" => "blind", "method" => "post", "payload" => $this->opt));
                }
            } 

            return $this->vulnerability;
        }
    }
