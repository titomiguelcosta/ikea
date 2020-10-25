<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Dto\DtoInterface;
use App\Serializer\PropertiesConverter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ControllerArgumentsDtoSubscriber implements EventSubscriberInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(ValidatorInterface $validator)
    {
        $nameConverter = new PropertiesConverter();
        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
        $normalizer = new ObjectNormalizer(null, $nameConverter, null, $extractor);

        $this->serializer = new Serializer(
            [$normalizer, new ArrayDenormalizer()],
            [new JsonEncoder()]
        );

        $this->validator = $validator;
    }

    /**
     * if any of the arguments has Dto in the namespace
     * and there is a body in the request, we map and validate.
     */
    public function onKernelControllerArguments(ControllerArgumentsEvent $event)
    {
        $arguments = $event->getArguments();
        $request = $event->getRequest();

        foreach ($arguments as $pos => $argument) {
            if (is_object($argument) && $argument instanceof DtoInterface) {
                $body = $request->getContent();

                if (0 === mb_strlen(trim($body))) {
                    $body = '{}';
                }

                $dto = $this->serializer->deserialize(
                    $body,
                    get_class($argument),
                    'json'
                );

                $errors = $this->validator->validate($dto);
                if (count($errors) > 0) {
                    throw new ValidationException($errors);
                }

                $arguments[$pos] = $dto;
                $event->setArguments($arguments);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller_arguments' => 'onKernelControllerArguments',
        ];
    }
}
