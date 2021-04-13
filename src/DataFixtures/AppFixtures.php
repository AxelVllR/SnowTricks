<?php

namespace App\DataFixtures;

use App\Entity\Groups;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private const Groups = [
        "Grabs" => "primary",
        "Rotations" => "secondary",
        "Flips" => "success",
        "Slides" => "danger",
        "One Foot Tricks" => "warning",
        "Old School" => "info"
    ];

    public function load(ObjectManager $manager)
    {
        foreach(self::Groups as $group => $color) {
            $gp = (new Groups())
                ->setName($group)
                ->setColor($color)
            ;
            $manager->persist($gp);
        }

        $manager->flush();
    }
}
