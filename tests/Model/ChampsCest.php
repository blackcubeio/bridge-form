<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm\Tests\Model;

use Blackcube\BridgeForm\Field\Checkbox;
use Blackcube\BridgeForm\Field\CheckboxList;
use Blackcube\BridgeForm\Field\Input;
use Blackcube\BridgeForm\Field\RadioList;
use Blackcube\BridgeForm\Field\Select;
use Blackcube\BridgeForm\Field\Textarea;
use Blackcube\BridgeForm\Field\Toggle;
use Blackcube\BridgeForm\Field\Upload;
use Blackcube\BridgeForm\Tests\Support\ModelTester;
use Blackcube\BridgeForm\Tests\Support\ProfilForm;

/**
 * The fields render native HTML: ask for a field, look at what comes out.
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class ChampsCest
{
    public function inputRendersANativeInput(ModelTester $I): void
    {
        $I->wantTo('render a text field');

        $html = Input::widget()->name('nickname')->render();

        $I->assertStringContainsString('<input type="text" name="nickname"', $html);
    }

    public function inputFollowsItsType(ModelTester $I): void
    {
        $I->wantTo('choose the type of the field');

        $I->assertStringContainsString('type="email"', Input::widget()->name('a')->email()->render());
        $I->assertStringContainsString('type="password"', Input::widget()->name('a')->password()->render());
        $I->assertStringContainsString('type="date"', Input::widget()->name('a')->date()->render());
        $I->assertStringContainsString('type="number"', Input::widget()->name('a')->number()->render());
        $I->assertStringContainsString('type="tel"', Input::widget()->name('a')->telephone()->render());
        $I->assertStringContainsString('type="hidden"', Input::widget()->name('a')->hidden()->render());
    }

    public function theFieldReadsTheModel(ModelTester $I): void
    {
        $I->wantTo('bind a field to a model property');

        $html = Input::create(new ProfilForm(nom: 'Ada Lovelace'), 'nom')->render();

        $I->assertStringContainsString('name="ProfilForm[nom]"', $html);
        $I->assertStringContainsString('id="profilform-nom"', $html);
        $I->assertStringContainsString('value="Ada Lovelace"', $html);
        $I->assertStringContainsString('Full name', $html);
        $I->assertStringContainsString('As it will appear on your profile', $html);
    }

    public function theBlockCarriesLabelAndHint(ModelTester $I): void
    {
        $I->wantTo('check that the block gathers label, field and hint');

        $html = Input::create(new ProfilForm(), 'courriel')->email()->render();

        $I->assertStringContainsString('<label', $html);
        $I->assertStringContainsString('Email address', $html);
        $I->assertStringContainsString('<input', $html);
        $I->assertStringContainsString('<p', $html);
        $I->assertStringContainsString('We never share it', $html);
    }

    public function theIdPointsAtTheField(ModelTester $I): void
    {
        $I->wantTo('check that the label points at its field');

        $html = Input::create(new ProfilForm(), 'nom')->render();

        preg_match('/<label for="([^"]+)"/', $html, $label);
        preg_match('/<input[^>]*id="([^"]+)"/', $html, $field);

        $I->assertNotEmpty($label, 'the label carries a for attribute');
        $I->assertSame($field[1], $label[1]);
    }

    public function thePlaceholderComesFromTheModel(ModelTester $I): void
    {
        $I->wantTo('reuse the placeholder declared by the model');

        $html = Input::create(new ProfilForm(), 'courriel')->email()->render();

        $I->assertStringContainsString('placeholder="you@example.com"', $html);
    }

    public function textareaRendersATextarea(ModelTester $I): void
    {
        $I->wantTo('render a text area');

        $html = Textarea::create(new ProfilForm(presentation: 'Hello'), 'presentation')->rows(6)->render();

        $I->assertStringContainsString('<textarea', $html);
        $I->assertStringContainsString('rows="6"', $html);
        $I->assertStringContainsString('Hello', $html);
    }

    public function selectRendersANativeSelect(ModelTester $I): void
    {
        $I->wantTo('render a native dropdown list');

        $html = Select::create(new ProfilForm(pays: 'fr'), 'pays')
            ->optionsData([
                'fr' => 'France',
                'be' => 'Belgium',
            ])
            ->prompt('-- Choose --')
            ->render();

        $I->assertStringContainsString('<select', $html);
        $I->assertStringContainsString('>-- Choose --</option>', $html);
        $I->assertStringContainsString('<option value="fr" selected>France</option>', $html);
        $I->assertStringContainsString('<option value="be">Belgium</option>', $html);
    }

    public function selectRendersGroups(ModelTester $I): void
    {
        $I->wantTo('render option groups');

        $html = Select::widget()
            ->name('pays')
            ->optionsData(['Europe' => ['fr' => 'France']])
            ->render();

        $I->assertStringContainsString('<optgroup label="Europe">', $html);
        $I->assertStringContainsString('<option value="fr">France</option>', $html);
    }

    public function multipleSelectIsNamedAsAnArray(ModelTester $I): void
    {
        $I->wantTo('check that a multiple choice sends an array');

        $html = Select::widget()->name('pays')->optionsData(['fr' => 'France'])->multiple()->render();

        $I->assertStringContainsString('name="pays[]"', $html);
        $I->assertStringContainsString('multiple', $html);
    }

    public function checkboxCarriesItsValue(ModelTester $I): void
    {
        $I->wantTo('render a checkbox and its value');

        $html = Checkbox::widget()->name('lettre')->inputValue('1')->uncheckValue('0')->render();

        $I->assertStringContainsString('type="checkbox"', $html);
        $I->assertStringContainsString('value="1"', $html);
        $I->assertStringContainsString('type="hidden"', $html);
        $I->assertStringContainsString('value="0"', $html);
    }

    public function checkboxIsCheckedWhenTheModelSaysSo(ModelTester $I): void
    {
        $I->wantTo('check the box when the model says so');

        $checked = Checkbox::create(new ProfilForm(lettre: true), 'lettre')->inputValue('1')->render();
        $unchecked = Checkbox::create(new ProfilForm(lettre: false), 'lettre')->inputValue('1')->render();

        $I->assertStringContainsString('checked', $checked);
        $I->assertStringNotContainsString('checked', $unchecked);
    }

    public function toggleIsACheckbox(ModelTester $I): void
    {
        $I->wantTo('check that the switch is a checkbox');

        $html = Toggle::widget()->name('lettre')->render();

        $I->assertStringContainsString('type="checkbox"', $html);
    }

    public function radioListRendersASingleValue(ModelTester $I): void
    {
        $I->wantTo('render a group of radio buttons');

        $html = RadioList::create(new ProfilForm(formule: 'medium'), 'formule')
            ->items([
                'small' => 'Small',
                'medium' => 'Medium',
            ])
            ->render();

        $I->assertSame(2, substr_count($html, 'type="radio"'));
        $I->assertStringContainsString('value="small"', $html);
        $I->assertStringContainsString('value="medium"', $html);
        $I->assertSame(1, substr_count($html, 'checked'));
    }

    public function checkboxListIsNamedAsAnArray(ModelTester $I): void
    {
        $I->wantTo('render a group of checkboxes that sends several values');

        $html = CheckboxList::create(new ProfilForm(centresInteret: ['php']), 'centresInteret')
            ->items([
                'php' => 'PHP',
                'js' => 'JavaScript',
            ])
            ->render();

        $I->assertSame(2, substr_count($html, 'name="ProfilForm[centresInteret][]"'));
        $I->assertStringContainsString('value="php"', $html);
        $I->assertStringContainsString('value="js"', $html);
        $I->assertSame(1, substr_count($html, 'checked'));
    }

    public function eachItemCarriesItsOwnId(ModelTester $I): void
    {
        $I->wantTo('check that a click on the label reaches its box');

        $html = CheckboxList::create(new ProfilForm(), 'centresInteret')
            ->items([
                'php' => 'PHP',
                'js' => 'JavaScript',
            ])
            ->render();

        preg_match_all('/<input[^>]*id="([^"]+)"/', $html, $fields);
        preg_match_all('/<label for="([^"]+)"/', $html, $labels);

        $I->assertCount(2, $fields[1]);
        $I->assertSame($fields[1], $labels[1]);
    }

    public function twoListsOfTheSameFieldKeepDistinctIds(ModelTester $I): void
    {
        $I->wantTo('check that two lists of the same property on one page share no id');

        $html = CheckboxList::create(new ProfilForm(), 'centresInteret')
            ->items([
                'php' => 'PHP',
                'js' => 'JavaScript',
            ])
            ->render();
        $html .= CheckboxList::create(new ProfilForm(), 'centresInteret')
            ->items([
                'go' => 'Go',
                'rust' => 'Rust',
            ])
            ->render();

        preg_match_all('/<input[^>]*id="([^"]+)"/', $html, $fields);
        preg_match_all('/<label for="([^"]+)"/', $html, $labels);

        $I->assertCount(4, $fields[1]);
        $I->assertSame($fields[1], array_unique($fields[1]));
        $I->assertSame($fields[1], $labels[1]);
    }

    public function aListNamedWithoutAModelNamesItsItems(ModelTester $I): void
    {
        $I->wantTo('check that a name set by hand reaches every item');

        $items = [
            '1' => 'one',
            '2' => 'two',
        ];

        $boxes = CheckboxList::widget()->name('g')->items($items)->render();
        $radios = RadioList::widget()->name('f')->items($items)->render();

        $I->assertSame(2, substr_count($boxes, 'name="g[]"'));
        $I->assertSame(2, substr_count($radios, 'name="f"'));
    }

    public function theGroupLabelPointsAtNobody(ModelTester $I): void
    {
        $I->wantTo('check that the group label does not point into the void');

        $html = RadioList::create(new ProfilForm(), 'formule')->items(['a' => 'A'])->render();

        preg_match('/<label(?: for="([^"]+)")?>Plan<\/label>/', $html, $found);

        $I->assertNotEmpty($found, 'the group carries its label');
        $I->assertArrayNotHasKey(1, $found, 'no for attribute, the list having no single field');
    }

    public function uploadRendersAFileField(ModelTester $I): void
    {
        $I->wantTo('render a native file field');

        $html = Upload::widget()
            ->name('attachment')
            ->accept([
                'pdf',
                'zip',
            ])
            ->multiple()
            ->render();

        $I->assertStringContainsString('type="file"', $html);
        $I->assertStringContainsString('name="attachment[]"', $html);
        $I->assertStringContainsString('accept=".pdf,.zip"', $html);
        $I->assertStringContainsString('multiple', $html);
    }

    public function noFieldSetsAnyClass(ModelTester $I): void
    {
        $I->wantTo('check that the package sets no class at all');

        $rendered = [
            Input::widget()->name('a')->render(),
            Textarea::widget()->name('b')->render(),
            Select::widget()->name('c')->optionsData(['1' => 'one'])->render(),
            Checkbox::widget()->name('d')->render(),
            Toggle::widget()->name('e')->render(),
            RadioList::widget()->name('f')->items(['1' => 'one'])->render(),
            CheckboxList::widget()->name('g')->items(['1' => 'one'])->render(),
            Upload::widget()->name('h')->render(),
        ];

        foreach ($rendered as $html) {
            $I->assertStringNotContainsString('class=', $html);
        }
    }
}
