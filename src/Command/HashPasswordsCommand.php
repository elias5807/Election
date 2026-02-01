<?php

namespace App\Command;

use App\Entity\Respo; // On importe ton entité spécifique
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:hash-list', description: 'Hashe une liste de mots de passe pour les Respos')]
class HashPasswordsCommand extends Command
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        parent::__construct();
        $this->hasher = $hasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Ta liste de mots de passe à convertir
        $passwords = ['agorae123',
'campusud123',
'campusiut123',
'campusstaps123',
'campuscitadelle123',
'campusscience123',
'campusart123',
'campusapradis123',
'campusifmk123',
'campuscathedrale123',
'deloc123'];
        
        $io->title('Génération des Hashs pour l\'entité Respo');

        // On instancie Respo. Symfony va maintenant trouver 
        // la config "auto" dans security.yaml car Respo implémente les interfaces requises.
        $respo = new Respo();

        foreach ($passwords as $pwd) {
            $hashed = $this->hasher->hashPassword($respo, $pwd);
            $io->writeln(sprintf('<info>%s</info> : %s', $pwd, $hashed));
        }

        $io->newLine();
        $io->success('Terminé ! Tu peux copier ces hashs dans ta base de données.');
        
        return Command::SUCCESS;
    }
}