<?php

namespace Byscripts\HtmlAttributes;

trait HtmlAttributesTrait
{
    private $attributes = [];
    private $classes = [];

    private function parseClass(array $classes)
    {
        $flatClasses = [];
        array_walk_recursive(
            $classes,
            function ($x) use (&$flatClasses) {
                $flatClasses[] = $x;
            }
        );

        return array_fill_keys(explode(' ', implode(' ', $flatClasses)), true);
    }

    public function addClass($classes)
    {
        $this->classes += $this->parseClass(func_get_args());

        return $this;
    }

    public function removeClass($classes)
    {
        $this->classes = array_diff_key($this->classes, $this->parseClass(func_get_args()));

        return $this;
    }

    public function setClasses($classes)
    {
        $this->classes = $this->parseClass(func_get_args());

        return $this;
    }

    public function hasClass($class)
    {
        return !empty($this->classes[ $class ]);
    }

    public function setAttribute($name, $value)
    {
        if(null === $value) {
            return $this->removeAttribute($name);
        }

        if ('class' === $name) {
            $this->addClass($value);
        } else {
            $this->attributes[ $name ] = (string)$value;
        }

        return $this;
    }

    public function removeAttribute($name)
    {
        if('class' === $name) {
            $this->classes = [];
        } else {
            unset($this->attributes[ $name ]);
        }

        return $this;
    }

    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    public function setAttributes(array $attributes = null, $replace = false)
    {
        if (true === $replace) {
            $this->attributes = [];
            $this->classes    = [];
        }

        if (empty($attributes)) {
            return $this;
        }

        array_walk(
            $attributes,
            function ($value, $name) {
                $this->setAttribute($name, $value);
            }
        );

        return $this;
    }

    public function renderAttributes()
    {
        $output = sprintf('class="%s"', implode(' ', array_keys($this->classes)));

        foreach ($this->attributes as $name => $value) {
            $output .= sprintf(' %s="%s"', $name, htmlentities($value, ENT_QUOTES, 'UTF-8'));
        }

        return $output;
    }
}