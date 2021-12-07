<?php

namespace TaiwanSms\Mitake\Tests;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use TaiwanSms\Mitake\MitakeMessage;

class MitakeMessageTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testConstruct()
    {
        $message = new MitakeMessage(
            $content = 'foo'
        );

        $this->assertSame($content, $message->content);
    }

    public function testContent()
    {
        $message = new MitakeMessage();
        $message->content(
            $content = 'foo'
        );

        $this->assertSame($content, $message->content);
    }

    public function testCreate()
    {
        $message = MitakeMessage::create(
            $content = 'foo'
        );

        $this->assertSame($content, $message->content);
    }
}
