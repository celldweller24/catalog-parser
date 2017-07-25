<?php

/**
 * Created by PhpStorm.
 * User: celldweller
 * Date: 7/24/17
 * Time: 8:42 PM
 */
namespace App;

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
//include '/home/developer/imgparser/vendor/simple-html-dom/simple-html-dom/simple_html_dom.php';


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

  public function getImgReferenses() {
    $instance = new \simple_html_dom();
    echo 3;
    /*$html = file_get_html($this->url);
    $imgArray = $html->find('img');
    var_dump($imgArray);*/
  }


}