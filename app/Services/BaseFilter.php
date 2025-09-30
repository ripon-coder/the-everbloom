<?php
namespace App\Services;

use Illuminate\Pipeline\Pipeline;
abstract class BaseFilter
{
    abstract protected function getFilters();
    public function getResults(array $contents)
    {
        $result = app(Pipeline::class)
            ->send($contents)
            ->through($this->getFilters())
            ->thenReturn();

        return $result['query'];
    }
}