<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImportZillow extends Command
{
  protected $signature = 'import:zillow';
  protected $description = 'Import property data from multiple sources from CSV into the database';

  public function handle()
  {
    $csvDirectory = storage_path('csv');
    $sources = File::directories($csvDirectory);

    foreach ($sources as $source) {
      $incomingDirectory = $source . DIRECTORY_SEPARATOR . 'incoming';
      $processedDirectory = $source . DIRECTORY_SEPARATOR . 'processed';

      if (!File::exists($processedDirectory)) {
        File::makeDirectory($processedDirectory, 0766, true, true); //$recursive = true, $force = true
      }

      $files = File::files($incomingDirectory);
      // dd($files);





      $fields = \Schema::getColumnListing('properties');
      // dd($fields);


      foreach ($files as $file) {
        if (File::extension($file) === 'csv') {
          $this->info("Processing {$file}...");

          $rows = array_map('str_getcsv', file($file));
          $csv_headers = array_shift($rows);


          // dd($csv_headers);
          // dd($rows);

          //show me the first line 
          // dd($rows[1]);


          // test if $rows[0][0] is a json string
          // dd(json_decode($rows[0][0], true));







          foreach ($rows as $row) {
            // dd($row);

            $data = []; // reset the data array for each row



            $addressJson = json_decode($row[0], true);
            $eventJson = json_decode($row[1], true);
            // dd($addressJson);
            // dd($eventJson);
            /* 
              $eventJson = {"@type":"Event","@context":"http://schema.org","name":"3D Tour Available - 2 Dogwood Loop Ave","url":"https://www.zillow.com/homedetails/2-Dogwood-Loop-Ave-Ocala-FL-34472/87647859_zpid/","image":"https://photos.zillowstatic.com/fp/935f2eceb638b455a3976c823474f963-p_e.jpg","startDate":"2023-09-20","endDate":"2023-09-20","eventAttendanceMode":"https://schema.org/OnlineEventAttendanceMode","eventStatus":"https://schema.org/EventScheduled","location":[{"@type":"VirtualLocation","url":"https://www.zillow.com/homedetails/2-Dogwood-Loop-Ave-Ocala-FL-34472/87647859_zpid/"},{"@type":"Place","@context":"http://schema.org","name":"2 Dogwood Loop Ave","address":{"@type":"PostalAddress","@context":"http://schema.org","streetAddress":"2 Dogwood Loop Ave","postalCode":"34472","addressLocality":"Ocala","addressRegion":"FL"}}],"offers":{"price":293000,"priceCurrency":"$","availability":"http://schema.org/InStock","url":"https://www.zillow.com/homedetails/2-Dogwood-Loop-Ave-Ocala-FL-34472/87647859_zpid/","validFrom":"2023-09-20"},"performer":"Opendoor Brokerage LLC"}
            */

            // dd($addressJson); 
            // dd($eventJson['offers']['price']);


            // Map All  Data
            $data['address'] = $addressJson['address']['streetAddress'] ?? '';
            $data['city'] = $addressJson['address']['addressLocality'] ?? '';
            $data['state'] = $addressJson['address']['addressRegion'] ?? '';
            $data['zip'] = $addressJson['address']['postalCode'] ?? '';
            $data['url'] = $addressJson['url'] ?? '';

            // geo
            $data['geo'] = json_encode([
              'latitude' => $addressJson['geo']['latitude'] ?? '',
              'longitude' => $addressJson['geo']['longitude'] ?? '',
            ]);

            // $data['parcel_id'] = '';

            // price 

            //$eventJson['offers']['price']   Trying to access array offset on value of type null 
            if (is_array($eventJson) && isset($eventJson['offers']) && is_array($eventJson['offers']) && isset($eventJson['offers']['price'])) {
              $data['price'] = str_replace(['$', ','], '', $eventJson['offers']['price']);
            }

            // thumbnail_url
            if (is_array($eventJson) && isset($eventJson['image'])) {
              $data['thumbnail_url'] = $eventJson['image'];
            }

            //area
            $data['area'] = $addressJson['floorSize']['value'] ?? '';

            // bed / bath
            $data['bedrooms'] = $row[7] ?? '';
            $data['bathrooms'] = $row[9] ?? '';

            //source
            $data['source'] = 'zillow';



            // dd($data);


            Property::updateOrCreate(
              [
                'address' => $data['address'],
                'city' => $data['city'],
                'state' => $data['state'],
                'zip' => $data['zip'],
              ],
              $data
            );
          }

          File::move($file, $processedDirectory . DIRECTORY_SEPARATOR . File::basename($file));
          $this->info("Finished processing {$file} and moved to processed directory.");
        }
      }
    }

    $this->info('All CSV files processed and data imported into database.');
  }
}
