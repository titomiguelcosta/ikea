<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

trait SerializerTrait
{
    public function getSerializer(): SerializerInterface
    {
        $nameConverter = new PropertiesConverter();
        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
        $normalizer = new ObjectNormalizer(null, $nameConverter, null, $extractor);

        return new Serializer(
            [$normalizer, new ArrayDenormalizer()],
            [new JsonEncoder()]
        );
    }
}
