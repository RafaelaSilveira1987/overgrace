<?php

require_once __DIR__ . '/../integrations/IntegrationSettingsService.php';

class PaymentAvailabilityService
{
    private const DEFAULTS = [
        'payment_pix_enabled' => '1',
        'payment_boleto_enabled' => '0',
        'payment_credit_card_enabled' => '1',
        'payment_debit_card_enabled' => '0',
        'payment_max_installments' => '6',
        'payment_min_installment' => '50.00',
    ];

    public static function all(): array
    {
        return array_merge(
            self::DEFAULTS,
            IntegrationSettingsService::getGroup('payments', false)
        );
    }

    public static function normalizedMethod(string $method): string
    {
        return match (strtolower(trim($method))) {
            'card', 'cartao', 'credit_card' => 'credit_card',
            'boleto', 'ticket' => 'boleto',
            'pix' => 'pix',
            'debit', 'debit_card', 'cartao_debito' => 'debit_card',
            default => strtolower(trim($method)),
        };
    }

    public static function isEnabled(string $method): bool
    {
        $method = self::normalizedMethod($method);
        $settings = self::all();
        $key = 'payment_' . $method . '_enabled';
        return (string)($settings[$key] ?? '0') === '1';
    }

    public static function assertEnabled(string $method): void
    {
        $normalized = self::normalizedMethod($method);
        if (!self::isEnabled($normalized)) {
            $label = match ($normalized) {
                'pix' => 'PIX',
                'boleto' => 'Boleto bancário',
                'credit_card' => 'Cartão de crédito',
                'debit_card' => 'Cartão de débito',
                default => 'Forma de pagamento',
            };
            throw new InvalidArgumentException($label . ' está desativado nas configurações da loja.');
        }
    }
}
