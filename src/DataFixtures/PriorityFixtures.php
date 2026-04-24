<?php

namespace App\DataFixtures;

use App\Entity\Priority;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PriorityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $defaults = [
            ['name' => 'Normal', 'color' => '#BFDBFE'],
            ['name' => 'Important', 'color' => '#FED7AA'],
            ['name' => 'Urgent', 'color' => '#FCA5A5'],
        ];

        foreach ($defaults as $data) {
            $priority = new Priority();
            $priority->setName($data['name']);
            $priority->setColor($data['color']);

            $manager->persist($priority);
        }
        $manager->flush();
    }
}