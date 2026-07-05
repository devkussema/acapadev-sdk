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

        $this->info('Acapadev SDK instalado com sucesso!');
        
        $this->warn('Por favor, adicione as seguintes variáveis ao seu ficheiro .env:');
        $this->line('ACAPADEV_URL=https://id.acapadev.com');
        $this->line('ACAPADEV_WEBHOOK_SECRET=o_seu_webhook_secret_aqui');
    }
}
