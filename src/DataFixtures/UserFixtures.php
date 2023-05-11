<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserFixtures constructor.
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('Gadiel');
        $user->setLastName('Reyes');
        $user->setUsername('gadielreyes');
        $user->setEmail('gadiel.reyesdelrosario@vidoomy.com');
        $user->setLocale('en');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setStatus(User::STATUS_ACTIVE);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            '123123123'
        ));

        $user2 = new User();
        $user2->setName('Mario');
        $user2->setLastName('Ortas');
        $user2->setUsername('marioortas');
        $user2->setEmail('mario.ortas@vidoomy.com');
        $user2->setLocale('es');
        $user2->setRoles(['ROLE_ADMIN']);
        $user2->setStatus(User::STATUS_ACTIVE);
        $user2->setPassword($this->passwordEncoder->encodePassword(
            $user2,
            '123123123'
        ));

        $user3 = new User();
        $user3->setName('Federico');
        $user3->setLastName('Izuel');
        $user3->setUsername('fede_admin');
        $user3->setEmail('federico.izuel@vidoomy.com');
        $user3->setLocale('es');
        $user3->setRoles(['ROLE_ADMIN']);
        $user3->setStatus(User::STATUS_ACTIVE);
        $user3->setPassword($this->passwordEncoder->encodePassword(
            $user3,
            '123123321'
        ));

        $manager->persist($user);
        $manager->persist($user2);
        $manager->persist($user3);

        $manager->flush();
    }
}
