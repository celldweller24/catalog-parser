<?php

/**
 * Created by PhpStorm.
 * User: celldweller
 * Date: 7/24/17
 * Time: 8:42 PM
 */
namespace App;

use Sunra\PhpSimple\HtmlDomParser;

class contentParse
{
  private $url;

  private $pathToCatalog;

  private $targetSelectors;

  public function getSpecConfig() {
    $options = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/config.json'));
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
    $selectorsArray = [];
    foreach ($options->selectors as $key => $selector) {
      $selectorsArray[$key] = $selector;
    }
    $this->targetSelectors = $selectorsArray;
  }

  private function getAllTargetPages() {
    $pagesList = [];
    $urlLength = strlen($this->pathToCatalog);
    if (($urlLength - 1) == strripos($this->pathToCatalog, '/')) {
      $this->pathToCatalog = substr($this->pathToCatalog, 0, $urlLength - 1);
    }
    $catalogUri = explode('/', $this->pathToCatalog);
  }

  protected function getSourceMarkup() {
    $ch = curl_init($this->url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
  }

  protected function collectCatalogElement() {
    $elementSource = [];
    $dom = HtmlDomParser::str_get_html($this->getSourceMarkup());
    foreach ($dom->find('img') as $node) {
      print_r($node->src);
    }
  }

  private function writeElementList(array $targetSelectors) {

    /*foreach ($dom->getElementsByTagName('img') as $node) {
      if (!preg_match('/^(http:\/\/|https:\/\/)/', $node->getAttribute('src'))) {
        $srcArray[] = $node->getAttribute('src');
      }
    }*/

    /*if (!empty($srcArray)) {
      $json = json_encode($srcArray);
      try {
        if (file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/parseList.json', $json) == false) {
          throw new \Exception('Data weren\'t wrote into imagesList.json');
        }
      }
      catch(Exception $e) {
        echo $e->getMessage();
        return false;
      }
    }*/
  }

  public function run() {
    $this->getSpecConfig();
    //$this->writeSrcList();
  }
}