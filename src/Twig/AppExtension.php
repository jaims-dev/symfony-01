<?php
/**
 * Created by PhpStorm.
 * User: jaims
 * Date: 22/02/19
 * Time: 11:45
 */

namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{


    public function getFilters()
    {
        return [
          new TwigFilter('price', [$this, 'priceFilter'])
        ];
    }

    public function priceFilter($price)
    {
        return '$'.number_format($price, '2', '.', ',');
    }
}