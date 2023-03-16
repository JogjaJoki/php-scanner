<?php
    use GuzzleHttp\Exception\RequestException;

    use Psr\Http\Message\UriInterface;
    use Psr\Http\Message\ResponseInterface;

    use Spatie\Crawler\Crawler;
    use Spatie\Crawler\CrawlObservers\CrawlObserver;

    require_once 'vendor/autoload.php';

    class Observer extends CrawlObserver {
        public $count = 1;
        public function willCrawl(UriInterface $url) { 
            echo "Start Crawling " . $url . "\n";
        }

        public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null): void {
            $path = $url->getPath();
            $doc = new \DOMDocument();
            @$doc->loadHTML($response->getBody());

            echo $this->count . " : " . $url . "\n";
            $this->count++;
        }

        public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null): void { }

        public function finishedCrawling() { }
    }

    class CrawlClass {       
        function __construct($url) {
            Crawler::create()
            ->setCrawlObserver(new Observer())
            ->ignoreRobots()
            ->setConcurrency(3)
            ->setMaximumDepth(10)
            ->acceptNofollowLinks()
            ->setDelayBetweenRequests(500)
            ->setParseableMimeTypes(['text/html'])
            ->startCrawling($url);
        }
    }