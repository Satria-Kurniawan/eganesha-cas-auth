<?php

namespace EGanesha\CasAuth;

use Illuminate\Support\Facades\Http;

class CasManager
{
    protected $config;
    protected $casUser;
    protected $casAttributes;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->casAttributes = [];
        $this->casUser = null;
    }

    public function getLoginUrl(string $serviceUrl): string
    {
        $baseUrl = rtrim($this->config['cas_server_url'], '/');
        $loginPath = $this->config['paths']['login'];

        return "{$baseUrl}/{$loginPath}?service=" . urlencode($serviceUrl);
    }

    /**
     * Redirect ke halaman login CAS sesuai service URL yang diberikan.
     *
     * @param string|null $serviceUrl
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToLogin(?string $serviceUrl = null)
    {
        // Jika user sudah login, tidak perlu redirect
        if ($this->isAuthenticated()) {
            return redirect($serviceUrl ?? url('/'));
        }

        $loginUrl = $this->getLoginUrl($serviceUrl ?? url()->current());
        return redirect()->away($loginUrl);
    }

    public function validateTicket(string $ticket, string $serviceUrl): bool
    {
        $baseUrl = rtrim($this->config['cas_server_url'], '/');
        $validatePath = $this->config['paths']['validate'];
        $validateUrl = "{$baseUrl}/{$validatePath}?ticket={$ticket}&service=" . urlencode($serviceUrl);

        try {
            $response = Http::timeout(10)->get($validateUrl);

            if ($response->failed()) {
                report("Gagal menghubungi server CAS di: " . $validateUrl);
                return false;
            }

            return $this->parseCasResponse($response->body());
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    protected function parseCasResponse(string $xmlBody): bool
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlBody, 'SimpleXMLElement', LIBXML_NOCDATA);

        if ($xml === false || !isset($xml->children('cas', true)->authenticationSuccess)) {
            return false;
        }

        $successNode = $xml->children('cas', true)->authenticationSuccess;
        $this->casUser = (string) $successNode->user;

        if (isset($successNode->attributes)) {
            foreach ($successNode->attributes->children('cas', true) as $key => $value) {
                $this->casAttributes[(string) $key] = (string) $value;
            }
        }

        return true;
    }

    public function storeCasUserInSession(): bool
    {
        if (!$this->casUser) {
            return false;
        }

        session([
            'cas_user' => $this->casUser,
            'cas_attributes' => $this->casAttributes,
        ]);

        return true;
    }

    public function isAuthenticated(): bool
    {
        return session()->has('cas_user');
    }

    public function getCurrentUser(): ?string
    {
        return session('cas_user');
    }

    public function getAttributes(): array
    {
        return session('cas_attributes', []);
    }

    public function logout(): void
    {
        session()->forget(['cas_user', 'cas_attributes']);
    }

    public function getLogoutUrl(?string $redirectUrl = null): string
    {
        $baseUrl = rtrim($this->config['cas_server_url'], '/');
        $logoutPath = $this->config['paths']['logout'] ?? 'logout';

        $url = "{$baseUrl}/{$logoutPath}";
        if ($redirectUrl) {
            $url .= '?service=' . urlencode($redirectUrl);
        }

        return $url;
    }

    public function logoutAndRedirect(?string $redirectUrl = null)
    {
        $this->logout();

        $casLogoutUrl = $this->getLogoutUrl($redirectUrl ?? url('/'));
        return redirect()->away($casLogoutUrl);
    }
}
