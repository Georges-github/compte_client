<?php

namespace App\Tests\Controller;

use App\Entity\Contrat;
use App\Repository\ContratRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ContratControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $contratRepository;
    private string $path = '/contrat/controller/by/c/r/u/d/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->contratRepository = $this->manager->getRepository(Contrat::class);

        foreach ($this->contratRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Contrat index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'contrat[numeroContrat]' => 'Testing',
            'contrat[intitule]' => 'Testing',
            'contrat[description]' => 'Testing',
            'contrat[dateDebutPrevue]' => 'Testing',
            'contrat[dateFinPrevue]' => 'Testing',
            'contrat[dateDebut]' => 'Testing',
            'contrat[dateFin]' => 'Testing',
            'contrat[cheminFichier]' => 'Testing',
            'contrat[dateHeureInsertion]' => 'Testing',
            'contrat[dateHeureMAJ]' => 'Testing',
            'contrat[idUtilisateur]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->contratRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Contrat();
        $fixture->setNumeroContrat('My Title');
        $fixture->setIntitule('My Title');
        $fixture->setDescription('My Title');
        $fixture->setDateDebutPrevue('My Title');
        $fixture->setDateFinPrevue('My Title');
        $fixture->setDateDebut('My Title');
        $fixture->setDateFin('My Title');
        $fixture->setCheminFichier('My Title');
        $fixture->setDateHeureInsertion('My Title');
        $fixture->setDateHeureMAJ('My Title');
        $fixture->setIdUtilisateur('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Contrat');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Contrat();
        $fixture->setNumeroContrat('Value');
        $fixture->setIntitule('Value');
        $fixture->setDescription('Value');
        $fixture->setDateDebutPrevue('Value');
        $fixture->setDateFinPrevue('Value');
        $fixture->setDateDebut('Value');
        $fixture->setDateFin('Value');
        $fixture->setCheminFichier('Value');
        $fixture->setDateHeureInsertion('Value');
        $fixture->setDateHeureMAJ('Value');
        $fixture->setIdUtilisateur('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'contrat[numeroContrat]' => 'Something New',
            'contrat[intitule]' => 'Something New',
            'contrat[description]' => 'Something New',
            'contrat[dateDebutPrevue]' => 'Something New',
            'contrat[dateFinPrevue]' => 'Something New',
            'contrat[dateDebut]' => 'Something New',
            'contrat[dateFin]' => 'Something New',
            'contrat[cheminFichier]' => 'Something New',
            'contrat[dateHeureInsertion]' => 'Something New',
            'contrat[dateHeureMAJ]' => 'Something New',
            'contrat[idUtilisateur]' => 'Something New',
        ]);

        self::assertResponseRedirects('/contrat/controller/by/c/r/u/d/');

        $fixture = $this->contratRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNumeroContrat());
        self::assertSame('Something New', $fixture[0]->getIntitule());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getDateDebutPrevue());
        self::assertSame('Something New', $fixture[0]->getDateFinPrevue());
        self::assertSame('Something New', $fixture[0]->getDateDebut());
        self::assertSame('Something New', $fixture[0]->getDateFin());
        self::assertSame('Something New', $fixture[0]->getCheminFichier());
        self::assertSame('Something New', $fixture[0]->getDateHeureInsertion());
        self::assertSame('Something New', $fixture[0]->getDateHeureMAJ());
        self::assertSame('Something New', $fixture[0]->getIdUtilisateur());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Contrat();
        $fixture->setNumeroContrat('Value');
        $fixture->setIntitule('Value');
        $fixture->setDescription('Value');
        $fixture->setDateDebutPrevue('Value');
        $fixture->setDateFinPrevue('Value');
        $fixture->setDateDebut('Value');
        $fixture->setDateFin('Value');
        $fixture->setCheminFichier('Value');
        $fixture->setDateHeureInsertion('Value');
        $fixture->setDateHeureMAJ('Value');
        $fixture->setIdUtilisateur('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/contrat/controller/by/c/r/u/d/');
        self::assertSame(0, $this->contratRepository->count([]));
    }
}
