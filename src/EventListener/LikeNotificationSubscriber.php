<?php


namespace App\EventListener;


use App\Entity\LikeNotification;
use App\Entity\MicroPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;

class LikeNotificationSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush
        ];
    }

    public function onFlush(OnFlushEventArgs $flushEventArgs) {
        $em = $flushEventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        /** @var PersistentCollection $collectionUpdate */
        foreach($uow->getScheduledCollectionUpdates() as $collectionUpdate){
            if(!$collectionUpdate->getOwner() instanceof MicroPost ) {
                continue;
            }

            // to determine the field that caused the event to be triggered, since it comes with the
            // PersistenCollection collectionUpdate as a field, you can debug/dump variable
            if( $collectionUpdate->getMapping()['fieldName'] !== 'likedBy' ){
                continue;
            }

            $insertDiff = $collectionUpdate->getInsertDiff();

            if (!count($insertDiff)) {
                return;
            }

            /** @var MicroPost $microPost */
            $microPost = $collectionUpdate->getOwner();

            $notification = new LikeNotification();
            $notification->setUser($microPost->getUser());
            $notification->setMicroPost($microPost);
            $notification->setLikedBy(reset($insertDiff));

            $em->persist($notification);

            // We mustnt flush but add the notification to the unit of work
            $uow->computeChangeSet(
                $em->getClassMetadata(LikeNotification::class),
                $notification
            );

        }

    }
}