<?php

namespace App\Command;

use App\Dto\Articles;
use App\Dto\Products;
use App\Serializer\PropertiesConverter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TestSerializerCommand extends Command
{
    protected static $defaultName = 'test:serializer';

    protected function configure()
    {
        $this
            ->setDescription('Playground for serializer');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
        $nameConverter = new PropertiesConverter();
        $normalizer = new ObjectNormalizer(null, $nameConverter, null, $extractor);

        $serializer = new Serializer(
            [$normalizer, new ArrayDenormalizer()],
            [new JsonEncoder()]
        );

        $inventory = file_get_contents('./data/inventory.json');
        $products = file_get_contents('./data/products.json');

        print_r($serializer->deserialize($inventory, Articles::class, 'json'));
        print_r($serializer->deserialize($products, Products::class, 'json'));

        return Command::SUCCESS;
    }
}
