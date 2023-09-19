<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;

class ZillowSpider extends BasicSpider
{
  /**
   * @var string[]
   */
  public array $startUrls = [
    'https://www.zillow.com/ok/houses/?userPosition=-112.0390812,41.1427855&userPositionBounds=41.147785500000005,-112.0340812,41.1377855,-112.0440812&currentLocationSearch=true&searchQueryState=%7B%22mapBounds%22%3A%7B%22north%22%3A37.002312%2C%22east%22%3A-94.430662%2C%22south%22%3A33.615787%2C%22west%22%3A-103.002455%7D%2C%22isMapVisible%22%3Atrue%2C%22filterState%22%3A%7B%22sort%22%3A%7B%22value%22%3A%22globalrelevanceex%22%7D%2C%22ah%22%3A%7B%22value%22%3Atrue%7D%2C%22tow%22%3A%7B%22value%22%3Afalse%7D%2C%22mf%22%3A%7B%22value%22%3Afalse%7D%2C%22con%22%3A%7B%22value%22%3Afalse%7D%2C%22apco%22%3A%7B%22value%22%3Afalse%7D%2C%22land%22%3A%7B%22value%22%3Afalse%7D%2C%22apa%22%3A%7B%22value%22%3Afalse%7D%2C%22manu%22%3A%7B%22value%22%3Afalse%7D%7D%2C%22isListVisible%22%3Atrue%2C%22mapZoom%22%3A7%2C%22usersSearchTerm%22%3A%22Oklahoma%22%2C%22regionSelection%22%3A%5B%7B%22regionId%22%3A45%2C%22regionType%22%3A2%7D%5D%2C%22schoolId%22%3Anull%7D'
  ];

  public array $downloaderMiddleware = [
    RequestDeduplicationMiddleware::class,
  ];

  public array $spiderMiddleware = [
    //
  ];

  public array $itemProcessors = [
    //
  ];

  public array $extensions = [
    LoggerExtension::class,
    StatsCollectorExtension::class,
  ];

  public int $concurrency = 2;

  public int $requestDelay = 1;

  /**
   * @return Generator<ParseResult>
   */
  public function parse(Response $response): Generator
  {
    $links = $response->filter('#search-page-list-container')->links();

    foreach ($links as $link) {
      yield $this->request('GET', $link->getUri(), 'parseBlogPage');
    }
  }
}