<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use \App\Models\Brand;
use Symfony\Component\DomCrawler\Crawler;

class ScrapeModelController extends Controller
{
    public function fetchModels(){
        $url = 'https://www.drom.ru/catalog/audi/';
        $html = $this->fetchPageContent($url);
        // Find elements with data-ftid="component_cars-list"
        $brandList = $html->filter('[data-ftid="component_cars-list"]');

        // Extract the href and text from anchor elements within the target element
        $brands = $brandList->filter('a')->each(function (Crawler $node, $i) {
            $link['name'] = $node->attr('href');
            $link['link'] = $node->text();
            return $link;
        });

        foreach($brands as $brand){
            Brand::create([
                'name' => $brand['name'],
                'link' => $brand['link']
            ]);
        }
        dd($brands);
    }

    public function fetchGeneration(){

        $url = 'https://www.drom.ru/catalog/audi/50/';
        $html = $this->fetchPageContent($url);
        // Find elements with the class "css-pyemnz"
        $elements = $html->filter('.css-pyemnz');
        $brandTextContent = $elements->filter('.css-pyemnz .css-112idg0')->text();
        if($brandTextContent){
            preg_match('/для\s+(\S+)/u', $brandTextContent, $matches);
            // Check if matches were found
            if (!empty($matches)) { $brandTextContent = $matches[0]; }
        }
     

        $links = $elements->filter('a')->each(function (Crawler $node, $i) {
            $link['href'] = $node->attr('href');
            $link['img_link'] = $node->filter('img')->attr('src');
            $link['name_period'] = $node->filter('[data-ftid="component_article_caption"]> span')->text();
            $link['generation'] = $node->filter('div[data-ftid="component_article_extended-info"] > div:first-child')->text();
            return $link;
        });
        dd($links);
    
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
