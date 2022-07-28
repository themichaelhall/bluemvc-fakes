<?php

/**
 * This file is a part of the bluemvc-fakes package.
 *
 * Read more at https://bluemvc.com/
 */

declare(strict_types=1);

namespace BlueMvc\Fakes\Collections;

use BlueMvc\Core\Interfaces\Collections\SessionItemCollectionInterface;

/**
 * BlueMvc fake session item collection class.
 *
 * @since 1.0.0
 */
class FakeSessionItemCollection implements SessionItemCollectionInterface
{
    /**
     * Constructs the collection of session items.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->items = [];
    }

    /**
     * Returns the number of session items.
     *
     * @since 1.0.0
     *
     * @return int The number of session items.
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Returns the current session item value.
     *
     * @since 1.0.0
     *
     * @return mixed The current session item value.
     */
    public function current(): mixed
    {
        return current($this->items);
    }

    /**
     * Returns the session item value by session item name if it exists, null otherwise.
     *
     * @since 1.0.0
     *
     * @param string $name The session item name.
     *
     * @return mixed The session item value by session item name if it exists, null otherwise.
     */
    public function get(string $name): mixed
    {
        if (!isset($this->items[$name])) {
            return null;
        }

        return $this->items[$name];
    }

    /**
     * Returns the current session item name.
     *
     * @since 1.0.0
     *
     * @return string The current session item name.
     */
    public function key(): string
    {
        return strval(key($this->items));
    }

    /**
     * Moves forwards to the next session item.
     *
     * @since 1.0.0
     */
    public function next(): void
    {
        next($this->items);
    }

    /**
     * Removes a session item by session item name.
     *
     * @since 1.0.0
     *
     * @param string $name The session item name.
     */
    public function remove(string $name): void
    {
        unset($this->items[$name]);
    }

    /**
     * Rewinds the session item collection to first element.
     *
     * @since 1.0.0
     */
    public function rewind(): void
    {
        reset($this->items);
    }

    /**
     * Sets a session item value by session item name.
     *
     * @since 1.0.0
     *
     * @param string $name  The session item name.
     * @param mixed  $value The session item value.
     */
    public function set(string $name, mixed $value): void
    {
        $this->items[$name] = $value;
    }

    /**
     * Returns true if the current session item is valid.
     *
     * @since 1.0.0
     *
     * @return bool True if the current session item is valid.
     */
    public function valid(): bool
    {
        return key($this->items) !== null;
    }

    /**
     * @var array<string, mixed> The items.
     */
    private array $items;
}
