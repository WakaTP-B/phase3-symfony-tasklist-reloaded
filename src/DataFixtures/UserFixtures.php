<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'username' => 'Tom',
                'email'    => 'tom@ato.com',
                'password' => '123456789',
                'roles'    => ['ROLE_USER'],
            ],
            [
                'username' => 'Admin',
                'email'    => 'admin@mail.com',
                'password' => '123456789',
                'roles'    => ['ROLE_ADMIN'],
            ],
        ];

        foreach ($usersData as $data) {
            $user = new User();
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setRoles($data['roles']);

            $hashed = $this->hasher->hashPassword($user, $data['password']);
            $user->setPassword($hashed);

            $manager->persist($user);
        }
        $manager->flush();
    }
}
