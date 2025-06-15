<?php
namespace App\Tests\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Publication;
use App\Entity\Photo;

class PublicationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $publication = new Publication();
        $publication->setTitre('Test')
                    ->setContenu('Contenu')
                    ->setDateHeureInsertion(new \DateTimeImmutable());

        $photo1 = new Photo();
        $photo1->setCheminFichierImage('test-image-1.jpg')
               ->setIdPublication($publication);
        $publication->addPhoto($photo1);

        $photo2 = new Photo();
        $photo2->setCheminFichierImage('test-image-2.jpg')
               ->setIdPublication($publication);
        $publication->addPhoto($photo2);

        $manager->persist($publication);
        $manager->flush();
    }
}
