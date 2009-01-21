<?php

require_once "../inc/promotions.php";

class Promotions
{
  public static function pajax_getCourses($promoId)
  {
    $promo = loadPromotion($promoId[0]);
    $courses = getAvailableCourses($promo);
    return $courses;
  }
}

?>