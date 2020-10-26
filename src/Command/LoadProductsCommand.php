<?php

namespace App\Command;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Dto\Products;
use App\Message\ProductsMessage;
use App\Serializer\SerializerTrait;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LoadProductsCommand extends Command
{
    use SerializerTrait;

    protected static $defaultName = 'products:load';

    private $bus;
    private $serializer;
    private $validator;

    public function __construct(MessageBusInterface $bus, ValidatorInterface $validator)
    {
        parent::__construct();

        $this->bus = $bus;
        $this->serializer = $this->getSerializer();
        $this->validator = $validator;
    }

    protected function configure()
    {
        $this
            ->setDescription('Load products from a file')
            ->addArgument('file', InputArgument::REQUIRED, 'path to json file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!file_exists($file) || !is_readable($file)) {
            throw new RuntimeException(sprintf('file %s is not valid', $file));
        }

        $dto = $this->serializer->deserialize(
            file_get_contents($file),
            Products::class,
            'json'
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $this->bus->dispatch(new ProductsMessage($dto));

        return Command::SUCCESS;
    }
}
