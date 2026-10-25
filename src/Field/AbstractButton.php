<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm\Field;

use Blackcube\BridgeForm\Traits\AttributesTrait;
use Blackcube\BridgeForm\Traits\FieldDataTrait;
use Yiisoft\Form\Field\Base\PartsField;
use Blackcube\Html\Html;
use Blackcube\Html\Tag\Button;

/**
 * Bleet button base.
 *
 * Extends PartsField and takes over the ButtonField interface, whose rendering
 * is sealed and whose attributes are private: the button is therefore assembled
 * here, at render time.
 *
 * The Bleet styling only applies when the call has not set its own classes; as
 * soon as it gives some, those are the ones that count.
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
abstract class AbstractButton extends PartsField
{
    use AttributesTrait;
    use FieldDataTrait;

    protected string $template = '{input}';
    protected bool $useContainer = false;

    protected ?Button $button = null;
    protected array $buttonAttributes = [];


    protected ?string $iconName = null;
    protected string $iconType = 'outline';
    protected ?int $badge = null;
    protected ?string $text = null;
    protected bool $outline = false;
    protected bool $ghost = false;
    protected bool $inverse = false;

    public function button(?Button $button): static
    {
        $new = clone $this;
        $new->button = $button;

        return $new;
    }

    public function buttonAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->buttonAttributes = $attributes;

        return $new;
    }

    public function addButtonAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->buttonAttributes = array_merge($this->buttonAttributes, $attributes);

        return $new;
    }

    public function buttonId(?string $id): static
    {
        $new = clone $this;
        $new->buttonAttributes['id'] = $id;

        return $new;
    }

    public function buttonClass(?string ...$class): static
    {
        $new = clone $this;
        $definedClasses = [];
        foreach ($class as $oneClass) {
            if ($oneClass !== null) {
                $definedClasses[] = $oneClass;
            }
        }

        $new->classesReplaced = true;
        $new->buttonAttributes['class'] = $definedClasses;

        return $new;
    }

    public function addButtonClass(?string ...$class): static
    {
        $new = clone $this;
        Html::addCssClass($new->buttonAttributes, $class);

        return $new;
    }

    public function name(?string $name): static
    {
        $new = clone $this;
        $new->buttonAttributes['name'] = $name;

        return $new;
    }

    public function value(mixed $value): static
    {
        $new = clone $this;
        $new->buttonAttributes['value'] = $value;

        return $new;
    }

    public function ariaDescribedBy(?string ...$value): static
    {
        $new = clone $this;
        $definedValues = [];
        foreach ($value as $oneValue) {
            if ($oneValue !== null) {
                $definedValues[] = $oneValue;
            }
        }

        $new->buttonAttributes['aria-describedby'] = $definedValues;

        return $new;
    }

    public function ariaLabel(?string $value): static
    {
        $new = clone $this;
        $new->buttonAttributes['aria-label'] = $value;

        return $new;
    }

    public function autofocus(bool $value = true): static
    {
        $new = clone $this;
        $new->buttonAttributes['autofocus'] = $value;

        return $new;
    }

    public function tabIndex(?int $value): static
    {
        $new = clone $this;
        $new->buttonAttributes['tabindex'] = $value;

        return $new;
    }

    public function disabled(bool $disabled = true): static
    {
        $new = clone $this;
        $new->buttonAttributes['disabled'] = $disabled;

        return $new;
    }

    public function form(?string $id): static
    {
        $new = clone $this;
        $new->buttonAttributes['form'] = $id;

        return $new;
    }

    public function text(string $text): static
    {
        $new = clone $this;
        $new->text = $text !== '' ? $text : null;

        return $new;
    }

    public function icon(string $name, string $type = 'outline'): static
    {
        $new = clone $this;
        $new->iconName = $name;
        $new->iconType = $type;

        return $new;
    }

    public function badge(int $count): static
    {
        $new = clone $this;
        $new->badge = $count;

        return $new;
    }

    public function outline(bool $outline = true): static
    {
        $new = clone $this;
        $new->outline = $outline;

        return $new;
    }

    public function ghost(bool $ghost = true): static
    {
        $new = clone $this;
        $new->ghost = $ghost;

        return $new;
    }

    public function inverse(bool $inverse = true): static
    {
        $new = clone $this;
        $new->inverse = $inverse;

        return $new;
    }

    public function hasIcon(): bool
    {
        return $this->iconName !== null;
    }

    public function hasBadge(): bool
    {
        return $this->badge !== null;
    }

    /**
     * The button is assembled here: the styling is only added when the call
     * has not set its own classes.
     */
    protected function generateInput(): string
    {
        $button = $this->button;
        if ($button === null) {
            $button = Html::button();
        }
        $button = $button->type($this->getType());

        $attributes = $this->prepareTagAttributes($this->buttonAttributes);
        $attributes = [
            ...$attributes,
            ...$this->getFieldDataAttributes(),
        ];

        if ($this->classesReplaced === false) {
            $added = $attributes['class'] ?? null;
            unset($attributes['class']);
            Html::addCssClass($attributes, $this->prepareClasses());

            if ($added !== null) {
                Html::addCssClass($attributes, $added);
            }
        }

        if ($attributes !== []) {
            $button = $button->addAttributes($attributes);
        }

        $content = $this->buildContent();
        if ($content !== null && $content !== '') {
            $button = $button->content($content)->encode(false);
        }

        return $button->render();
    }

    abstract protected function getType(): string;

    /**
     * Build content with icon and/or badge
     */
    /**
     * What the interface puts before the text, an icon for instance.
     */
    protected function renderBeforeText(): string
    {
        return '';
    }

    /**
     * What the interface puts after the text, a badge for instance.
     */
    protected function renderAfterText(): string
    {
        return '';
    }

    protected function buildContent(): ?string
    {
        $parts = [];

        $before = $this->renderBeforeText();
        if ($before !== '') {
            $parts[] = $before;
        }

        if ($this->text !== null) {
            $parts[] = Html::encode($this->text);
        }

        $after = $this->renderAfterText();
        if ($after !== '') {
            $parts[] = $after;
        }

        $content = null;
        if (empty($parts) === false) {
            $content = implode('', $parts);
        }

        return $content;
    }

    /**
     * @return string[]
     */
    protected function prepareClasses(): array
    {
        return [];
    }

    /**
     * Classes of the button icon.
     *
     * @return string[]
     */
    protected function iconClasses(): array
    {
        return [];
    }

    /**
     * Classes of the badge set on the button.
     *
     * @return string[]
     */
    protected function badgeClasses(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    protected function getColorClasses(): array
    {
        return [];
    }

    protected function getSizeClasses(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    /**
     * Rounding by size: the two smallest ones are less rounded.
     */
    protected function roundedClass(): string
    {
        return '';
    }
}
