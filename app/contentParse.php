<?php

/**
 * Created by PhpStorm.
 * User: celldweller
 * Date: 7/24/17
 * Time: 8:42 PM
 */
namespace App;

use Sunra\PhpSimple\HtmlDomParser;

define('PUBLIC_PATH', '/var/www/catalogparser');

class contentParse
{
  private $url;

  private $pathToCatalog;

  private $targetSections;

  public function getSpecConfig() {
    $options = json_decode(file_get_contents(PUBLIC_PATH . '/config.json'));
    try {
      if (!$options) {
        throw new \Exception('Configs wasn\'t extracted from config.json');
      }
    }
    catch(Exception $e) {
      echo $e->getMessage();
      return false;
    }

    $this->url = $options->url;
    $this->pathToCatalog = $options->path_to_catalog;

    $targetUrls = [];
    foreach ($options->target_urls as $key => $target_url) {
      $targetUrls[$key] = $target_url;
    }
    $this->targetSections = $targetUrls;
  }

  private function getCatalogRoot() {
    $urlLength = strlen($this->pathToCatalog);
    if (($urlLength - 1) == strripos($this->pathToCatalog, '/')) {
      $this->pathToCatalog = substr($this->pathToCatalog, 0, $urlLength - 1);
    }
    $catalogUri = explode('/', $this->pathToCatalog);
    return end($catalogUri);
  }

  protected function getSourceMarkup($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
  }

  private function collectElementUrls() {
    $allElementUrls = [];
    foreach ($this->targetSections as $sectionName => $targetSection) {
      $sectionMarkup = $this->getSourceMarkup($targetSection);
      $html = HtmlDomParser::str_get_html($sectionMarkup);
      if (!empty($html->find('ul.pager li.pager-item'))) {
        $pages = count($html->find('ul.pager li.pager-item'));
      }
      $viewBoardsElements = $html->find('.panel-panel .view-board-all .view-content div');
      foreach ($viewBoardsElements as $viewBoardsElement) {
        if (!is_null($viewBoardsElement->children(0)) && !is_null($viewBoardsElement->children(0)->children(1))) {
          $allElementUrls[] = $viewBoardsElement->children(0)->children(1)->children(0)->children(0)->children(0)->attr['href'];
        }
      }

      if (isset($pages)) {
        for ($i = 1; $i <= $pages; $i++) {
          $sectionMarkup = $this->getSourceMarkup($targetSection . '&page=' . $i);
          $html = HtmlDomParser::str_get_html($sectionMarkup);
          if (!empty($html->find('ul.pager li.pager-item'))) {
            $pages = count($html->find('ul.pager li.pager-item'));
          }
          $viewBoardsElements = $html->find('.panel-panel .view-board-all .view-content div');
          foreach ($viewBoardsElements as $viewBoardsElement) {
            if (!is_null($viewBoardsElement->children(0)) && !is_null($viewBoardsElement->children(0)->children(1))) {
              $allElementUrls[] = $viewBoardsElement->children(0)->children(1)->children(0)->children(0)->children(0)->attr['href'];
            }
          }
        }
      }
    }
    return $allElementUrls;
  }

  protected function collectCatalogElement() {
    $elementsData = [];
    $elementUrls = $this->collectElementUrls();
    //$elementUrls = ['board/sale/konina-pch-bk-296551', 'board/sale/konina-pch-bk-296551'];
    $count = 1;
    foreach ($elementUrls as $elementUrl) {
      $elementMarkup = $this->getSourceMarkup($this->url . $elementUrl);
      $html = HtmlDomParser::str_get_html($elementMarkup);
      $contentWrapper = $html->find('.region-content #block-system-main')[0]->children(0);

      $id = explode('-', $contentWrapper->children(0)->attr['id']);
      $id = end($id);

      $title = $html->find('h1', 0)->plaintext;
      $postDate = $contentWrapper->find('.submitted span')[0]->attr['content'];

      $description = "";
      if (!empty($contentWrapper->find('.content .field-name-body'))) {
        $description = $contentWrapper->find('.content .field-name-body .field-items .field-item p')[0]->plaintext;
      }

      $price = "";
      if (!empty($contentWrapper->find('.content .field-name-field-sale-price'))) {
        $price = $contentWrapper->find('.content .field-name-field-sale-price .field-items .field-item')[0]->plaintext;
      }

      $address = "";
      if (!empty($contentWrapper->find('.content .field-name-field-sale-town'))) {
        $address = $contentWrapper->find('.content .field-name-field-sale-town .field-items .field-item')[0]->plaintext;
      }

      $contacts = "";
      if (!empty($contentWrapper->find('.content .field-name-field-sale-contacts'))) {
        $contacts = $contentWrapper->find('.content .field-name-field-sale-contacts .field-items .field-item')[0]->plaintext;
      }

      $email = "";
      if (!empty($contentWrapper->find('.content .field-name-field-email'))) {
        $email = $contentWrapper->find('.content .field-name-field-email .field-items .field-item a')[0]->plaintext;
      }

      $images = [];
      if (!empty($contentWrapper->find('.content .field-name-field-sale-photo'))) {
        foreach ($contentWrapper->find('.content .field-name-field-sale-photo .field-items')[0]->children() as $imageWrapper) {
          $images[] = $imageWrapper->find('img')[0]->src;
        }
      }

      $elementsData[] = [
        'id' => $id,
        'title' => $title,
        'post_date' => $postDate,
        'description' => $description,
        'price' => $price,
        'address' => $address,
        'contacts' =>  $contacts,
        'email' => $email,
        'images' => $images
      ];

      $count++;

      // Saving images to data/images folder.
      $this->downloadImages($images, $id);
    }
    print 'Images has been downloaded successfully' . "\r\n";

    // Writing array of data into /data/elementsData.json.
    $this->writeElementList($elementsData);
    print 'The data of catalog elements has been parsed successfully. Amount of elements: ' . $count . "\r\n";
  }

  private function downloadImages(array $imageUrls, $id) {
    foreach ($imageUrls as $imageUrl) {
      $token = '';
      if (strpos($imageUrl, 'itok')) {
        $token = explode('itok=', $imageUrl);
        $token = end($token);
        $imageUrl = preg_replace('(\?itok=[\w\S\d]+)', '', $imageUrl);
      }

      $imageName = explode('/', $imageUrl);
      $imageName = end($imageName);

      $ch = curl_init($imageUrl);
      $fp = fopen(PUBLIC_PATH . '/data/images/' . $id . '-' . $token . $imageName, 'wb');
      try {
        if (!$fp) {
          throw new \Exception('The image ' . $imageName . ' can\'t be created for node ' . $id);
        }
      }
      catch(Exception $e) {
        echo $e->getMessage();
        return false;
      }
      curl_setopt($ch, CURLOPT_FILE, $fp);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_exec($ch);
      curl_close($ch);
      fclose($fp);
    }
  }

  private function writeElementList(array $elementsData) {
    if (!empty($elementsData)) {
      $json = json_encode($elementsData);
      try {
        if (file_put_contents(PUBLIC_PATH . '/data/elementsData.json', $json) == false) {
          throw new \Exception('Data weren\'t wrote into elementsData.json');
        }
      }
      catch(Exception $e) {
        echo $e->getMessage();
        return false;
      }
    }
  }

  public function run() {
    $this->getSpecConfig();
    $this->collectCatalogElement();
  }
}