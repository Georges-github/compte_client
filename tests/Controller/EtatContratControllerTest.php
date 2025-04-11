<?php

namespace App\Tests\Controller;

use App\Entity\EtatContrat;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class EtatContratControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $etatContratRepository;
    private string $path = '/etat/contrat/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->etatContratRepository = $this->manager->getRepository(EtatContrat::class);

        foreach ($this->etatContratRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('EtatContrat index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'etat_contrat[etat]' => 'Testing',
            'etat_contrat[dateHeureInsertion]' => 'Testing',
            'etat_contrat[dateHeureMAJ]' => 'Testing',
            'etat_contrat[idUtilisateur]' => 'Testing',
            'etat_contrat[idContrat]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->etatContratRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new EtatContrat();
        $fixture->setEtat('My Title');
        $fixture->setDateHeureInsertion('My Title');
        $fixture->setDateHeureMAJ('My Title');
        $fixture->setIdUtilisateur('My Title');
        $fixture->setIdContrat('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('EtatContrat');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new EtatContrat();
        $fixture->setEtat('Value');
        $fixture->setDateHeureInsertion('Value');
        $fixture->setDateHeureMAJ('Value');
        $fixture->setIdUtilisateur('Value');
        $fixture->setIdContrat('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'etat_contrat[etat]' => 'Something New',
            'etat_contrat[dateHeureInsertion]' => 'Something New',
            'etat_contrat[dateHeureMAJ]' => 'Something New',
            'etat_contrat[idUtilisateur]' => 'Something New',
            'etat_contrat[idContrat]' => 'Something New',
        ]);

        self::assertResponseRedirects('/etat/contrat/');

        $fixture = $this->etatContratRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getEtat());
        self::assertSame('Something New', $fixture[0]->getDateHeureInsertion());
        self::assertSame('Something New', $fixture[0]->getDateHeureMAJ());
        self::assertSame('Something New', $fixture[0]->getIdUtilisateur());
        self::assertSame('Something New', $fixture[0]->getIdContrat());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new EtatContrat();
        $fixture->setEtat('Value');
        $fixture->setDateHeureInsertion('Value');
        $fixture->setDateHeureMAJ('Value');
        $fixture->setIdUtilisateur('Value');
        $fixture->setIdContrat('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/etat/contrat/');
        self::assertSame(0, $this->etatContratRepository->count([]));
    }
}
