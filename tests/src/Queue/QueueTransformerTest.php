<?php

namespace tests\eLife\Bus\Queue;

use eLife\Bus\Queue\SqsMessageTransformer;
use LogicException;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

final class QueueTransformerTest extends PHPUnit_Framework_TestCase
{
    /** @var SqsMessageTransformer transformer */
    private $transformer;

    public function setUp()
    {
        $ref = new ReflectionClass(SqsMessageTransformer::class);
        $this->transformer = $ref->newInstanceWithoutConstructor();
    }

    public function test_can_instantiate_with_mock()
    {
        $this->assertInstanceOf(SqsMessageTransformer::class, $this->transformer);
    }

    public function test_can_transform_sqs_message()
    {
        $message = SqsMessageTransformer::fromMessage([
            'Messages' => [
                [
                    'MessageId' => 'id-1234',
                    'Body' => $body = '{"id": "1234", "type": "blog-article"}',
                    'MD5OfBody' => md5($body),
                    'ReceiptHandle' => 'very-long-string-thing',
                ],
            ],
        ]);

        $this->assertEquals($message->getId(), '1234');
        $this->assertEquals($message->getType(), 'blog-article');
        $this->assertEquals($message->getReceipt(), 'very-long-string-thing');
    }
    public function test_can_transform_sqs_message_failure()
    {
        $this->expectException(LogicException::class);
        SqsMessageTransformer::fromMessage([
            'Messages' => [
                [
                    'MessageId' => 'id-1234',
                    'Body' => $body = '{"id": "1234", "type": "blog-article"}',
                    'MD5OfBody' => md5($body.'-md5-mismatch'),
                    'ReceiptHandle' => 'very-long-string-thing',
                ],
            ],
        ]);
    }
}
