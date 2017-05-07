<?php

namespace Test\Fractal;

use Appkr\Api\Commands\MakeTransformerCommand;

class MakeTransformerCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_is_instantiable()
    {
        $this->assertInstanceOf(MakeTransformerCommand::class, new MakeTransformerCommand);
    }
}
