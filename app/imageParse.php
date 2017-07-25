<?php

/**
 * Created by PhpStorm.
 * User: celldweller
 * Date: 7/24/17
 * Time: 8:42 PM
 */
namespace App;

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

class imageParse
{
  /**
   * @var string
   *
   * Source url.
   */
  protected $url;

  /**
   * @var string
   *
   * Directory of parsed images.
   */
  private $directory;

  public function setUrl($url) {
    $this->url = $url;
  }

  public function setDirectory($directory) {
    $this->directory = $directory;
  }

  public function getHtml() {
    $ch = curl_init($this->url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $content = curl_exec($ch);
    var_dump($content);
    curl_close($ch);
  }

  public function getImgReferenses() {
    $dom = new \DOMDocument;
    $dom->loadHTML($this->url);
      var_dump($dom->getElementsByTagName('a'));
    /*foreach ($dom->getElementsByTagName('a') as $node) {
      echo $dom->saveHtml($node), PHP_EOL;
    }*/
  }


}