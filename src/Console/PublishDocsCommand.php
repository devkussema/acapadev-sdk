<?php

namespace Acapadev\Sdk\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishDocsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acapadev:docs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera e publica a documentação offline do Acapadev SDK para a raiz do teu projeto';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('A gerar a documentação do Acapadev SDK...');

        $sourcePath = __DIR__ . '/../../docs';
        $destinationPath = base_path('docs/acapadev');

        if (!File::exists($sourcePath)) {
            $this->error("A pasta de documentação original não foi encontrada em: {$sourcePath}");
            return;
        }

        if (!File::exists(base_path('docs'))) {
            File::makeDirectory(base_path('docs'));
        }

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath);
        }

        File::copyDirectory($sourcePath, $destinationPath);

        // Copy README.md as well
        $readmeSource = __DIR__ . '/../../README.md';
        if (File::exists($readmeSource)) {
            File::copy($readmeSource, $destinationPath . '/README.md');
        }

        $this->info("Documentação publicada com sucesso em: <comment>docs/acapadev/</comment> 📚");
        $this->line("Podes agora ler os ficheiros Markdown para entenderes toda a arquitetura e integração!");
    }
}
