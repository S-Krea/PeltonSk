<?php

namespace App\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PlainIdentifierDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function __construct(protected IriConverterInterface $iriConverter)
    {
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $mappings = $context['relation_mapping'];

        foreach ($mappings as $property => $class) {
            if (is_array($data[$property])) {
                $data[$property] = array_map(
                    fn ($value) => $this->iriConverter->getIriFromResource(resource: $class, context: ['uri_variables' => ['id' => $value]]),
                    $data[$property]
                );
            } else {
                $value = $data[$property];
                $data[$property] = $this->iriConverter->getIriFromResource(resource: $class, context: ['uri_variables' => ['id' => $value]]);
            }
        }

        return $this->denormalizer->denormalize($data, $type, $format, $context + [__CLASS__ => true]);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        $formatIsSupported = \in_array($format, ['json', 'jsonld'], true);
        $calledFirstTime = !isset($context[__CLASS__]);

        if (isset($context['relation_mapping']) && $calledFirstTime) {
            return $formatIsSupported;
        }

        return false;
    }
}
