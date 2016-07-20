<?php

namespace Tagmeo\Installer\Console;

use ZipArchive;
use RuntimeException;
use GuzzleHttp\Client;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Create a new Tagmeo application.')
            ->addArgument('name', InputArgument::OPTIONAL)
            ->addOption('dev', null, InputOption::VALUE_NONE, 'Installs the latest "development" release');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!class_exists('ZipArchive')) {
            throw new RuntimeException('The Zip PHP extension is not installed. Please install it and try again.');
        }

        $this->verifyApplicationDoesntExist(
            $directory = ($input->getArgument('name')) ? getcwd().'/'.$input->getArgument('name') : getcwd(),
            $output
        );

        $output->writeln('<info>Building application...</info>');

        $version = $this->getVersion($input);

        $this->download($zipFile = $this->makeFilename(), $version)
             ->extract($zipFile, $directory)
             ->cleanUp($zipFile);

        $composer = $this->findComposer();

        $commands = [
            $composer.' install --no-scripts',
            $composer.' run-script post-root-package-install',
            $composer.' run-script post-create-project-cmd'
        ];

        $process = new Process(implode(' && ', $commands), $directory, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });

        $output->writeln('<comment>Application ready!</comment>');
    }

    protected function verifyApplicationDoesntExist($directory, OutputInterface $output)
    {
        if ((is_dir($directory) || is_file($directory)) && $directory != getcwd()) {
            throw new RuntimeException('Application already exists!');
        }
    }

    protected function makeFilename()
    {
        return getcwd().'/tagmeo'.md5(time().uniqid()).'.zip';
    }

    protected function download($zipFile, $version = 'master')
    {
        switch ($version) {
            case 'master':
                $filename = 'latest.zip';
                break;
            case 'develop':
                $filename = 'latest-develop.zip';
                break;
        }

        $response = (new Client)->get('https://tagmeo.com/'.$filename);

        file_put_contents($zipFile, $response->getBody());

        return $this;
    }

    protected function extract($zipFile, $directory)
    {
        $archive = new ZipArchive;

        $archive->open($zipFile);
        $archive->extractTo($directory);
        $archive->close();

        return $this;
    }

    protected function cleanUp($zipFile)
    {
        @chmod($zipFile, 0777);
        @unlink($zipFile);

        return $this;
    }

    protected function getVersion($input)
    {
        if ($input->getOption('dev')) {
            return 'develop';
        }

        return 'master';
    }

    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" composer.phar';
        }

        return 'composer';
    }
}
