<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class UsersFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
    ){}

    public function load(ObjectManager $manager): void
    {
        $admin = new Users();
        $admin->setEmail('admin@demo.fr');
        $admin->setName('Achraf ladhari');
        $admin->setAddress('12 rue du port');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $adminProd = new Users();
        $adminProd->setEmail('adminprod@demo.fr');
        $adminProd->setName('Achraf ladhari');
        $adminProd->setAddress('12 rue du port');
        $adminProd->setPassword(
            $this->passwordEncoder->hashPassword($adminProd, 'admin')
        );
        $adminProd->setRoles(['ROLE_PRODUCT_ADMIN']);

        $manager->persist($adminProd);

        $faker = Faker\Factory::create('fr_FR');

        for($usr = 1; $usr <= 5; $usr++){
            $user = new Users();
            $user->setEmail($faker->email);
            $user->setName($faker->Name);
            $user->setAddress($faker->streetAddress);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'secret')
            );
            $manager->persist($user);
        }

        $manager->flush();
    }
}
