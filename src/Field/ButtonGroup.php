<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm\Field;

use Blackcube\Html\Html;
use Blackcube\Html\Tag\Base\Tag;
use Yiisoft\Widget\Widget;

/**
 * ButtonGroup widget - buttons joined in a single block.
 *
 * The group styles its children: the ring, the corners and the one pixel
 * overlap come from here, the shade stays the one of each button.
 *
 * Usage:
 *   Bleet::buttonGroup()
 *       ->addButton(Bleet::button()->icon('pencil')->info())
 *       ->addButton(Bleet::a()->url('/edit')->icon('pencil')->info()->button())
 *       ->addButton(Bleet::button()->icon('trash')->danger())
 *       ->render()
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class ButtonGroup extends Widget
{
    /** @var array<Widget|Tag> */
    private array $buttons = [];

    /**
     * Adds a button (a form widget) or an anchor (a tag)
     */
    public function addButton(Widget|Tag $button): static
    {
        $new = clone $this;
        $new->buttons[] = $button;
        return $new;
    }

    /**
     * Sets all buttons
     * @param array<Widget|Tag> $buttons
     */
    public function buttons(array $buttons): static
    {
        $new = clone $this;
        $new->buttons = $buttons;
        return $new;
    }

    public function render(): string
    {
        $html = '';

        if (empty($this->buttons) === false) {
            $last = count($this->buttons) - 1;

            foreach ($this->buttons as $position => $button) {
                $html .= $this->renderButton($button, $position, $last);
            }

            $html = Html::span($html, ['class' => $this->prepareClasses()])
                ->encode(false)
                ->render();
        }

        return $html;
    }

    /**
     * A button of the group: the group styling wins, the shade stays
     * the one of the button.
     */
    protected function renderButton(Widget|Tag $button, int $position, int $last): string
    {
        $classes = $this->itemClasses($button, $position, $last);

        if ($classes !== []) {
            $button = $button->class(...$classes);
        }

        return $button->render();
    }

    /**
     * @return string[]
     */
    /**
     * Classes set on a button of the group, by position. The group styles its
     * children: an interface project decides here about the ring, the
     * corners and the overlap.
     *
     * @return string[]
     */
    protected function itemClasses(Widget|Tag $button, int $position, int $last): array
    {
        return [];
    }

    /**
     * The ring belongs to the group: a single shade for the whole block.
     */
    protected function getRingColorClass(): string
    {
        return '';
    }

    protected function getHoverColorClass(): string
    {
        return '';
    }

    /**
     * The content shade stays the one of the button.
     */
    protected function getTextColorClass(string $color): string
    {
        return '';
    }

    /**
     * @return string[]
     */
    protected function prepareClasses(): array
    {
        return [];
    }
}
