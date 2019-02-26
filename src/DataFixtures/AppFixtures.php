<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Lets create 10 entities to db

        // How is this executed?
        // php bin/console doctrine:fixtures:load
        for($i=0; $i<10; $i++)
        {
            $microPost = new MicroPost();
            $microPost->setText("Lorem ipsum dolor sic amet $i");
            $microPost->setTime(new \DateTime());
            $manager->persist($microPost);
        }
        $manager->flush();
    }
}
