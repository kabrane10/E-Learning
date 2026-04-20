<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display settings page.
     */
    public function index()
    {
        $settings = $this->getAllSettings();
        
        return view('admin.settings.index', compact('settings'));
    }
    
    /**
     * Update general settings.
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_keywords' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|max:2048',
            'site_favicon' => 'nullable|image|max:1024',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);
        
        // Gestion du logo
        if ($request->hasFile('site_logo')) {
            $logoPath = $request->file('site_logo')->store('settings', 'public');
            $this->setSetting('site_logo', $logoPath);
        }
        
        // Gestion du favicon
        if ($request->hasFile('site_favicon')) {
            $faviconPath = $request->file('site_favicon')->store('settings', 'public');
            $this->setSetting('site_favicon', $faviconPath);
        }
        
        // Sauvegarde des autres paramètres
        foreach (['site_name', 'site_description', 'site_keywords', 'contact_email', 'contact_phone', 'address'] as $key) {
            if (isset($validated[$key])) {
                $this->setSetting($key, $validated[$key]);
            }
        }
        
        $this->clearSettingsCache();
        
        return back()->with('success', 'Paramètres généraux mis à jour avec succès.');
    }
    
    /**
     * Update email settings.
     */
    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'mail_driver' => 'required|string|in:smtp,ses,mailgun,postmark',
            'mail_host' => 'nullable|string|required_if:mail_driver,smtp',
            'mail_port' => 'nullable|integer|required_if:mail_driver,smtp',
            'mail_username' => 'nullable|string|required_if:mail_driver,smtp',
            'mail_password' => 'nullable|string|required_if:mail_driver,smtp',
            'mail_encryption' => 'nullable|string|in:tls,ssl,null',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);
        
        foreach ($validated as $key => $value) {
            $this->setSetting($key, $value);
        }
        
        $this->clearSettingsCache();
        
        // Mise à jour du fichier .env
        $this->updateEnvFile([
            'MAIL_MAILER' => $validated['mail_driver'],
            'MAIL_HOST' => $validated['mail_host'] ?? '',
            'MAIL_PORT' => $validated['mail_port'] ?? '',
            'MAIL_USERNAME' => $validated['mail_username'] ?? '',
            'MAIL_PASSWORD' => $validated['mail_password'] ?? '',
            'MAIL_ENCRYPTION' => $validated['mail_encryption'] ?? '',
            'MAIL_FROM_ADDRESS' => $validated['mail_from_address'],
            'MAIL_FROM_NAME' => '"' . $validated['mail_from_name'] . '"',
        ]);
        
        return back()->with('success', 'Paramètres email mis à jour avec succès.');
    }
    
    /**
     * Update payment settings.
     */
    public function updatePayment(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:5',
            'stripe_enabled' => 'boolean',
            'stripe_key' => 'nullable|string|required_if:stripe_enabled,1',
            'stripe_secret' => 'nullable|string|required_if:stripe_enabled,1',
            'stripe_webhook_secret' => 'nullable|string',
            'paypal_enabled' => 'boolean',
            'paypal_client_id' => 'nullable|string|required_if:paypal_enabled,1',
            'paypal_secret' => 'nullable|string|required_if:paypal_enabled,1',
            'paypal_mode' => 'nullable|string|in:sandbox,live',
        ]);
        
        foreach ($validated as $key => $value) {
            $this->setSetting($key, $value);
        }
        
        $this->clearSettingsCache();
        
        // Mise à jour du fichier .env pour Stripe
        if ($request->boolean('stripe_enabled')) {
            $this->updateEnvFile([
                'STRIPE_KEY' => $validated['stripe_key'],
                'STRIPE_SECRET' => $validated['stripe_secret'],
                'STRIPE_WEBHOOK_SECRET' => $validated['stripe_webhook_secret'] ?? '',
            ]);
        }
        
        // Mise à jour du fichier .env pour PayPal
        if ($request->boolean('paypal_enabled')) {
            $this->updateEnvFile([
                'PAYPAL_CLIENT_ID' => $validated['paypal_client_id'],
                'PAYPAL_SECRET' => $validated['paypal_secret'],
                'PAYPAL_MODE' => $validated['paypal_mode'],
            ]);
        }
        
        return back()->with('success', 'Paramètres de paiement mis à jour avec succès.');
    }
    
    /**
     * Update security settings.
     */
    public function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'recaptcha_enabled' => 'boolean',
            'recaptcha_site_key' => 'nullable|string|required_if:recaptcha_enabled,1',
            'recaptcha_secret_key' => 'nullable|string|required_if:recaptcha_enabled,1',
            'two_factor_enabled' => 'boolean',
            'session_lifetime' => 'required|integer|min:1|max:1440',
            'max_login_attempts' => 'required|integer|min:1|max:10',
            'password_expiry_days' => 'nullable|integer|min:0',
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:500',
        ]);
        
        foreach ($validated as $key => $value) {
            $this->setSetting($key, $value);
        }
        
        $this->clearSettingsCache();
        
        // Mise à jour du fichier .env pour reCAPTCHA
        if ($request->boolean('recaptcha_enabled')) {
            $this->updateEnvFile([
                'RECAPTCHA_SITE_KEY' => $validated['recaptcha_site_key'],
                'RECAPTCHA_SECRET_KEY' => $validated['recaptcha_secret_key'],
            ]);
        }
        
        // Gestion du mode maintenance
        if ($request->boolean('maintenance_mode')) {
            $this->enableMaintenanceMode($validated['maintenance_message'] ?? null);
        } else {
            $this->disableMaintenanceMode();
        }
        
        return back()->with('success', 'Paramètres de sécurité mis à jour avec succès.');
    }
    
    /**
     * Update social settings.
     */
    public function updateSocial(Request $request)
    {
        $validated = $request->validate([
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'github_url' => 'nullable|url',
        ]);
        
        foreach ($validated as $key => $value) {
            $this->setSetting($key, $value);
        }
        
        $this->clearSettingsCache();
        
        return back()->with('success', 'Réseaux sociaux mis à jour avec succès.');
    }
    
    /**
     * Update SEO settings.
     */
    public function updateSeo(Request $request)
    {
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'og_image' => 'nullable|image|max:2048',
            'google_analytics_id' => 'nullable|string',
            'facebook_pixel_id' => 'nullable|string',
            'robots_txt' => 'nullable|string',
        ]);
        
        if ($request->hasFile('og_image')) {
            $ogImagePath = $request->file('og_image')->store('settings', 'public');
            $this->setSetting('og_image', $ogImagePath);
        }
        
        foreach (['meta_title', 'meta_description', 'meta_keywords', 'google_analytics_id', 'facebook_pixel_id', 'robots_txt'] as $key) {
            if (isset($validated[$key])) {
                $this->setSetting($key, $validated[$key]);
            }
        }
        
        $this->clearSettingsCache();
        
        return back()->with('success', 'Paramètres SEO mis à jour avec succès.');
    }
    
    /**
     * Test email configuration.
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);
        
        try {
            \Illuminate\Support\Facades\Mail::raw('Ceci est un email de test depuis votre plateforme E-Learn.', function ($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('Test Email - E-Learn');
            });
            
            return back()->with('success', 'Email de test envoyé avec succès à ' . $request->test_email);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }
    
    /**
     * Clear application cache.
     */
    public function clearCache()
    {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        
        return back()->with('success', 'Cache vidé avec succès.');
    }
    
    /**
     * Get all settings as key-value array.
     */
    private function getAllSettings(): array
    {
        return Cache::remember('app_settings', 3600, function () {
            return Setting::pluck('value', 'key')->toArray();
        });
    }
    
    /**
     * Set a setting value.
     */
    private function setSetting(string $key, $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
    
    /**
     * Clear settings cache.
     */
    private function clearSettingsCache(): void
    {
        Cache::forget('app_settings');
    }
    
    /**
     * Update .env file.
     */
    private function updateEnvFile(array $values): void
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            return;
        }
        
        $content = file_get_contents($envFile);
        
        foreach ($values as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$value}", $content);
            } else {
                $content .= PHP_EOL . "{$key}={$value}";
            }
        }
        
        file_put_contents($envFile, $content);
    }
    
    /**
     * Enable maintenance mode.
     */
    private function enableMaintenanceMode(?string $message = null): void
    {
        $params = [];
        if ($message) {
            $params['secret'] = \Illuminate\Support\Str::random(32);
            // Stocker le message dans un fichier
            file_put_contents(storage_path('framework/maintenance.php'), '<?php return ' . var_export(['message' => $message], true) . ';');
        }
        
        \Illuminate\Support\Facades\Artisan::call('down', $params);
    }
    
    /**
     * Disable maintenance mode.
     */
    private function disableMaintenanceMode(): void
    {
        \Illuminate\Support\Facades\Artisan::call('up');
    }
}