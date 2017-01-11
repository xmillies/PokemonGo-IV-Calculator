<?php

/*
 * (c) Jérémy Marodon <marodon.jeremy@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Th3Mouk\PokemonGoIVCalculator\Tests\Calculator;

use Illuminate\Support\Collection;
use Peridot\Leo\Interfaces\Assert;
use Th3Mouk\PokemonGoIVCalculator\Calculator\Calculator;
use Th3Mouk\PokemonGoIVCalculator\Entities\IvCombinaison;
use Th3Mouk\PokemonGoIVCalculator\Entities\Level;

describe('Calculator', function () {
    beforeEach(function () {
        $this->assert = new Assert();
    });

    describe('calculate()', function () {
        it("test exclusion double 13 IV (max)", function () {
            $bulbasaur = (new Calculator())->calculate(
                'bulbasaur', 515, 59, 2500, 4, 3, ['def']
            );

            $level = new Level(19, 2500, 0.5822789);
            $combinaison = new IvCombinaison($level, 13, 14, 12);

            $this->assert->equal($bulbasaur->getAveragePerfection(), 86.7);
            $this->assert->equal(
                $bulbasaur->getIvCombinaisons(),
                new Collection([$combinaison])
            );
        });

        it("test double 13 IV (max)", function () {
            $gengar = (new Calculator())->calculate(
                'gengar', 1421, 74, 2500, 3, 3, ['atk', 'def']
            );

            $level = new Level(20, 2500, 0.5974);
            $combinaison = new IvCombinaison($level, 13, 13, 5);

            $this->assert->equal((int)$gengar->getAveragePerfection()*10, (int)68.9*10);
            $this->assert->equal(
                $gengar->getIvCombinaisons(),
                new Collection([$combinaison])
            );
        });
    });

    describe('setRanges()', function () {
        it("100% iv pokemon", function () {
            $calculator = new Calculator();
            $calculator->calculate(
                'mew', 5000, 500, 200, 4, 4, ['atk', 'def', 'hp']
            );

            $this->assert->equal($calculator->getAttackRange(), [15]);
            $this->assert->equal($calculator->getDefenseRange(), [15]);
            $this->assert->equal($calculator->getStaminaRAnge(), [15]);
        });

        it("double max iv pokemon", function () {
            $calculator = new Calculator();
            $calculator->calculate(
                'mew', 5000, 500, 200, 3, 3, ['atk', 'hp']
            );

            $this->assert->equal($calculator->getAttackRange(), range(13, 14));
            $this->assert->equal($calculator->getDefenseRange(), range(0, 13));
            $this->assert->equal($calculator->getStaminaRAnge(), range(13, 14));
        });

        it("normal iv pokemon", function () {
            $calculator = new Calculator();
            $calculator->calculate(
                'mew', 5000, 500, 200, 3, 3, ['atk']
            );

            $this->assert->equal($calculator->getAttackRange(), range(13, 14));
            $this->assert->equal($calculator->getDefenseRange(), range(0, 13));
            $this->assert->equal($calculator->getStaminaRAnge(), range(0, 13));
        });

        it("0% iv pokemon", function () {
            $calculator = new Calculator();
            $calculator->calculate(
                'mew', 5000, 500, 200, 1, 1, ['atk', 'def', 'hp']
            );

            $this->assert->equal($calculator->getAttackRange(), range(0, 7));
            $this->assert->equal($calculator->getDefenseRange(), range(0, 7));
            $this->assert->equal($calculator->getStaminaRAnge(), range(0, 7));
        });

        it("check wtf stats values", function () {
            $calculator = new Calculator();
            $calculator->calculate(
                'mew', -43, 500, 200, 6, 7, ['atk', 'def', 'hp']
            );

            $this->assert->equal($calculator->getAttackRange(), range(0, 15));
            $this->assert->equal($calculator->getDefenseRange(), range(0, 15));
            $this->assert->equal($calculator->getStaminaRAnge(), range(0, 15));
        });
    });
});