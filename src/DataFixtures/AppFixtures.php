<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $user_password) {}

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $admin = new User;
        $admin->setNom('Nesy');
        $admin->setPrenom('Melvin');
        $admin->setPhone('679117686');
        $admin->setEmail('melvin@smartbiblio.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $hased = $this->user_password->hashPassword($admin, 'lecielestbleu');
        $admin->setPassword($hased);

        $manager->persist($admin);
        $manager->flush();
    }
}
