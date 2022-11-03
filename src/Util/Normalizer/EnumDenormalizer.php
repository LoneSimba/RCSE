<?php

namespace App\Util\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class EnumDenormalizer implements DenormalizerInterface
{

    public function denormalize(mixed $data, $type, $format = null, $context = [])
    {
        return $type::tryFrom($data);
    }

    public function supportsDenormalization(mixed $data, $type, $format = null)
    {
        return is_subclass_of($type, \BackedEnum::class);
    }
}