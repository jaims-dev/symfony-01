<?php
/**
 * Created by PhpStorm.
 * User: jaims
 * Date: 18/02/19
 * Time: 18:25
 */

namespace App\Controller;


use App\Service\Greeting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BlogController
 * @package App\Controller
 *
 * @Route("/blog")
 */

class BlogController extends AbstractController
{
    /**
     * @var Greeting
     */
    private $greeting;

    public function __construct(Greeting $greeting)
    {
        $this->greeting = $greeting;
    }

    /**
     * @Route("/{name}", name="blog_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index($name)
    {
//        return $this->render('base.html.twig',
//            ['message' => $this->greeting->greet($request->get('name'))]);
        return $this->render('base.html.twig',
            ['message' => $this->greeting->greet($name)]);
    }

}