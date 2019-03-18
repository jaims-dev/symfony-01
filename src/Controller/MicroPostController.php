<?php
/**
 * Created by PhpStorm.
 * User: jaims
 * Date: 26/02/19
 * Time: 17:06
 */

namespace App\Controller;



use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class MicroPostController
 * @package App\Controller
 *
 * @Route("/micro-post")
 */

class MicroPostController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var MicroPostRepository
     */
    private $microPostRepository;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;

    // Autowiring here!
    public function __construct(
        \Twig_Environment $twig,
        MicroPostRepository $microPostRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        RouterInterface $router )
    {
        $this->twig = $twig;
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * @Route("/", name="micro_post_index")
     */
    public function index()
    {
//        $html = $this->twig->render('micro-post/index.html.twig', [
//            'posts' => $this->microPostRepository->findAll()
//        ]);

        $html = $this->twig->render('micro-post/index.html.twig', [
            'posts' => $this->microPostRepository->findBy([], ['time' => 'DESC'])
        ]);

        return new Response($html);
    }

    /**
     * @Route("/add", name="micro_post_add")
     */
    public function add(Request $request)
    {
        $microPost = new MicroPost();
        $microPost->setTime(new \DateTime());
        $form = $this->formFactory->create(
            MicroPostType::class,
            $microPost
        );
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('micro_post_index'));
        }


        return new Response(
            $this->twig->render(
                'micro-post/add.html.twig',
                ['form' => $form->createView()]
            )
        );
    }

    /**
     * @Route("/{id}", name="micro_post_post")
     */
//    public function post($id)
    public function post(MicroPost $post) // Here we use Symfony's feature called param converter
    {
//        $post = $this->microPostRepository->find($id); // param converter probably does this internally
        return new Response(
            $this->twig->render(
                'micro-post/post.html.twig',
                ['post' => $post]
            )
        );
    }

    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     */
    public function edit(MicroPost $post, Request $request)
    {
        $form = $this->formFactory->create(
            MicroPostType::class,
            $post
        );
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
//            $this->entityManager->persist($post); // Needed to create new posts, not for editing them
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('micro_post_index'));
        }


        return new Response(
            $this->twig->render(
                'micro-post/add.html.twig',
                ['form' => $form->createView()]
            )
        );
    }

}