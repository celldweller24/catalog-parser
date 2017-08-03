<?php

/**
 * Created by PhpStorm.
 * User: celldweller
 * Date: 7/24/17
 * Time: 8:42 PM
 */
namespace App;

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Sunra\PhpSimple\HtmlDomParser;

class contentParse
{
  /**
   * Source url.
   * @var string
   */
  private $url;

  /**
   * set url.
   * @param string $url
   */
  private function setUrl($url) {
    $this->url = $url;
  }

  /**
   * get url.
   * @return string
   */
  protected function getUrl() {
    return $this->url;
  }

  /**
   * start function.
   * @param string $url
   */
  public function run($url) {
    $this->setUrl($url);
    $this->collectContent();
    $this->writeSrcList();
  }

  /**
   * get html markup of source.
   * @return string
   */
  protected function getSourceMarkup() {
    $ch = curl_init($this->url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
  }

  protected function collectContent() {
    $elementSource = [];
    $dom = HtmlDomParser::str_get_html($this->getSourceMarkup());
    foreach ($dom->find('img') as $node) {
      print_r($node->src);
    }
  }

  public function writeSrcList() {

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


}