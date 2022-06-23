<?php

namespace App\DataFixtures;

use App\Entity\Food;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $admin = new User();
        $admin->setFirstName("admin");
        $admin->setLastName("admin");
        $admin->setEmail("admin@admin.com");
        $admin->setUsername("admin");
        $admin->setPhone("admin99");
        $admin->setRoles(["ROLE_ADMIN"]);
        $password = $this->hasher->hashPassword($admin, 'admin');
        $admin->setPassword($password);
        $manager->persist($admin);

        $user = new User();
        $user->setFirstName("user1");
        $user->setLastName("user1");
        $user->setEmail("admin@admin.com");
        $user->setUsername("user1");
        $user->setPhone("admin99");
        $password = $this->hasher->hashPassword($user, 'user1');
        $user->setPassword($password);
        $manager->persist($user);
        
        $user = new User();
        $user->setFirstName("user2");
        $user->setLastName("user2");
        $user->setUsername("user2");
        $user->setEmail("admin@admin.com");
        $user->setPhone("admin99");
        $password = $this->hasher->hashPassword($user, 'user2');
        $user->setPassword($password);
        $manager->persist($user);

        for($x=0; $x<=10; $x++)
        {
            $food = new Food();
            $name = 'food'.$x;
            $food->setName($name);
            $food->setPrice(rand(100,600));
            $manager->persist($food);
        }

        $manager->flush();
    }
}
