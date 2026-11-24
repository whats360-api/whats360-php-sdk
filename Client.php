<?php
namespace Whats360;

/**
 * Whats360 API client. Only endpoints from the official reference.
 * Status: written from the docs; not executed against the live API.
 */
class Client
{
    private string $token;
    private ?string $instanceId;
    private string $baseUrl;

    public function __construct(string $token, ?string $instanceId = null, string $baseUrl = 'https://whats360.live/api/v1')
    {
        if ($token === '') {
            throw new \InvalidArgumentException('token is required');
        }
        $this->token = $token;
        $this->instanceId = $instanceId;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    private function inst(array $extra = []): array
    {
        if (!$this->instanceId) {
            throw new \InvalidArgumentException('instanceId is required for this call');
        }
        return array_merge(['instance_id' => $this->instanceId], $extra);
    }

    private function call(string $method, string $path, array $params = [], ?array $body = null): array
    {
        $params = array_filter($params, fn($v) => $v !== null);
        $url = $this->baseUrl . $path . '?' . http_build_query(array_merge(['token' => $this->token], $params));
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_CUSTOMREQUEST => $method]);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
        $raw = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($raw === false) {
            $err = curl_error($ch); // curl messages do not include the URL query
            curl_close($ch);
            throw new \RuntimeException('Whats360 request failed: ' . $err);
        }
        curl_close($ch);
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            $data = ['raw' => $raw];
        }
        if ($status >= 400 || (isset($data['success']) && $data['success'] === false)) {
            throw new \RuntimeException($data['error'] ?? ('HTTP ' . $status), $status);
        }
        return $data;
    }

    public function sendText(string $jid, string $msg): array { return $this->call('GET', '/send-text', $this->inst(['jid' => $jid, 'msg' => $msg])); }
    public function sendImage(string $jid, string $url, ?string $caption = null): array { return $this->call('GET', '/send-image', $this->inst(['jid' => $jid, 'imageurl' => $url, 'caption' => $caption])); }
    public function sendVideo(string $jid, string $url, ?string $caption = null): array { return $this->call('GET', '/send-video', $this->inst(['jid' => $jid, 'videourl' => $url, 'caption' => $caption])); }
    public function sendAudio(string $jid, string $url): array { return $this->call('GET', '/send-audio', $this->inst(['jid' => $jid, 'audiourl' => $url])); }
    public function sendDoc(string $jid, string $url, ?string $caption = null): array { return $this->call('GET', '/send-doc', $this->inst(['jid' => $jid, 'docurl' => $url, 'caption' => $caption])); }

    public function listDevices(): array { return $this->call('GET', '/instances'); }
    public function createDevice(string $id, ?string $name = null): array { return $this->call('GET', '/instances/create', ['id' => $id, 'name' => $name]); }
    public function connectDevice(): array { return $this->call('GET', '/instances/connect', $this->inst()); }
    public function deviceStatus(): array { return $this->call('GET', '/instances/status', $this->inst()); }
    public function deviceQr(): array { return $this->call('GET', '/instances/qr', $this->inst()); }

    public function createCampaign(array $body): array { return $this->call('POST', '/campaigns/create', $this->inst(), $body); }
    public function startCampaign(string $campaignId): array { return $this->call('POST', '/campaigns/start', $this->inst(['campaign_id' => $campaignId])); }
    public function campaignStatus(string $campaignId): array { return $this->call('GET', '/campaigns/status', $this->inst(['campaign_id' => $campaignId])); }
}
