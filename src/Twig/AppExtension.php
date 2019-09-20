<?php
/**
 * Created by PhpStorm.
 * User: jaims
 * Date: 22/02/19
 * Time: 11:45
 */

namespace App\Twig;


use App\Entity\LikeNotification;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    private $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;    // See services.yaml
    }

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

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    public function getGlobals()
    {
        return [
            'locale' => $this->locale,
            'foobarz' => 'foobarz',
        ];
    }

    public function getTests() {
        return [
            new \Twig_SimpleTest('like', function($obj){
                return $obj instanceof LikeNotification;
            })
        ];
    }
}