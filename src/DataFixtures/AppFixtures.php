<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private const USERS = [
        [
            'username' => 'john',
            'email' => 'john_doe@doe.com',
            'password' => 'john',
            'fullName' => 'John Doe',
            'roles' => [USER::ROLE_USER],
        ],
        [
            'username' => 'rob',
            'email' => 'rob_smith@smith.com',
            'password' => 'rob',
            'fullName' => 'Rob Smith',
            'roles' => [USER::ROLE_USER],
        ],
        [
            'username' => 'marry',
            'email' => 'marry_gold@gold.com',
            'password' => 'marry',
            'fullName' => 'Marry Gold',
            'roles' => [USER::ROLE_USER],
        ],
        [
            'username' => 'admin',
            'email' => 'admin@gold.com',
            'password' => 'admin',
            'fullName' => 'Admin Ister',
            'roles' => [USER::ROLE_ADMIN],
        ],

    ];

    private const POST_TEXT = [
        'Hello, how are you?',
        'It\'s nice sunny weather today',
        'I need to buy some ice cream!',
        'I wanna buy a new car',
        'There\'s a problem with my phone',
        'I need to go to the doctor',
        'What are you up to today?',
        'Did you watch the game yesterday?',
        'How was your day?'
    ];

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
            $microPost->setText(self::POST_TEXT[rand(0, count(self::POST_TEXT)-1)]);
            $date = new \DateTime();
            $date->modify('-'.rand(0,10).' day');
            $microPost->setTime($date);
            $microPost->setUser($this->getReference(
                self::USERS[rand(0, count(self::USERS)-1)]['username']
            ));
            $manager->persist($microPost);
        }
        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        foreach ( self::USERS as $userData ) {
            $user = new User();
            $user->setFullname($userData['fullName']);
            $user->setUsername($userData['username']);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $userData['password']));
            $user->setMail($userData['email']);
            $user->setRoles($userData['roles']);

            $this->addReference($userData['username'], $user);

            $manager->persist($user);
            $manager->flush();
        }
    }
}
