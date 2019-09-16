<?php


namespace App\Controller;


use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Class LikesController
 * @package App\Controller
 * @Route("/likes")
 */
class LikesController extends AbstractController
{
    /**
     * @param MicroPost $microPost
     * @Route("/like/{id}", name="likes_like")
     * @return JsonResponse
     */
    public function like(MicroPost $microPost) {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        if(!$currentUser instanceof User) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $microPost->like($currentUser);
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse(['count' => $microPost->getLikedBy()->count()]);
    }

    /**
     *@Route("/unlike/{id}", name="likes_unlike")
     */
    public function unlike(MicroPost $microPost) {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        if(!$currentUser instanceof User) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $microPost->unlike($currentUser);
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse(['count' => $microPost->getLikedBy()->count()]);
    }

}