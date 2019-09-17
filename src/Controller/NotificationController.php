<?php


namespace App\Controller;


use App\Repository\NotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NotificationController
 * @package App\Controller
 * @Security("is_granted('ROLE_USER')")
 * @Route("/notification")
 */
class NotificationController extends AbstractController
{
    /**
     * @Route("/unread-count", name="notification_unread")
     */
    public function unreadCount(NotificationRepository $notificationRepository) { // no need to pass user, it is current user

        return new JsonResponse([
            'count' => $notificationRepository->findUnseenByUser($this->getUser())
        ]);
    }

}