<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm\Tests\Support;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

/**
 * A real form model, with its properties, its rules and its labels. The
 * fields read it as they would read any model.
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class ProfilForm extends FormModel implements RulesProviderInterface
{
    public function __construct(
        private string $nom = '',
        private string $courriel = '',
        private string $presentation = '',
        private ?string $pays = null,
        private bool $lettre = false,
        private ?string $formule = null,
        private array $centresInteret = [],
    ) {
    }

    public function getRules(): array
    {
        return [
            'nom' => [
                new Required(),
                new Length(min: 2, max: 60),
            ],
            'courriel' => [
                new Required(),
                new Email(),
            ],
        ];
    }

    public function getPropertyLabels(): array
    {
        return [
            'nom' => 'Full name',
            'courriel' => 'Email address',
            'presentation' => 'Presentation',
            'pays' => 'Country',
            'lettre' => 'Receive the newsletter',
            'formule' => 'Plan',
            'centresInteret' => 'Interests',
        ];
    }

    public function getPropertyHints(): array
    {
        return [
            'nom' => 'As it will appear on your profile',
            'courriel' => 'We never share it',
        ];
    }

    public function getPropertyPlaceholders(): array
    {
        return [
            'courriel' => 'you@example.com',
        ];
    }
}
