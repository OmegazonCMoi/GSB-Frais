<?php

namespace App\Tests\Unit;

use App\Entity\LigneFraisForfait;
use App\Entity\FraisForfait;
use PHPUnit\Framework\TestCase;

class LigneFraisForfaitTest extends TestCase
{
    public function testCalculerTotal()
    {
        $fraisForfait = new FraisForfait();
        $fraisForfait->setMontant(100); // Montant unitaire

        $ligneFrais = new LigneFraisForfait();
        $ligneFrais->setQuantite(2); // Quantité
        $ligneFrais->setFraisForfait($fraisForfait);

        $this->assertEquals(200, $ligneFrais->calculerTotal());
    }
}
