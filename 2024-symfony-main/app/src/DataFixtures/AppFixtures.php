<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Music;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i <= 10; $i++){
            $music = new Music();
            $music->setName("Music". $i);
            $music->setUrl("url". $i);

            $manager->persist($music);
        }
        $manager->flush();
    }
}
