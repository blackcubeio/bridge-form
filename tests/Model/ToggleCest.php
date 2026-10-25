<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm\Tests\Model;

use Blackcube\BridgeForm\Field\Toggle;
use Blackcube\BridgeForm\Field\ToggleList;
use Blackcube\BridgeForm\Tests\Support\ModelTester;
use Blackcube\BridgeForm\Tests\Support\ProfilForm;

/**
 * The toggle follows the checkbox rules: its name, and a checked state that
 * comes from comparing its value with the field value.
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class ToggleCest
{
    public function theToggleCarriesItsName(ModelTester $I): void
    {
        $I->wantTo('name a toggle without a model');

        $html = Toggle::widget()->name('newsletter')->inputValue('yes')->render();

        $I->assertStringContainsString('name="newsletter"', $html);
        $I->assertStringContainsString('value="yes"', $html);
        $I->assertStringNotContainsString('checked', $html);
    }

    public function theToggleComparesItsValueWithTheField(ModelTester $I): void
    {
        $I->wantTo('check a toggle from the value of a boolean property');

        $on = Toggle::create(new ProfilForm(lettre: true), 'lettre')->render();
        $off = Toggle::create(new ProfilForm(lettre: false), 'lettre')->render();

        $I->assertStringContainsString('name="ProfilForm[lettre]"', $on);
        $I->assertStringContainsString('value="1"', $on);
        $I->assertStringContainsString('checked', $on);
        $I->assertStringNotContainsString('checked', $off);
    }

    public function anExplicitCheckedStateWins(ModelTester $I): void
    {
        $I->wantTo('force the checked state of a toggle');

        $forcedOff = Toggle::create(new ProfilForm(lettre: true), 'lettre')->checked(false)->render();
        $forcedOn = Toggle::widget()->name('n')->checked()->render();

        $I->assertStringNotContainsString('checked', $forcedOff);
        $I->assertStringContainsString('checked', $forcedOn);
    }

    public function theToggleListSelectsFromTheArray(ModelTester $I): void
    {
        $I->wantTo('render a list of toggles bound to an array property');

        $html = ToggleList::create(new ProfilForm(centresInteret: ['php']), 'centresInteret')
            ->items([
                'php' => 'PHP',
                'js' => 'JavaScript',
            ])
            ->render();

        $I->assertSame(2, substr_count($html, 'name="ProfilForm[centresInteret][]"'));
        $I->assertSame(1, substr_count($html, 'checked'));
        preg_match_all('/<label for="([^"]+)"/', $html, $labels);
        $I->assertCount(2, $labels[1]);
        foreach ($labels[1] as $id) {
            $I->assertStringContainsString('id="' . $id . '"', $html);
        }
        $I->assertStringContainsString('value="php"', $html);
        $I->assertStringContainsString('value="js"', $html);
        $I->assertStringContainsString('PHP', $html);
        $I->assertStringContainsString('JavaScript', $html);
    }
}
