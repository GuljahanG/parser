<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;
use App\Models\Brand;


class ParseBrand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:parse-brand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = 'https://www.drom.ru/catalog/audi/';
        $html = $this->fetchPageContent($url);
        // Find elements with data-ftid="component_cars-list"
        $brandList = $html->filter('[data-ftid="component_cars-list"]');

        // Extract the href and text from anchor elements within the target element
        $brands = $brandList->filter('a')->each(function (Crawler $node) {
            $link['link'] = $node->attr('href');
            $link['name'] = $node->text();
            return $link;
        });
        // create BrandAudi
        foreach($brands as $brand){
            Brand::create([
                'name' => $brand['name'],
                'link' => $brand['link']
            ]);
        }
    }

    public function fetchPageContent($url){
        $client = new Client();
        $response = $client->get($url,['verify' => false]);
        $html = (string) $response->getBody();

        // Use the Symfony DomCrawler component to parse the HTML
        $crawler = new Crawler($html);

        return $crawler;
    }
}
