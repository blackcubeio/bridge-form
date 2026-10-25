<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm\Field;

use Yiisoft\FormModel\FormModelInterface;
use Yiisoft\FormModel\FormModelInputData;
use Blackcube\BridgeForm\Traits\FieldDataTrait;
use Blackcube\BridgeForm\Traits\FloatingLabelTrait;
use Blackcube\BridgeForm\Traits\WrapperAttributesTrait;
use Yiisoft\Form\Field\Base\EnrichFromValidationRules\EnrichFromValidationRulesInterface;
use Yiisoft\Form\Field\Base\EnrichFromValidationRules\EnrichFromValidationRulesTrait;
use Yiisoft\Form\Field\Base\Placeholder\PlaceholderInterface;
use Yiisoft\Form\Field\Base\Placeholder\PlaceholderTrait;
use Yiisoft\Form\Field\Base\ValidationClass\ValidationClassInterface;
use Yiisoft\Form\Field\Base\ValidationClass\ValidationClassTrait;
use Blackcube\Html\Html;

/**
 * Input field, without any styling.
 *
 * Usage:
 *   Input::widget()->name('email')->placeholder('you@example.com')->render()
 *   Input::widget()->password()->name('pwd')->render()
 *   Input::widget()->inputData(new FormModelInputData($model, 'email'))->render()
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class Input extends AbstractField implements EnrichFromValidationRulesInterface, PlaceholderInterface, ValidationClassInterface
{
    use FieldDataTrait;
    use FloatingLabelTrait;
    use WrapperAttributesTrait;
    use EnrichFromValidationRulesTrait;
    use PlaceholderTrait;
    use ValidationClassTrait;


    protected string $type = 'text';
    protected bool $disabled = false;
    protected bool $readonly = false;
    protected bool $required = false;
    private ?string $labelledBy = null;
    private ?string $describedBy = null;
    private ?string $autocomplete = null;

    /**
     * Sets the input type (generic)
     */

    /**
     * Creates the field for a model property.
     */
    public static function create(?FormModelInterface $model = null, ?string $property = null): static
    {
        $field = static::widget();

        if ($model !== null && $property !== null) {
            $field = $field->inputData(new FormModelInputData($model, $property));
        }

        return $field->fieldParts();
    }
    public function type(string $type): static
    {
        $new = clone $this;
        $new->type = $type;
        return $new;
    }

    /**
     * Text type (default)
     */
    public function text(): static
    {
        return $this->type('text');
    }

    /**
     * Type password
     */
    public function password(): static
    {
        return $this->type('password');
    }

    /**
     * Type email
     */
    public function email(): static
    {
        return $this->type('email');
    }

    /**
     * Type number
     */
    public function number(): static
    {
        return $this->type('number');
    }

    /**
     * Type date
     */
    public function date(): static
    {
        return $this->type('date');
    }

    /**
     * Type hidden (aucune classe)
     */
    public function hidden(): static
    {
        $new = $this->type('hidden');
        $new->template = '{input}';
        $new->useContainer = false;
        $new->inputContainerTag = null;

        return $new;
    }

    /**
     * Type tel (autocomplete implicite: tel)
     */
    public function telephone(): static
    {
        return $this->type('tel');
    }

    /**
     * Marks the field as disabled
     */
    public function disabled(bool $disabled = true): static
    {
        $new = clone $this;
        $new->disabled = $disabled;
        return $new;
    }

    /**
     * Marks the field as readonly
     */
    public function readonly(bool $readonly = true): static
    {
        $new = clone $this;
        $new->readonly = $readonly;
        return $new;
    }

    /**
     * Marks the field as required
     */
    public function required(bool $required = true): static
    {
        $new = clone $this;
        $new->required = $required;
        return $new;
    }

    /**
     * Sets aria-labelledby
     */
    public function ariaLabelledBy(?string ...$value): static
    {
        $definedValues = [];
        foreach ($value as $oneValue) {
            if ($oneValue !== null) {
                $definedValues[] = $oneValue;
            }
        }

        $new = clone $this;
        $new->labelledBy = $definedValues === [] ? null : implode(' ', $definedValues);

        return $new;
    }

    /**
     * Sets aria-describedby
     */
    public function ariaDescribedBy(?string ...$value): static
    {
        $definedValues = [];
        foreach ($value as $oneValue) {
            if ($oneValue !== null) {
                $definedValues[] = $oneValue;
            }
        }

        $new = clone $this;
        $new->describedBy = $definedValues === [] ? null : implode(' ', $definedValues);

        return $new;
    }

    /**
     * Sets l'attribut autocomplete (override the value implicite)
     */
    public function autocomplete(string $value): static
    {
        $new = clone $this;
        $new->autocomplete = $value;
        return $new;
    }

    protected function prepareInputAttributes(array &$attributes): void
    {
        $describedBy = $this->describedById();
        if ($describedBy !== null) {
            $attributes['aria-describedby'] = $describedBy;
        }

        $this->preparePlaceholderInInputAttributes($attributes);
        $this->addInputValidationClassToAttributes(
            $attributes,
            $this->getInputData(),
            $this->hasCustomError() ? true : null,
        );
    }

    protected function generateInput(): string
    {
        if ($this->floatingLabel !== null) {
            $input = $this->renderFloatingLabel();
        } else {
            $input = $this->renderInput($this->prepareClasses());
        }

        return $input;
    }

    /**
     * True when the field carries a validation error.
     */
    protected function hasFieldError(): bool
    {
        return $this->getInputData()->getValidationErrors() !== [];
    }

    /**
     * Render l'input simple
     */
    protected function renderInput(array $classes): string
    {
        $name = $this->getName();
        $value = $this->getValue();
        $required = $this->required;

        $defaults = [
            'type' => $this->type,
            ...$this->getInputAttributes(),
        ];

        if ($this->type === 'hidden') {
            unset($defaults['placeholder']);
        }

        if ($this->hasFieldError() === true) {
            $defaults['aria-invalid'] = 'true';
        }

        if ($this->disabled === true) {
            $defaults['disabled'] = true;
        }

        if ($this->readonly === true) {
            $defaults['readonly'] = true;
        }

        if ($required) {
            $defaults['required'] = true;
        }

        if ($this->labelledBy !== null) {
            $defaults['aria-labelledby'] = $this->labelledBy;
        }

        if ($this->describedBy !== null) {
            $defaults['aria-describedby'] = $this->describedBy;
        }

        $autocomplete = $this->autocomplete ?? $this->getImplicitAutocomplete();
        if ($autocomplete !== null) {
            $defaults['autocomplete'] = $autocomplete;
        }

        $attributes = $this->prepareTagAttributes($defaults);

        if ($this->type !== 'hidden') {
            Html::addCssClass($attributes, $this->fieldClasses($classes));
        }

        if($this->type === 'date' && $value !== null && $value instanceof \DateTimeImmutable) {
            $value = $value->format('Y-m-d');
            $attributes['value'] = $value;
        } elseif($this->type === 'datetime-local' && $value !== null && $value instanceof \DateTimeImmutable) {
            $value = $value->format('Y-m-d\TH:i');
            $attributes['value'] = $value;
        }

        $attributes = [
            ...$attributes,
            ...$this->getFieldDataAttributes(),
        ];

        return Html::input($this->type, $name, $value, $attributes)->render();
    }

    /**
     * Renders with label flottant
     */
    protected function renderFloatingControl(): string
    {
        return $this->renderInput($this->prepareFloatingInputClasses());
    }

    /**
     * Classes for input simple
     * @return string[]
     */
    protected function prepareClasses(): array
    {
        return [];
    }

    /**
     * Classes for input en mode floating label
     * @return string[]
     */
    protected function prepareFloatingInputClasses(): array
    {
        return [];
    }

    /**
     * Outline color class of the floating wrapper (static border)
     */
    protected function getFloatingOutlineColorClass(): string
    {
        return '';
    }

    /**
     * focus-within outline color class for the floating wrapper (border on focus)
     */
    protected function getFloatingFocusWithinOutlineColorClass(): string
    {
        return '';
    }

    /**
     * Returns the implicit autocomplete value according to the type
     */
    protected function getImplicitAutocomplete(): ?string
    {
        return match ($this->type) {
            'email' => 'email',
            'password' => 'current-password',
            'tel' => 'tel',
            default => null,
        };
    }
}
