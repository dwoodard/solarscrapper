<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;

class ZillowSpider extends BasicSpider
{
  // Define the starting URL for Zillow's search results page
  public array $startUrls = [
    'https://www.zillow.com/ok/houses/',
  ];

  public function parse(Response $response): \Generator
  {
    // Select elements using CSS selectors to extract data
    $listingElements = $response->filter('.list-card');

    foreach ($listingElements as $listingElement) {
      // Extract data from each listing
      $listing = $this->extractListingData($listingElement);

      // Yield the listing data
      yield $this->item($listing);
    }
  }

  // Helper method to extract data from a single listing element
  private function extractListingData($listingElement): array
  {
    $listing = [];

    // Extract data using CSS selectors
    $listing['title'] = $listingElement->filter('.list-card-title')->text();
    $listing['price'] = $listingElement->filter('.list-card-price')->text();
    $listing['bedrooms'] = $listingElement->filter('.list-card-details li')->eq(0)->text();
    $listing['bathrooms'] = $listingElement->filter('.list-card-details li')->eq(1)->text();
    $listing['area'] = $listingElement->filter('.list-card-details li')->eq(2)->text();
    $listing['address'] = $listingElement->filter('.list-card-info a')->text();

    dump($listing);

    return $listing;
  }
}

// Usage
// $spider = new ZillowSpider();
