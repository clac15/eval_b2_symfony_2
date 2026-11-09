<?php

namespace App\Tests\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventPagesTest extends WebTestCase
{
    public function testHome(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertCount(3, $crawler->filter('.event-card'));
        $this->assertSelectorNotExists('li:contains("Concert passé")');
        $this->assertSelectorNotExists('li:contains("Tournoi de basket")');
    }

    public function testEventDetail(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $client->clickLink('Soirée jazz');

        $this->assertSelectorTextContains('h1', 'Soirée jazz');
    }

    public function testEventNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/evenement/9999');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCategoryFilter(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/?categorie=sport');

        $this->assertCount(1, $crawler->filter('.event-card'));
        $this->assertSelectorTextContains('a.active', 'Sport');
    }

    public function testFormWithoutTitle(): void
    {
        $client = static::createClient();
        $repository = static::getContainer()->get(EventRepository::class);
        $avant = $repository->count([]);

        $client->request('GET', '/evenement/ajouter');
        $client->submitForm('Enregistrer l\'événement', [
            'event[title]' => '',
            'event[date]' => '2030-01-01T20:00',
            'event[information]' => 'Des informations',
            'event[category]' => 'sport',
            'event[places]' => '10',
        ]);

        $this->assertSelectorTextContains('body', 'Le titre est obligatoire.');
        $this->assertSame($avant, $repository->count([]));
    }

    public function testFormWithZeroPlaces(): void
    {
        $client = static::createClient();
        $repository = static::getContainer()->get(EventRepository::class);
        $avant = $repository->count([]);

        $client->request('GET', '/evenement/ajouter');
        $client->submitForm('Enregistrer l\'événement', [
            'event[title]' => 'Mon événement',
            'event[date]' => '2030-01-01T20:00',
            'event[information]' => 'Des informations',
            'event[category]' => 'sport',
            'event[places]' => '0',
        ]);

        $this->assertSelectorTextContains('body', 'Le nombre de places doit être supérieur à 0.');
        $this->assertSame($avant, $repository->count([]));
    }
}