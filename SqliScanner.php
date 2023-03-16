<?php
    require_once 'CrawlClass.php';

    class SqliScanner {
        private $url = '';
        private $text = '';
        function __construct($u, $inband = true, $blind = true) {
            $this->url = $u;
            $this->send($u);
            //new CrawlClass($url);
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

        public function send($host) {
            $conn = curl_init($host . "'");
            curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($conn, CURLOPT_TIMEOUT, 200);
            curl_setopt($conn, CURLOPT_HEADER, 1);
            curl_setopt($conn, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($conn, CURLOPT_REFERER, "http://google.com");
            curl_setopt($conn, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');
            $res = curl_exec($conn);
            if($res){
                foreach($this->sql_error as $error) {
                    $e = strtolower($error);
                    if(preg_match("/$e/", strtolower($res))) {
                        $this->text =  "[+] SQL Injection vulnerable detected : $this->url\n";
                    }else{
                    }
                }
                print $this->text;
            } else {
                print false;
            }
        }
    }

    new SqliScanner('http://testphp.vulnweb.com/artists.php?artist=3');