<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager) {
        $this->loadUsers($manager);
        $this->loadMicroPosts($manager);
    }

    private function loadMicroPosts(ObjectManager $manager)
    {
        // Lets create 10 entities to db

        // How is this executed?
        // php bin/console doctrine:fixtures:load
        for($i=0; $i<10; $i++)
        {
            $microPost = new MicroPost();
            $microPost->setText("Lorem ipsum dolor sic amet $i");
            $microPost->setTime(new \DateTime());
            $microPost->setUser($this->getReference('johnyydoe'));
            $manager->persist($microPost);
        }
        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setFullname('John Doe');
        $user->setUsername('john');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'doe'));
        $user->setMail('johndoe@doe.com');

        $this->addReference('johnyydoe', $user);

        $manager->persist($user);
        $manager->flush();
    }
}
