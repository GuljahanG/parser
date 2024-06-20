<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;
use \App\Models\Brand;
use \App\Models\Generation;

class ParseGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:parse-generation';

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
        //Will be better to use Laravel Horizon
        $brands = Brand::get();
        foreach($brands as $brand){
            $url = $brand->link;
            $html = $this->fetchPageContent($url);
            // Find elements with the class "css-pyemnz"
            $elements = $html->filter('.css-pyemnz');
            $brandTextContent = $elements->filter('.css-pyemnz .css-112idg0')->text();
            if($brandTextContent){
                preg_match('/для\s+(\S+)/u', $brandTextContent, $matches);
                // Check if matches were found
                if (!empty($matches)) { $brandTextContent = $matches[0]; }
            }
        
            $elements->filter('a')->each(function (Crawler $node, $i) use ($brand, $brandTextContent) {
                $generation['brand_id'] = $brand->id ?? null;
                $generation['market'] = $brandTextContent ?? null;
                $generation['link'] = $node->attr('href') ?? null;
                $generation['img_link'] = $node->filter('img')->attr('src') ?? null;
                $generation['name_period'] = $node->filter('[data-ftid="component_article_caption"]> span')->text() ?? null;
                $generation['generation'] = $node->filter('div[data-ftid="component_article_extended-info"] > div:first-child')->text() ?? null;
                Generation::create($generation);
            });
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
