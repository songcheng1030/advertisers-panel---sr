<?php

namespace App\Validator;

use App\Entity\Campaign;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class FixedRangeValidator
{
    /**
     * @param $campaign
     * @param $payload
     */
    public static function validate(Campaign $campaign, ExecutionContextInterface $context)
    {
        self::validateField($campaign->getVtrFrom(), 'vtrFrom', $campaign->getVtrTo(), 'vtrTo', $context);
        self::validateField($campaign->getCtrFrom(), 'ctrFrom', $campaign->getCtrTo(), 'ctrTo', $context);
        self::validateField($campaign->getViewabilityFrom(), 'viewabilityFrom', $campaign->getViewabilityTo(), 'viewabilityTo', $context);
    }

    private static function validateValue(ExecutionContextInterface $context, int $value, string $field)
    {
        if ($value < 1) {
            $context->buildViolation('common.field_more_or_equal_one_message')
                ->atPath($field)
                ->addViolation();
        } elseif ($value > 100) {
            $context->buildViolation('common.field_less_or_equal_one_hundred_message')
                ->atPath($field)
                ->addViolation();
        }
    }

    /**
     * @param $object
     */
    private static function validateField(
        $fromValue,
        string $fromPath,
        $toValue,
        string $toPath,
        ExecutionContextInterface $context
    ): void {
        if (null !== $fromValue && null === $toValue) {
            $context->buildViolation('common.field_must_be_completed')
                ->atPath($toPath)
                ->addViolation();
            self::validateValue($context, $fromValue, $fromPath);
        } elseif (null !== $toValue && null === $fromValue) {
            $context->buildViolation('common.field_must_be_completed')
                ->atPath($fromPath)
                ->addViolation();
            self::validateValue($context, $toValue, $toPath);
        } elseif (null !== $fromValue && null !== $toValue) {
            self::validateValue($context, $fromValue, $fromPath);
            self::validateValue($context, $toValue, $toPath);
        }
    }
}
