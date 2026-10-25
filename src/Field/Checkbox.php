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
use Yiisoft\Form\Field\Base\ValidationClass\ValidationClassInterface;
use Yiisoft\Form\Field\Base\ValidationClass\ValidationClassTrait;
use Blackcube\Html\Html;

/**
 * Checkbox: the box on the left, the label and its explanation on the right.
 *
 * The template differs from the other fields - the label is not above but
 * beside - so the template and the containers are redefined here.
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class Checkbox extends AbstractField implements ValidationClassInterface
{
    use FieldDataTrait;
    use ValidationClassTrait;


    protected string $template = "{input}\n{label}\n{hint}\n{error}";

    protected ?string $inputContainerTag = 'div';


    private ?string $inputValue = null;
    private ?string $uncheckValue = null;
    protected ?bool $checked = null;
    protected bool $disabled = false;
    protected bool $required = false;

    /**
     * Value posted when the box is unchecked.
     */
    /**
     * Value of the value attribute: the checked state comes from comparing it
     * with the field value, as in Yiisoft\Form\Field\Checkbox.
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
    public function inputValue(bool|float|int|string|null $value): static
    {
        $new = clone $this;
        $new->inputValue = $this->prepareCheckboxValue($value);

        return $new;
    }

    public function uncheckValue(string $value): static
    {
        $new = clone $this;
        $new->uncheckValue = $value;

        return $new;
    }

    public function checked(bool $checked = true): static
    {
        $new = clone $this;
        $new->checked = $checked;

        return $new;
    }

    public function disabled(bool $disabled = true): static
    {
        $new = clone $this;
        $new->disabled = $disabled;

        return $new;
    }

    public function required(bool $required = true): static
    {
        $new = clone $this;
        $new->required = $required;

        return $new;
    }

    protected function prepareInputAttributes(array &$attributes): void
    {
        $describedBy = $this->describedById();
        if ($describedBy !== null) {
            $attributes['aria-describedby'] = $describedBy;
        }

        $this->addInputValidationClassToAttributes(
            $attributes,
            $this->getInputData(),
            $this->hasCustomError() ? true : null,
        );
    }

    private function prepareCheckboxValue(mixed $value): ?string
    {
        $prepared = null;

        if ($value !== null) {
            if (is_bool($value) === true) {
                $prepared = $value ? '1' : '0';
            } else {
                $prepared = (string) $value;
            }
        }

        return $prepared;
    }

    protected function generateInput(): string
    {
        $attributes = $this->getInputAttributes();
        $attributes['type'] = 'checkbox';
        Html::addCssClass($attributes, $this->fieldClasses($this->getInputClasses()));

        $inputValue = $this->inputValue;
        $inputValue ??= $this->prepareCheckboxValue($attributes['value'] ?? null);
        unset($attributes['value']);
        $inputValue ??= '1';

        $value = $inputValue;

        $checked = $this->checked;
        if ($checked === null) {
            $checked = $inputValue === $this->prepareCheckboxValue($this->getValue());
        }

        if ($checked === true) {
            $attributes['checked'] = true;
        }

        if ($this->disabled === true) {
            $attributes['disabled'] = true;
        }

        if ($this->required === true) {
            $attributes['required'] = true;
        }

        $attributes = [
            ...$attributes,
            ...$this->getFieldDataAttributes(),
        ];

        $box = $this->decorateBox(
            Html::input('checkbox', $this->getName(), $value, $attributes)->render()
        );

        if ($this->uncheckValue !== null) {
            $box = Html::input('hidden', $this->getName(), $this->uncheckValue)->render() . $box;
        }

        return $box;
    }

    /**
     * Renders the SVG checkmark
     */
    /**
     * Classes for l'input checkbox
     * @return string[]
     */
    protected function getInputClasses(): array
    {
        return [];
    }

    /**
     * Classes of the check drawn inside the box.
     *
     * @return string[]
     */
    /**
     * The box as it will be seen. The native one renders it alone; an interface
     * wraps it with what it takes to draw it.
     */
    protected function decorateBox(string $box): string
    {
        return $box;
    }

    /**
     * Color classes of the input (toggle states)
     * @return string[]
     */
    protected function getInputColorClasses(): array
    {
        return [];
    }

    /**
     * Disabled stroke class for the SVG
     */
    protected function getSvgDisabledStrokeClass(): string
    {
        return '';
    }
}
