<?php

namespace App\Utility;

class Collection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var array
     */
    protected $items = [];
    /**
     * @var array
     */
    protected $arrayedItems = [];

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * Returns true if collection has no items collected, false otherwise
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !(count($this->items));
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @param $offset
     * @param $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * @param $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * @param $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * @param $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset] ?? null;
    }

    /**
     * Returns the first collected item
     * @return mixed
     */
    public function first()
    {
        return reset($this->items);
    }

    /**
     * Returns the last collected item
     * @return mixed
     */
    public function last()
    {
        return end($this->items);
    }

    /**
     * Retrieve the first item from the collection and remove it
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * Retrieve the first item from the collection and remove it
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * Returns item with maximum $param property
     * @param $param
     * @return mixed
     */
    public function max($param)
    {
        $max = null;
        foreach ($this->items as $key => $item) {
            if ($max === null || (isset($item->{$param}) && (int)$item->{$param} > (int)$max->{$param})) {
                $max = $item;
            }
        }
        return $max;
    }


    /**
     * Returns true if item with key $key was collected, false otherwise
     * @param $key
     * @return bool
     */
    public function has($key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * Implode a column of items using $glue
     * @param $glue
     * @param $param
     * @return string
     */
    public function implode($glue, $param): string
    {
        return implode($glue, $this->getColumn($param));
    }


    /**
     * Get array consisting of all children's property $param values
     * @param $param
     * @return array
     */
    public function getColumn($param): array
    {
        $return = array();

        foreach ($this->items as $item) {
            $return[] = $item->{$param};
        }

        return $return;
    }

    /**
     * Check if any of collected item's property $param values is $value
     * @param $param
     * @param $value
     * @return bool
     */
    public function contains($param, $value): bool
    {
        foreach ($this->items as $item) {
            if ($item->{$param} == $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Iterates over each collected item and performs a callback on them.
     * @param \Closure $callback
     */
    public function each(\Closure $callback): void
    {
        foreach ($this->items as $itemKey => $itemValue) {
            $temporary = $callback($itemKey, $itemValue);
            if ($temporary !== null) {
                $this->items[$itemKey] = $temporary;
            }
        }
    }



    /**
     * Return a new collection of items from this collection using a filter
     * @see $this:filterItemIsCompatible()
     * @param string $arrKey Object's key for filtering
     * @param $operator string Can be (>, <, ==, !=, in, !empty, empty)
     * @param $arrValue string Expected key value
     * @return Collection
     */
    public function filter($arrKey, $operator, $arrValue = null): Collection
    {
        $return = array();

        if (!empty($this->items)) {
            foreach ($this->items as $item) {
                if ($this->filterItemIsCompatible($item[$arrKey], $operator, $arrValue)) {
                    $return[] = $item;
                }
            }
        }

        return new self($return);
    }

    /**
     * Performs a check for the item
     * @param $givenVal
     * @param $operator
     * @param null $expectedVal
     * @return mixed
     */
    protected function filterItemIsCompatible($givenVal, $operator, $expectedVal = null)
    {
        $map = array(
            ">"  => function ($givenVal, $expectedVal) {
                return $givenVal >  $expectedVal;
            },
            ">="  => function ($givenVal, $expectedVal) {
                return $givenVal >=  $expectedVal;
            },
            "<"  => function ($givenVal, $expectedVal) {
                return $givenVal <  $expectedVal;
            },
            "<="  => function ($givenVal, $expectedVal) {
                return $givenVal <=  $expectedVal;
            },
            "==" => function ($givenVal, $expectedVal) {
                return $givenVal == $expectedVal;
            },
            "!=" => function ($givenVal, $expectedVal) {
                return $givenVal != $expectedVal;
            },
            "!empty" => function ($givenVal, $expectedVal) {
                return !empty($givenVal);
            },
            "empty" => function ($givenVal, $expectedVal) {
                return empty($givenVal);
            },
            "in" => function ($givenVal, array $expectedVal) {
                return in_array($givenVal, $expectedVal);
            },
            'not in' => function ($givenVal, array $expectedVal) {
                return !in_array($givenVal, $expectedVal);
            },
            'contains' => function($givenVal, $expectedVal) {
                return preg_match("#{$expectedVal}#i", $givenVal);
            },
        );
        $func = $map[$operator];

        return ($func($givenVal, $expectedVal));
    }

    /**
     * Get Collection items and each child as a multidimensional array
     * @return array
     */
    public function asArray(): array
    {
        if (!empty($this->arrayedItems)) {
            return $this->arrayedItems;
        }

        foreach ($this->items as $item) {
            if (is_object($item)) {
                $item = $item->asArray();
            }

            $this->arrayedItems[] = $item;
        }

        return $this->arrayedItems;
    }

    /**
     * @param $callable
     * @return void
     */
    public function usort($callable): void
    {
        usort($this->items, $callable);
    }
}
