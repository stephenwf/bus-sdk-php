<?php

namespace eLife\Bus\Queue\Mock;

use eLife\Bus\Queue\QueueItem;
use eLife\Bus\Queue\WatchableQueue;

final class WatchableQueueMock implements WatchableQueue
{
    private $items = [];
    private $process = [];

    public function __construct(QueueItem ...$items)
    {
        $this->items = $items;
    }

    /**
     * Adds item to the queue.
     *
     * Mock: Add item to queue.
     */
    public function enqueue(QueueItem $item)
    {
        array_push($this->items, $item);
    }

    /**
     * Mock: Move to separate "in progress" queue.
     */
    public function dequeue()
    {
        $item = array_pop($this->items);

        return $this->process[$item->getReceipt()] = $item;
    }

    /**
     * Mock: Remove item completely.
     */
    public function commit(QueueItem $item)
    {
        unset($this->process[$item->getReceipt()]);
    }

    /**
     * Mock: re-add to queue.
     */
    public function release(QueueItem $item) : bool
    {
        array_unshift($this->items, $item);

        return true;
    }

    public function clean()
    {
        $this->items = [];
    }

    public function count() : int
    {
        return count($this->items);
    }
}
