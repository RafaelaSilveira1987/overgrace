<?php

require_once __DIR__ . '/../../core/Response.php';
require_once __DIR__ . '/../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../services/integrations/IntegrationSettingsService.php';
require_once __DIR__ . '/../../services/integrations/SmtpMailer.php';
require_once __DIR__ . '/../../services/integrations/EvolutionApiClient.php';
require_once __DIR__ . '/../../helpers/logger.php';

class IntegrationsController
{
    private const EVOLUTION_EVENTS = [
        'QRCODE_UPDATED',
        'CONNECTION_UPDATE',
        'MESSAGES_UPSERT',
        'MESSAGES_UPDATE',
        'SEND_MESSAGE',
        'CONTACTS_UPSERT',
        'CHATS_UPSERT',
        'GROUPS_UPSERT',
        'GROUP_UPDATE',
        'GROUP_PARTICIPANTS_UPDATE',
        'CALL',
        'ERRORS',
    ];

    public function getSettings(): void
    {
        AuthMiddleware::handleAdmin();
        $smtp = IntegrationSettingsService::getGroup('smtp', false);
        $evolution = IntegrationSettingsService::getGroup('evolution', false);
        $events = json_decode($evolution['evolution_events'] ?? '[]', true);
        if (!is_array($events)) $events = [];
        $evolution['evolution_events'] = $events;

        Response::json([
            'success' => true,
            'smtp' => $smtp,
            'evolution' => $evolution,
            'available_events' => self::EVOLUTION_EVENTS,
        ]);
    }

    public function saveSmtp(): void
    {
        $admin = AuthMiddleware::handleAdmin();
        $data = $this->input();
        $allowed = [
            'smtp_enabled', 'smtp_host', 'smtp_port', 'smtp_encryption',
            'smtp_username', 'smtp_password', 'smtp_from_email',
            'smtp_from_name', 'smtp_admin_recipients'
        ];
        $clean = $this->only($data, $allowed);
        if (($clean['smtp_enabled'] ?? '0') === '1') {
            if (empty($clean['smtp_host']) || empty($clean['smtp_port']) || empty($clean['smtp_from_email'])) {
                Response::json(['success' => false, 'message' => 'Preencha host, porta e e-mail remetente.'], 422);
            }
        }
        IntegrationSettingsService::saveGroup('smtp', $clean, (int)($admin['id'] ?? 0));
        Response::json([
            'success' => true,
            'message' => 'Configuração SMTP salva.',
            'settings' => IntegrationSettingsService::getGroup('smtp', false),
        ]);
    }

