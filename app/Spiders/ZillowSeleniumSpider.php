
namespace App\Services\Spiders;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use App\Models\Property;

class ZillowSeleniumSpider
{
    private $startUrl = 'https://www.zillow.com/homes/Your-Search-Area-Here';
    private $driver;

    public function __construct()
    {
        // Start ChromeDriver
        $capabilities = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
    }

    public function scrape()
    {
        // Navigate to Zillow URL
        $this->driver->get($this->startUrl);

        // Extract data using provided selectors
        $listings = $this->driver->findElements(WebDriverBy::cssSelector('.list-card'));

        foreach ($listings as $listingElement) {
            $listing = [];
            $listing['title'] = $listingElement->findElement(WebDriverBy::cssSelector('.list-card-title'))->getText();
            $listing['price'] = $listingElement->findElement(WebDriverBy::cssSelector('.list-card-price'))->getText();
            $details = $listingElement->findElements(WebDriverBy::cssSelector('.list-card-details li'));
            $listing['bedrooms'] = $details[0]->getText();
            $listing['bathrooms'] = $details[1]->getText();
            $listing['area'] = $details[2]->getText();
            $listing['address'] = $listingElement->findElement(WebDriverBy::cssSelector('.list-card-info a'))->getText();
            
            // Store in database
            Property::create($listing);
        }

        // Close the browser window
        $this->driver->quit();
    }
}
