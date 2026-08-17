<?php

class EvolutionApiClient
{
    public function __construct(private string $baseUrl, private string $apiKey)
    {
        $this->baseUrl = rtrim(trim($baseUrl), '/');
        if ($this->baseUrl === '' || $this->apiKey === '') {
            throw new InvalidArgumentException('URL e API Key da Evolution são obrigatórias.');
        }
    }

    public function createInstance(string $name): array
    {
        return $this->request('POST', '/instance/create', [
            'instanceName' => trim($name),
            'qrcode' => true,
            'integration' => 'WHATSAPP-BAILEYS',
        ]);
    }

    public function connect(string $name): array
    {
        return $this->request('GET', '/instance/connect/' . rawurlencode($name));
    }

    public function state(string $name): array
    {
        return $this->request('GET', '/instance/connectionState/' . rawurlencode($name));
    }

    public function instances(): array
    {
        return $this->request('GET', '/instance/fetchInstances');
    }

    public function setWebhook(string $name, string $url, array $events, bool $enabled = true): array
    {
        return $this->request('POST', '/webhook/set/' . rawurlencode($name), [
            'webhook' => [
                'enabled' => $enabled,
                'url' => trim($url),
                'webhookByEvents' => false,
                'webhookBase64' => false,
                'events' => array_values(array_unique($events)),
            ],
        ]);
    }

    public function getWebhook(string $name): array
    {
        return $this->request('GET', '/webhook/find/' . rawurlencode($name));
    }

    public function logout(string $name): array
    {
        return $this->request('DELETE', '/instance/logout/' . rawurlencode($name));
    }

    public function delete(string $name): array
    {
        return $this->request('DELETE', '/instance/delete/' . rawurlencode($name));
    }

    public function sendText(string $name, string $number, string $text): array
    {
        return $this->request('POST', '/message/sendText/' . rawurlencode($name), [
            'number' => preg_replace('/\D+/', '', $number),
            'text' => $text,
        ]);
    }

    private function request(string $method, string $path, ?array $body = null): array
    {
        $curl = curl_init($this->baseUrl . $path);
        $headers = ['apikey: ' . $this->apiKey, 'Accept: application/json'];
        if ($body !== null) $headers[] = 'Content-Type: application/json';

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_POSTFIELDS => $body !== null ? json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
        ]);

        $raw = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($raw === false) throw new RuntimeException('Falha ao chamar Evolution API: ' . $error);
        $data = json_decode($raw, true);
        if ($status >= 400) {
            $message = $data['response']['message'][0] ?? $data['error']['message'] ?? $data['message'] ?? $raw;
            throw new RuntimeException('Evolution API HTTP ' . $status . ': ' . (is_array($message) ? json_encode($message) : $message));
        }
        return is_array($data) ? $data : ['raw' => $raw];
    }
}
