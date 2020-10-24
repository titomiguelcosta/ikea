<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class PropertiesConverter implements NameConverterInterface
{
    /**
     * from object to string
     */
    public function normalize(string $propertyName)
    {
        return $propertyName;
    }

    /**
     * from string to object
     */
    public function denormalize(string $propertyName)
    {
        switch ($propertyName) {
            case 'inventory':
                return 'articles';
            case 'art_id':
                return 'code';
            case 'contain_articles':
                return 'components';
            case 'amount_of':
                return 'amount';
            default:
                return $propertyName;
        }
    }
}
