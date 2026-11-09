<?php

namespace App\Tests\Entity;

use App\Entity\Event;
use App\Entity\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    #[DataProvider('providePlaces')]
    public function testRemainingPlaces(int $places, int $participants, int $expected): void
    {
        $event = new Event();
        $event->setPlaces($places);
        for ($i = 0; $i < $participants; $i++) {
            $event->addParticipant(new User());
        }

        $this->assertSame($expected, $event->getRemainingPlaces());
    }

    public static function providePlaces(): iterable
    {
        yield 'aucun inscrit' => [10, 0, 10];
        yield 'quelques inscrits' => [10, 3, 7];
        yield 'complet' => [2, 2, 0];
    }

    #[DataProvider('provideCategories')]
    public function testCategoryLabel(string $category, string $expected): void
    {
        $event = new Event();
        $event->setCategory($category);

        $this->assertSame($expected, $event->getCategoryLabel());
    }

    public static function provideCategories(): iterable
    {
        yield 'concert' => ['concert', 'Concert'];
        yield 'conference' => ['conference', 'Conférence'];
        yield 'atelier' => ['atelier', 'Atelier'];
        yield 'sport' => ['sport', 'Sport'];
    }
}