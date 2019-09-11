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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    // Autowiring here!
    public function __construct(
        \Twig_Environment $twig,
        MicroPostRepository $microPostRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        FlashBagInterface $flashBag,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->twig = $twig;
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->authorizationChecker = $authorizationChecker;
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
     * @Security("is_granted('ROLE_USER')")
     */
    public function add(Request $request, TokenStorageInterface $tokenStorage)
    {
        $user = $tokenStorage->getToken()->getUser();
        $microPost = new MicroPost();
        $microPost->setTime(new \DateTime());
        $microPost->setUser($user);
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
    public function edit(MicroPost $microPost, Request $request)
    {

        /*
        if we were extending AbstractController:
        $this->denyAccessUnlessGranted(...)

        We could also use the annotation @Security("is_granted('edit', post)", message="Acces denied")
        */
        if (!$this->authorizationChecker->isGranted('edit', $microPost)) {
            throw new UnauthorizedHttpException("Unauthorized operation");
        }
        $form = $this->formFactory->create(
            MicroPostType::class,
            $microPost
        );
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
//            $this->entityManager->persist($microPost); // Needed to create new posts, not for editing them
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
     * @Route("/delete/{id}", name="micro_post_delete")
     * @Security("is_granted('edit', microPost)", message="Acces denied")
     */
    public function delete(Micropost $microPost) {
        /* if we were extending AbstractController:
            $this->denyAccessUnlessGranted(...)

            We could also use the annotation @Security("is_granted('edit', post)", message="Acces denied")
        */
        if (!$this->authorizationChecker->isGranted('edit', $microPost)) {
            throw new UnauthorizedHttpException("Unauthorized operation");
        }
        $this->entityManager->remove($microPost);
        $this->entityManager->flush();
        $this->flashBag->add('notice', 'The micropost was deleted!');

        return new RedirectResponse($this->router->generate('micro_post_index'));

    }

}