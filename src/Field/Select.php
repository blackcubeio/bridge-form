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
 * The native dropdown: a select element, its options and its groups.
 *
 * An interface that wants more - a search, tags, a drawn panel - inherits from
 * this field and renders its own.
 *
 * Usage:
 *   Select::create($model, 'status')->optionsData(['1' => 'Actif'])->render()
 *   Select::widget()->name('status')->optionsData([...])->prompt('--')->render()
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class Select extends AbstractField
{
    use FieldDataTrait;
    use WrapperAttributesTrait;

    protected string $prompt = '';
    /** @var array<string, string|array<string, string>> */
    protected array $options = [];
    protected bool $disabled = false;
    protected bool $multiple = false;
    protected ?string $labelledBy = null;
    protected ?string $describedBy = null;

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
     * L'option vide en tete de liste.
     */
    public function prompt(?string $text): static
    {
        $new = clone $this;
        $new->prompt = $text ?? '';

        return $new;
    }

    /**
     * The options, or the option groups.
     *
     * @param array<string, string|array<string, string>> $data
     */
    public function optionsData(array $data): static
    {
        $new = clone $this;
        $new->options = $data;

        return $new;
    }

    public function disabled(bool $disabled = true): static
    {
        $new = clone $this;
        $new->disabled = $disabled;

        return $new;
    }

    public function multiple(bool $multiple = true): static
    {
        $new = clone $this;
        $new->multiple = $multiple;

        return $new;
    }

    /**
     * The ids of the external label (aria-labelledby).
     */
    public function ariaLabelledBy(?string ...$value): static
    {
        $new = clone $this;
        $new->labelledBy = $this->joinIds($value);

        return $new;
    }

    /**
     * The ids of the external description (aria-describedby).
     */
    public function ariaDescribedBy(?string ...$value): static
    {
        $new = clone $this;
        $new->describedBy = $this->joinIds($value);

        return $new;
    }

    protected function generateInput(): string
    {
        $name = $this->getName() ?? '';
        $selected = $this->getValue();

        if ($this->multiple === true && str_ends_with($name, '[]') === false) {
            $name .= '[]';
        }

        $attributes = $this->getInputAttributes();
        Html::addCssClass($attributes, $this->fieldClasses($this->prepareClasses()));

        if ($this->disabled === true) {
            $attributes['disabled'] = true;
        }

        if ($this->multiple === true) {
            $attributes['multiple'] = true;
        }

        if ($this->labelledBy !== null) {
            $attributes['aria-labelledby'] = $this->labelledBy;
        }

        $describedBy = $this->describedBy ?? $this->describedById();
        if ($describedBy !== null) {
            $attributes['aria-describedby'] = $describedBy;
        }

        $attributes = [
            ...$attributes,
            ...$this->getFieldDataAttributes(),
        ];

        $select = Html::select()
            ->attributes($attributes)
            ->name($name)
            ->optionsData($this->options, encode: true)
            ->value($selected);

        if ($this->prompt !== '') {
            $select = $select->prompt($this->prompt);
        }

        return $select->render();
    }

    /**
     * @param array<int, string|null> $ids
     */
    private function joinIds(array $ids): ?string
    {
        $defined = [];
        foreach ($ids as $id) {
            if ($id !== null) {
                $defined[] = $id;
            }
        }

        return $defined === [] ? null : implode(' ', $defined);
    }

    /**
     * @return string[]
     */
    protected function prepareClasses(): array
    {
        return [];
    }
}
