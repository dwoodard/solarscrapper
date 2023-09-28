<?php

namespace App\Services;

use Exception;

class CsvTransformer
{
  public function transformZillowData($filePath)
  {
    $transformedData = [];

    // Open the CSV file for reading
    if (($handle = fopen($filePath, "r")) !== FALSE) {
      // Read the headers (first row)
      $headers = fgetcsv($handle, 1000, ",");

      while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $data = [];

        // Extract data from the JSON column
        try {
          $jsonData = json_decode($row[0], true);
          $data['title'] = $jsonData['name'] ?? '';
          $data['address'] = $jsonData['address']['streetAddress'] ?? '';
          $data['url'] = $jsonData['url'] ?? '';
        } catch (Exception $e) {
          // Handle JSON parsing errors, if any
        }

        // Map other fields with data type checks
        $data['price'] = str_replace(['$', ','], '', $row[4]);
        $data['bedrooms'] = str_replace('bds', '', trim($row[6]));
        $data['bathrooms'] = str_replace('ba', '', trim($row[7]));
        $data['area'] = str_replace(['sqft', ','], '', trim($row[8]));

        $transformedData[] = $data;
      }
      fclose($handle);
    }

    return $transformedData;
  }
}
