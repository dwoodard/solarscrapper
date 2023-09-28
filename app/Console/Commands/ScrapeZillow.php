<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Spiders\ZillowSpider;
use App\Models\Property;
use RoachPHP\Http\Request;

class ScrapeZillow extends Command
{
  protected $signature = 'scrape:zillow';
  protected $description = 'Scrape Zillow properties and save to database';

  public function handle()
  {
    $spider = new ZillowSpider();

    foreach ($spider->startRequests() as $request) {
      $response = $request->send();

      foreach ($spider->parse($response) as $item) {
        Property::create([
          'title' => $item['title'],
          'price' => $item['price'],
          'bedrooms' => $item['bedrooms'],
          'bathrooms' => $item['bathrooms'],
          'area' => $item['area'],
          'address' => $item['address'],

        ]);
      }
    }

    $this->info('Properties scraped and saved to database.');
  }
}
