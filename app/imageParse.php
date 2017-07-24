<?php

/**
 * Created by PhpStorm.
 * User: celldweller
 * Date: 7/24/17
 * Time: 8:42 PM
 */
namespace App;

require __DIR__.'/../vendor/autoload.php';

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
    $html = new simple_html_dom();
    $html = file_get_html($this->url);
    $imgArray = $html->find('img');
  }


}