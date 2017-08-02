<?php

/**
 * Created by PhpStorm.
 * User: celldweller
 * Date: 7/24/17
 * Time: 8:42 PM
 */
namespace App;

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Masterminds\HTML5;

class imageParse
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
    $this->writeSrcList();
  }

  /**
   * get html markup of source.
   * @return string
   */
  protected function getContent() {
    $ch = curl_init($this->url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
  }

  public function writeSrcList() {
    $elementSource = [];
    $dom = new HTML5();
    $temp = $dom->loadHTML($this->getContent());
    //var_dump($temp->getElementsByTagName('img'));
    foreach ($temp->getElementsByTagName('img') as $node) {
      print_r($node->getAttribute('src'));
    }
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