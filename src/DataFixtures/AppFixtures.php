<?php

namespace App\DataFixtures;

use App\Entity\Groups;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private const Groups = [
        "Grabs",
        "Rotations",
        "Flips",
        "Slides",
        "One Foot Tricks",
        "Old School"
    ];

    public function load(ObjectManager $manager)
    {
        foreach(self::Groups as $group) {
            $gp = (new Groups())->setName($group);
            $manager->persist($gp);
        }

        $manager->flush();
    }
}
