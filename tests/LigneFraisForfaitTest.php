<?php

namespace App\Tests;

use App\Entity\LigneFraisForfait;
use PHPUnit\Framework\TestCase;

class LigneFraisForfaitTest extends TestCase
{
    public function testSomething(): void
    {
        $fraisForfait = $this->createMock(FraisForfait::class);
        $fraisForfait->method('getMontant')->willReturn(10.0);

        $ligneFraisForfait = new LigneFraisForfait();
        $ligneFraisForfait->setFraisforfait($fraisForfait);
        $ligneFraisForfait->setQuantite(3);

        $this->assertEquals(30.0, $ligneFraisForfait->getMontant());
    }
}
