<?php

namespace Rikthepixel\ModelTyper\Traits;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Rikthepixel\ModelTyper\Actions\RunModelInspector;

/**
 * @phpstan-import-type ModelInspectorResult from RunModelInspector
 */
trait ModelRefClass
{
    /**
     * Get the reflection interface.
     *
     * @param  ModelInspectorResult  $info
     * @return ReflectionClass<Model>
     */
    public function getRefInterface(array $info): ReflectionClass
    {
        return new ReflectionClass($info['class']);
    }
}
