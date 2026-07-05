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
     * Updates the environment file with Acapadev defaults if they do not exist.
     */
    protected function updateEnvironmentFile(): void
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            return;
        }

        $envContent = file_get_contents($envPath);
        $added = false;

        if (!str_contains($envContent, 'ACAPADEV_URL=')) {
            file_put_contents($envPath, PHP_EOL . 'ACAPADEV_URL=https://id.acapadev.com', FILE_APPEND);
            $added = true;
        }

        if (!str_contains($envContent, 'ACAPADEV_WEBHOOK_SECRET=')) {
            file_put_contents($envPath, PHP_EOL . 'ACAPADEV_WEBHOOK_SECRET=' . \Illuminate\Support\Str::random(32), FILE_APPEND);
            $added = true;
        }

        if ($added) {
            $this->info('As variáveis ACAPADEV_URL e ACAPADEV_WEBHOOK_SECRET foram adicionadas ao teu ficheiro .env automaticamente.');
        }
    }
    }
}
