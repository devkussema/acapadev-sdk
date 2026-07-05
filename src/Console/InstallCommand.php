<?php

namespace Acapadev\Sdk\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acapadev:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instala o Acapadev SDK e publica os ficheiros de configuração';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Instalando Acapadev SDK...');

        $this->info('A publicar ficheiro de configuração...');
        $this->callSilent('vendor:publish', ['--tag' => 'acapadev-config']);

        $this->updateEnvironmentFile();

        $this->info('Acapadev SDK instalado e configurado com sucesso! 🚀');
    }

    /**
     * Updates the environment file interactively.
     */
    protected function updateEnvironmentFile(): void
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->warn('Ficheiro .env não encontrado. Por favor configure as variáveis manualmente.');
            return;
        }

        $envContent = file_get_contents($envPath);
        $added = false;

        if (!str_contains($envContent, 'ACAPADEV_URL=')) {
            file_put_contents($envPath, PHP_EOL . 'ACAPADEV_URL=https://id.acapadev.com', FILE_APPEND);
            $added = true;
        }

        $this->info("\n--- Configuração de Segurança ---");
        
        if (!str_contains($envContent, 'ACAPADEV_WEBHOOK_SECRET=')) {
            $webhookSecret = $this->secret('Qual é o Webhook Secret gerado no Acapadev ID? (Deixe em branco para gerar um aleatório)');
            
            if (empty($webhookSecret)) {
                $webhookSecret = \Illuminate\Support\Str::random(32);
                $this->line("Gerado um secret aleatório: <comment>{$webhookSecret}</comment>");
            }
            
            file_put_contents($envPath, PHP_EOL . 'ACAPADEV_WEBHOOK_SECRET=' . $webhookSecret, FILE_APPEND);
            $added = true;
        } else {
            $this->line('ACAPADEV_WEBHOOK_SECRET já está configurado no .env.');
        }

        if (!str_contains($envContent, 'ACAPADEV_CLIENT_ID=')) {
            $clientId = $this->ask('Qual é o Client ID desta aplicação satélite? (Deixe em branco se ainda não tiver)');
            if (!empty($clientId)) {
                file_put_contents($envPath, PHP_EOL . 'ACAPADEV_CLIENT_ID=' . $clientId, FILE_APPEND);
                $added = true;
            }
        } else {
            $this->line('ACAPADEV_CLIENT_ID já está configurado no .env.');
        }

        if (!str_contains($envContent, 'ACAPADEV_CLIENT_SECRET=')) {
            $clientSecret = $this->secret('Qual é o Client Secret desta aplicação? (Deixe em branco se ainda não tiver)');
            if (!empty($clientSecret)) {
                file_put_contents($envPath, PHP_EOL . 'ACAPADEV_CLIENT_SECRET=' . $clientSecret, FILE_APPEND);
                $added = true;
            }
        } else {
            $this->line('ACAPADEV_CLIENT_SECRET já está configurado no .env.');
        }

        if ($added) {
            $this->info('Variáveis guardadas no ficheiro .env com sucesso!');
        }
    }
}
