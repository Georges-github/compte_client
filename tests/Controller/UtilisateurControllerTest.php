<?php

namespace App\Tests\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UtilisateurControllerTest extends WebTestCase{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $utilisateurRepository;
    private string $path = '/utilisateur/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->utilisateurRepository = $this->manager->getRepository(Utilisateur::class);

        foreach ($this->utilisateurRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Utilisateur index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'utilisateur[courriel]' => 'Testing',
            'utilisateur[roles]' => 'Testing',
            'utilisateur[password]' => 'Testing',
            'utilisateur[prenom]' => 'Testing',
            'utilisateur[nom]' => 'Testing',
            'utilisateur[genre]' => 'Testing',
            'utilisateur[telephone]' => 'Testing',
            'utilisateur[rueEtNumero]' => 'Testing',
            'utilisateur[codePostal]' => 'Testing',
            'utilisateur[ville]' => 'Testing',
            'utilisateur[societe]' => 'Testing',
            'utilisateur[dateHeureInsertion]' => 'Testing',
            'utilisateur[dateHeureMAJ]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->utilisateurRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Utilisateur();
        $fixture->setCourriel('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setPrenom('My Title');
        $fixture->setNom('My Title');
        $fixture->setGenre('My Title');
        $fixture->setTelephone('My Title');
        $fixture->setRueEtNumero('My Title');
        $fixture->setCodePostal('My Title');
        $fixture->setVille('My Title');
        $fixture->setSociete('My Title');
        $fixture->setDateHeureInsertion('My Title');
        $fixture->setDateHeureMAJ('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Utilisateur');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Utilisateur();
        $fixture->setCourriel('Value');
        $fixture->setRoles('Value');
        $fixture->setPassword('Value');
        $fixture->setPrenom('Value');
        $fixture->setNom('Value');
        $fixture->setGenre('Value');
        $fixture->setTelephone('Value');
        $fixture->setRueEtNumero('Value');
        $fixture->setCodePostal('Value');
        $fixture->setVille('Value');
        $fixture->setSociete('Value');
        $fixture->setDateHeureInsertion('Value');
        $fixture->setDateHeureMAJ('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'utilisateur[courriel]' => 'Something New',
            'utilisateur[roles]' => 'Something New',
            'utilisateur[password]' => 'Something New',
            'utilisateur[prenom]' => 'Something New',
            'utilisateur[nom]' => 'Something New',
            'utilisateur[genre]' => 'Something New',
            'utilisateur[telephone]' => 'Something New',
            'utilisateur[rueEtNumero]' => 'Something New',
            'utilisateur[codePostal]' => 'Something New',
            'utilisateur[ville]' => 'Something New',
            'utilisateur[societe]' => 'Something New',
            'utilisateur[dateHeureInsertion]' => 'Something New',
            'utilisateur[dateHeureMAJ]' => 'Something New',
        ]);

        self::assertResponseRedirects('/utilisateur/');

        $fixture = $this->utilisateurRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getCourriel());
        self::assertSame('Something New', $fixture[0]->getRoles());
        self::assertSame('Something New', $fixture[0]->getPassword());
        self::assertSame('Something New', $fixture[0]->getPrenom());
        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getGenre());
        self::assertSame('Something New', $fixture[0]->getTelephone());
        self::assertSame('Something New', $fixture[0]->getRueEtNumero());
        self::assertSame('Something New', $fixture[0]->getCodePostal());
        self::assertSame('Something New', $fixture[0]->getVille());
        self::assertSame('Something New', $fixture[0]->getSociete());
        self::assertSame('Something New', $fixture[0]->getDateHeureInsertion());
        self::assertSame('Something New', $fixture[0]->getDateHeureMAJ());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Utilisateur();
        $fixture->setCourriel('Value');
        $fixture->setRoles('Value');
        $fixture->setPassword('Value');
        $fixture->setPrenom('Value');
        $fixture->setNom('Value');
        $fixture->setGenre('Value');
        $fixture->setTelephone('Value');
        $fixture->setRueEtNumero('Value');
        $fixture->setCodePostal('Value');
        $fixture->setVille('Value');
        $fixture->setSociete('Value');
        $fixture->setDateHeureInsertion('Value');
        $fixture->setDateHeureMAJ('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/utilisateur/');
        self::assertSame(0, $this->utilisateurRepository->count([]));
    }
}
