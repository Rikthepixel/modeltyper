<?php

namespace Tests\Feature\Actions;

use Rikthepixel\ModelTyper\Actions\GenerateCliOutput;
use Rikthepixel\ModelTyper\Actions\GetMappings;
use Rikthepixel\ModelTyper\Actions\GetModels;
use Tests\TestCase;

class GenerateCliOutputTest extends TestCase
{
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(GenerateCliOutput::class, resolve(GenerateCliOutput::class));
    }

    public function test_action_can_be_executed()
    {
        $action = app(GenerateCliOutput::class);
        $result = $action(app(GetModels::class)(), app(GetMappings::class)());

        $this->assertIsString($result);
    }
}
