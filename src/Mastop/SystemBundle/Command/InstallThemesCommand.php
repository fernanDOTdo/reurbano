<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mastop\SystemBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Dumps assets to the filesystem.
 *
 * @author Kris Wallsmith <kris@symfony.com>
 */
class InstallThemesCommand extends Command {

    private $mt;

    protected function configure() {
        $this
                ->setDefinition(array(
                    new InputArgument('target', InputArgument::REQUIRED, 'A pasta os arquivos dos temas serão copiados (geralmente "web")'),
                ))
                ->addOption('symlink', null, InputOption::VALUE_NONE, 'Criar links ao invés de copiar')
                ->setHelp(<<<EOT
O comando <info>mastop:installthemes</info> instala os arquivos estáticos dos temas em uma determinada
pasta (ex.: a pasta web).

<info>./app/console mastop:installthemes web [--symlink]</info>

Uma pasta "themes" será criada dentro da pasta alvo, e o sistema varrerá
todos os temas, pegando os arquivos estáticos e copiando para a pasta criada.

Para criar links ao invés de copiar os arquivos, use a opção <info>--symlink</info>.

EOT
                )
                ->setName('mastop:installthemes')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) {
        parent::initialize($input, $output);

        $this->mt = $this->container->get('mastop.themes');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        if (!is_dir($input->getArgument('target'))) {
            throw new \InvalidArgumentException(sprintf('O diretório alvo "%s" não existe.', $input->getArgument('target')));
        }

        if (!function_exists('symlink') && $input->getOption('symlink')) {
            throw new \InvalidArgumentException('A função symlink() não está disponível em seu sistema. Você deve instalar os temas sem a opção --symlink.');
        }

        $filesystem = $this->container->get('filesystem');
        $origem = $this->mt->getDir();
        $temas = $this->mt->getAllowedThemes();
        if(empty ($origem) || empty($temas)){
            throw new \RuntimeException('O sistema de temas não está configurado.');
        }

        // Cria o diretório de temas
        $filesystem->mkdir($input->getArgument('target') . '/themes/', 0777);
        foreach ($temas as $tema) {
            $originDir = $origem . '/'.$tema.'/Frontend';
            $finder = new Finder();
            $finder->in($originDir);
            $finder->files()->notName('*.twig');
            if (is_dir($originDir)) {
                $targetDir = $input->getArgument('target') . '/themes/' . $tema;

                $output->writeln(sprintf('Instalando arquivos do tema <comment>%s</comment> em <comment>%s</comment>', $tema, $targetDir));

                $filesystem->remove($targetDir);

                if ($input->getOption('symlink')) {
                    $filesystem->symlink($originDir, $targetDir);
                } else {
                    $filesystem->mkdir($targetDir, 0777);
                    $filesystem->mirror($originDir, $targetDir, $finder);
                }
            }
            $originDirAdmin = $origem . '/'.$tema.'/Backend';
            $finder = new Finder();
            $finder->in($originDirAdmin);
            $finder->files()->notName('*.twig');
            if (is_dir($originDirAdmin)) {
                $targetDir = $input->getArgument('target') . '/themes/' . $tema . '/admin';

                $output->writeln(sprintf('Instalando arquivos da administração do tema <comment>%s</comment> em <comment>%s</comment>', $tema, $targetDir));

                $filesystem->remove($targetDir);

                if ($input->getOption('symlink')) {
                    $filesystem->symlink($originDirAdmin, $targetDir);
                } else {
                    $filesystem->mkdir($targetDir, 0777);
                    $filesystem->mirror($originDirAdmin, $targetDir, $finder);
                }
            }
        }
    }

}
