<?php

namespace App\Services;

use App\Models\OrderItem;
use Exception;

class OrderStatusService
{
    public const STATE_PENDING          = 'pending';
    public const STATE_CONFIRMED        = 'confirmed';
    public const STATE_PACKED           = 'packed';
    public const STATE_SHIPPED          = 'shipped';
    public const STATE_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATE_DELIVERED        = 'delivered';
    public const STATE_CANCELLED        = 'cancelled';

    /**
     * Map of state to its allowed NEXT states.
     */
    protected const ALLOWED_TRANSITIONS = [
        self::STATE_PENDING          => [self::STATE_CONFIRMED, self::STATE_CANCELLED],
        self::STATE_CONFIRMED        => [self::STATE_PACKED, self::STATE_CANCELLED],
        self::STATE_PACKED           => [self::STATE_SHIPPED, self::STATE_CANCELLED],
        self::STATE_SHIPPED          => [self::STATE_OUT_FOR_DELIVERY, self::STATE_DELIVERED], // Sometimes straight to delivered
        self::STATE_OUT_FOR_DELIVERY => [self::STATE_DELIVERED],
        self::STATE_DELIVERED        => [], // Terminal state
        self::STATE_CANCELLED        => [], // Terminal state
    ];

    /**
     * Determine if a transition is valid.
     */
    public function isValidTransition(string $currentStatus, string $newStatus): bool
    {
        if ($currentStatus === $newStatus) {
            return false;
        }

        $allowed = self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];
        return in_array($newStatus, $allowed, true);
    }

    /**
     * Assert a transition is valid, throws Exception if not.
     */
    public function assertValidTransition(OrderItem $item, string $newStatus): void
    {
        if (!$this->isValidTransition($item->status, $newStatus)) {
            throw new Exception("Invalid order state transition from '{$item->status}' to '{$newStatus}'.");
        }
    }
}
