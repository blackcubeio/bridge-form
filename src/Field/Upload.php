<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm\Field;

use Blackcube\BridgeForm\Traits\FieldDataTrait;
use Blackcube\BridgeForm\Traits\WrapperAttributesTrait;
use Yiisoft\FormModel\FormModelInputData;
use Yiisoft\FormModel\FormModelInterface;
use Blackcube\Html\Html;

/**
 * The native file field: an input of type file.
 *
 * An interface that wants a drop zone, a preview or a chunked upload inherits
 * from this field and renders its own.
 *
 * Usage:
 *   Upload::create($model, 'document')->accept(['pdf'])->render()
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class Upload extends AbstractField
{
    use FieldDataTrait;
    use WrapperAttributesTrait;

    /** @var string[] */
    protected array $accept = [];
    protected bool $multiple = false;
    protected bool $disabled = false;
    protected bool $required = false;

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

    /**
     * The accepted extensions or types.
     *
     * @param string[] $accept
     */
    public function accept(array $accept): static
    {
        $new = clone $this;
        $new->accept = $accept;

        return $new;
    }

    public function multiple(bool $multiple = true): static
    {
        $new = clone $this;
        $new->multiple = $multiple;

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

    protected function generateInput(): string
    {
        $name = $this->getName() ?? '';

        if ($this->multiple === true && str_ends_with($name, '[]') === false) {
            $name .= '[]';
        }

        $attributes = $this->getInputAttributes();
        Html::addCssClass($attributes, $this->fieldClasses($this->prepareClasses()));

        if ($this->accept !== []) {
            $attributes['accept'] = implode(',', $this->normalizeAccept());
        }

        if ($this->multiple === true) {
            $attributes['multiple'] = true;
        }

        if ($this->disabled === true) {
            $attributes['disabled'] = true;
        }

        if ($this->required === true) {
            $attributes['required'] = true;
        }

        $describedBy = $this->describedById();
        if ($describedBy !== null) {
            $attributes['aria-describedby'] = $describedBy;
        }

        $attributes = [
            ...$attributes,
            ...$this->getFieldDataAttributes(),
        ];

        return Html::input('file', $name, null, $attributes)->render();
    }

    /**
     * A bare extension becomes an accepted suffix.
     *
     * @return string[]
     */
    protected function normalizeAccept(): array
    {
        $accepted = [];

        foreach ($this->accept as $one) {
            $one = trim($one);

            if ($one !== '' && str_contains($one, '/') === false && str_starts_with($one, '.') === false) {
                $one = '.' . $one;
            }

            if ($one !== '') {
                $accepted[] = $one;
            }
        }

        return $accepted;
    }

    /**
     * @return string[]
     */
    protected function prepareClasses(): array
    {
        return [];
    }
}
