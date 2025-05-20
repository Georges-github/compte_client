<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\FileUploader;
use App\Tests\Fake\FakeFileUploader;
use App\Entity\Publication;

class SupprimerPublicationTest extends WebTestCase
{
    public function testSuppressionPublicationEtPhotos(): void
    {
        $client = static::createClient();

        $em = self::getContainer()->get('doctrine')->getManager();
        $publication = $em->getRepository(Publication::class)->findOneBy(['titre' => 'Test']);

        $tokenManager = self::getContainer()->get('security.csrf.token_manager');
        $token = $tokenManager->getToken('supprimer_publication_' . $publication->getId());

        $client->request('POST', '/supprimerUnePublication/' . $publication->getId(), [
            '_token' => $token,
        ]);

        $fakeUploader = self::getContainer()->get(FileUploader::class);
        $this->assertInstanceOf(FakeFileUploader::class, $fakeUploader);

        $deleted = $fakeUploader->getDeletedFiles();

        $this->assertContains('test-image-1.jpg', $deleted);
        $this->assertContains('test-image-2.jpg', $deleted);
    }
}
