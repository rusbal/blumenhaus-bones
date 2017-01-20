<?php

namespace Rsu\Slugify;


class Slugify
{
    protected $hayStack = [];

    public function noConflict($str)
    {
        $str = $this->slugifyAgainstArray($str);
        array_push($this->hayStack, $str);
        return $str;
    }

    private function slugifyAgainstArray($str)
    {
        $slug = $str;
        $suffixCounter = 1;

        while (in_array($slug, $this->hayStack)) {
            $suffixCounter += 1;
            $slug = $str . '_' . $suffixCounter;
        }

        return $slug;
    }
}