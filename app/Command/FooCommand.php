<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Hyperf\Amqp\Producer;
use App\Amqp\Producer\DemoProducer;
use Hyperf\Context\ApplicationContext;


#[Command]
class FooCommand extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('demo:command');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    public function handle()
    {
    	$wg = new \Hyperf\Utils\WaitGroup();
        $wg->add(1000);
        
        for ($i = 0; $i < 1000; $i++) {
            co(function () use ($wg) {
               $message = new DemoProducer(['aaa' => 111]);
               $producer = ApplicationContext::getContainer()->get(Producer::class);
               $producer->produce($message);
               $wg->done();
            });
        }
        $wg->wait();
    }
}