    public function testSmtp(): void
    {
        AuthMiddleware::handleAdmin();

        try {
            $data = $this->input();
            $to = trim((string) ($data['email'] ?? ''));

            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                Response::json([
                    'success' => false,
                    'message' => 'Informe um e-mail válido para o teste.',
                ], 422);

                return;
            }

            $config = IntegrationSettingsService::getGroup(
                'smtp',
                true
            );

            if (
                empty($config['smtp_host']) ||
                empty($config['smtp_port']) ||
                empty($config['smtp_from_email'])
            ) {
                Response::json([
                    'success' => false,
                    'message' =>
                        'Salve a configuração SMTP antes de enviar o teste.',
                ], 422);

                return;
            }

            SmtpMailer::send(
                $config,
                $to,
                'Teste SMTP — OverGrace',
                '
                    <div style="font-family:Arial,sans-serif">
                        <h2>SMTP configurado com sucesso</h2>
                        <p>
                            Este e-mail confirma que a OverGrace
                            conseguiu autenticar e enviar pelo
                            servidor configurado.
                        </p>
                        <p>
                            <small>' .
                            htmlspecialchars(date('d/m/Y H:i:s')) .
                            '</small>
                        </p>
                    </div>
                '
            );

            Response::json([
                'success' => true,
                'message' => 'E-mail de teste enviado.',
            ]);
        } catch (Throwable $exception) {
            Logger::error(
                '[SMTP TEST] ' .
                $exception->getMessage()
            );

            $isLocal =
                ($_ENV['APP_ENV'] ?? 'local') !== 'production';

            Response::json([
                'success' => false,
                'message' => $isLocal
                    ? $exception->getMessage()
                    : 'Não foi possível enviar o e-mail de teste.',
            ], 500);
        }
    }

    public function saveEvolution(): void
    {
        $admin = AuthMiddleware::handleAdmin();
        $data = $this->input();
        $events = array_values(array_intersect(
            self::EVOLUTION_EVENTS,
            is_array($data['evolution_events'] ?? null) ? $data['evolution_events'] : []
        ));
        $clean = $this->only($data, [
            'evolution_enabled', 'evolution_base_url', 'evolution_api_key',
            'evolution_instance_name', 'evolution_webhook_url',
            'evolution_admin_numbers', 'evolution_notify_new_order',
            'evolution_notify_payment_paid', 'evolution_notify_payment_review',
            'evolution_notify_stock_low'
        ]);
        $clean['evolution_events'] = json_encode($events, JSON_UNESCAPED_UNICODE);
        IntegrationSettingsService::saveGroup('evolution', $clean, (int)($admin['id'] ?? 0));
        Response::json(['success' => true, 'message' => 'Configuração da Evolution salva.']);
    }


    public function getPaymentSettings(): void
    {
        AuthMiddleware::handleAdmin();
        require_once __DIR__ . '/../../services/payment/PaymentAvailabilityService.php';
        Response::json(['success' => true, 'data' => PaymentAvailabilityService::all()]);
    }

    public function getPublicPaymentSettings(): void
    {
        require_once __DIR__ . '/../../services/payment/PaymentAvailabilityService.php';
        $settings = PaymentAvailabilityService::all();
        Response::json([
            'success' => true,
            'data' => [
                'pix' => (string)($settings['payment_pix_enabled'] ?? '1') === '1',
                'boleto' => (string)($settings['payment_boleto_enabled'] ?? '0') === '1',
                'credit_card' => (string)($settings['payment_credit_card_enabled'] ?? '1') === '1',
                'debit_card' => false,
                'max_installments' => (int)($settings['payment_max_installments'] ?? 6),
                'min_installment' => (float)($settings['payment_min_installment'] ?? 50),
            ],
        ]);
    }

    public function savePaymentSettings(): void
    {
        $admin = AuthMiddleware::handleAdmin();
        $data = $this->input();
        $clean = $this->only($data, [
            'payment_pix_enabled',
            'payment_boleto_enabled',
            'payment_credit_card_enabled',
            'payment_debit_card_enabled',
            'payment_max_installments',
            'payment_min_installment',
        ]);

        foreach (['payment_pix_enabled','payment_boleto_enabled','payment_credit_card_enabled','payment_debit_card_enabled'] as $key) {
            $clean[$key] = in_array((string)($clean[$key] ?? '0'), ['1','true','on'], true) ? '1' : '0';
        }
        // Débito ainda não possui integração no checkout.
        $clean['payment_debit_card_enabled'] = '0';
        $clean['payment_max_installments'] = (string)max(1, min(12, (int)($clean['payment_max_installments'] ?? 6)));
        $minInstallment = (float)str_replace(',', '.', preg_replace('/[^0-9,\.]/', '', (string)($clean['payment_min_installment'] ?? '50')));
        $clean['payment_min_installment'] = number_format(max(0, $minInstallment), 2, '.', '');

        if ($clean['payment_pix_enabled'] !== '1' && $clean['payment_boleto_enabled'] !== '1' && $clean['payment_credit_card_enabled'] !== '1') {
            Response::json(['success' => false, 'message' => 'Mantenha pelo menos uma forma de pagamento ativa.'], 422);
            return;
        }

        IntegrationSettingsService::saveGroup('payments', $clean, (int)($admin['id'] ?? 0));
        Response::json(['success' => true, 'message' => 'Métodos de pagamento salvos.', 'data' => $clean]);
    }

    public function evolutionInstances(): void
    {
        AuthMiddleware::handleAdmin();
        Response::json(['success' => true, 'data' => $this->evolution()->instances()]);
    }

    public function evolutionCreate(): void
    {
        $admin = AuthMiddleware::handleAdmin();
        $data = $this->input();
        $name = trim((string)($data['instance_name'] ?? IntegrationSettingsService::get('evolution', 'evolution_instance_name', '')));
        if ($name === '') Response::json(['success' => false, 'message' => 'Informe o nome da instância.'], 422);
        $result = $this->evolution()->createInstance($name);
        IntegrationSettingsService::saveGroup('evolution', ['evolution_instance_name' => $name], (int)($admin['id'] ?? 0));
        Response::json(['success' => true, 'message' => 'Instância criada.', 'data' => $result]);
    }

    public function evolutionQr(): void
    {
        AuthMiddleware::handleAdmin();
        $name = $this->instanceName();
        Response::json(['success' => true, 'data' => $this->evolution()->connect($name)]);
    }

    public function evolutionState(): void
    {
        AuthMiddleware::handleAdmin();
        $name = $this->instanceName();
        Response::json(['success' => true, 'data' => $this->evolution()->state($name)]);
    }

    public function evolutionWebhook(): void
    {
        AuthMiddleware::handleAdmin();
        $data = $this->input();
        $settings = IntegrationSettingsService::getGroup('evolution', true);
        $name = trim((string)($data['instance_name'] ?? $settings['evolution_instance_name'] ?? ''));
        $url = trim((string)($data['webhook_url'] ?? $settings['evolution_webhook_url'] ?? ''));
        $events = $data['events'] ?? json_decode($settings['evolution_events'] ?? '[]', true) ?? [];
        if (!$name || !$url) Response::json(['success' => false, 'message' => 'Instância e URL de webhook são obrigatórias.'], 422);
        $events = array_values(array_intersect(self::EVOLUTION_EVENTS, is_array($events) ? $events : []));
        $result = $this->evolution()->setWebhook($name, $url, $events, true);
        Response::json(['success' => true, 'message' => 'Webhook e eventos configurados.', 'data' => $result]);
    }

    public function evolutionWebhookGet(): void
    {
        AuthMiddleware::handleAdmin();
        Response::json(['success' => true, 'data' => $this->evolution()->getWebhook($this->instanceName())]);
    }

    public function evolutionLogout(): void
    {
        AuthMiddleware::handleAdmin();
        Response::json(['success' => true, 'message' => 'Instância desconectada.', 'data' => $this->evolution()->logout($this->instanceName())]);
    }

    public function evolutionDelete(): void
    {
        AuthMiddleware::handleAdmin();
        Response::json(['success' => true, 'message' => 'Instância excluída.', 'data' => $this->evolution()->delete($this->instanceName())]);
    }

    public function evolutionTest(): void
    {
        AuthMiddleware::handleAdmin();
        $data = $this->input();
        $number = trim((string)($data['number'] ?? ''));
        if ($number === '') Response::json(['success' => false, 'message' => 'Informe o número para teste.'], 422);
        $result = $this->evolution()->sendText(
            $this->instanceName(),
            $number,
            "Teste de integração OverGrace\n\nA conexão com a Evolution API está funcionando."
        );
        Response::json(['success' => true, 'message' => 'Mensagem de teste enviada.', 'data' => $result]);
    }

    private function evolution(): EvolutionApiClient
    {
        $settings = IntegrationSettingsService::getGroup('evolution', true);
        return new EvolutionApiClient(
            (string)($settings['evolution_base_url'] ?? ''),
            (string)($settings['evolution_api_key'] ?? '')
        );
    }

    private function instanceName(): string
    {
        $data = $this->input();
        $name = trim((string)($data['instance_name'] ?? $_GET['instance_name'] ?? IntegrationSettingsService::get('evolution', 'evolution_instance_name', '')));
        if ($name === '') Response::json(['success' => false, 'message' => 'Instância não configurada.'], 422);
        return $name;
    }

    private function input(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw ?: '{}', true);
        return is_array($data) ? $data : [];
    }

    private function only(array $data, array $keys): array
    {
        return array_intersect_key($data, array_flip($keys));
    }
}
