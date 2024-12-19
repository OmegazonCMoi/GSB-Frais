<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TopVisiteursControllerTest extends WebTestCase
{
    public function testTopVisiteurs()
    {
        $client = static::createClient();

        // Accéder à la page des top visiteurs pour le mois 2024-03
        $client->request('GET', '/top-visiteurs?mois=2024-03');

        // Vérifier que la page se charge correctement
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Top 3 des visiteurs pour le mois 2024-03');

        // Vérifier la présence des visiteurs dans le tableau
        $this->assertSelectorTextContains('td', 'Nom');
        $this->assertSelectorTextContains('td', 'Prénom');
        $this->assertSelectorTextContains('td', 'Montant Validé');
    }

    public function testTopVisiteursSorting()
    {
        $client = static::createClient();

        // Vérifier que les visiteurs sont triés par montant
        $crawler = $client->request('GET', '/top-visiteurs?mois=2024-03');

        $rows = $crawler->filter('table: tbody tr');
        $amounts = [];

        // Extraire les montants validés de chaque ligne
        foreach ($rows as $row) {
            $amounts[] = (float) trim($row->childNodes->item(2)->nodeValue);
        }

        // Vérifier que les montants sont triés par ordre décroissant
        $this->assertTrue($amounts[0] >= $amounts[1]);
        $this->assertTrue($amounts[1] >= $amounts[2]);
    }
}
