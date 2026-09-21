<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Infrastructure\DataTransformer;

/**
 * Marker for VOs meant for `PATCH` (partial update): every field defaults to `null`,
 * and `null` means "leave unchanged," not "clear this field." `SerializerTrait`
 * skips null-valued properties for these payloads so unset fields are omitted from
 * the JSON body entirely, instead of being sent as empty/zero values as `PUT`'s
 * full-replacement VOs do.
 */
interface PartialUpdatePayload
{
}
