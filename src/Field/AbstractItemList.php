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
use Blackcube\Html\Html;
use Yiisoft\Widget\Widget;

/**
 * Item list base: a field that renders a series of small fields.
 *
 * The loop lives here. What tells one list from another comes down to four
 * points: the item produced, the way it carries its value, how its selection
 * is decided, and the name passed to each one.
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
abstract class AbstractItemList extends AbstractField
{
    use FieldDataTrait;
    use WrapperAttributesTrait;

    /** @var array<string, string> */
    protected array $items = [];
    protected bool $disabled = false;
    protected bool $required = false;

    /**
     * @param array<string, string> $items
     */
    public function items(array $items): static
    {
        $new = clone $this;
        $new->items = $items;

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

    /**
     * The list label points at no field: the list has no single input, each
     * item carries its own.
     */
    public function fieldParts(): static
    {
        return parent::fieldParts()->shouldSetInputId(false);
    }

    protected function generateInput(): string
    {
        $inputData = $this->getInputData();
        $name = $this->itemsName($this->getName());
        $baseId = $this->getTagAttributes()['id'] ?? null;
        $baseId ??= $inputData->getId();
        $baseId ??= $this->defaultBaseId();

        $html = Html::openTag('div', $this->prepareWrapperAttributes([
            'class' => implode(' ', $this->listClasses()),
        ]));

        $position = 0;
        foreach ($this->items as $itemValue => $itemLabel) {
            $item = $this->prepareItem(
                (string) $itemValue,
                $itemLabel,
                Html::generateId($baseId . '-'),
                $position,
            );

            if ($name !== null) {
                $item = $item->name($name);
            }

            if ($this->disabled === true) {
                $item = $item->disabled();
            }

            if ($this->fieldData !== []) {
                $item = $item->fieldData($this->fieldData);
            }

            $html .= $item->render();
            $position++;
        }

        $html .= Html::closeTag('div');

        return $html;
    }

    /**
     * The item, its value, its label, its id and its selection state: that is
     * everything that tells one list from another.
     */
    abstract protected function prepareItem(string $value, string $label, string $id, int $position): Widget;

    /**
     * Is the item selected?
     */
    abstract protected function isSelected(string $value): bool;

    /**
     * The name passed to each item.
     */
    protected function itemsName(?string $name): ?string
    {
        return $name;
    }

    /**
     * The id prefix when the field does not provide one.
     */
    abstract protected function defaultBaseId(): string;

    /**
     * Classes of the list container.
     *
     * @return string[]
     */
    protected function listClasses(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    protected function prepareClasses(): array
    {
        return [];
    }
}
