<?php declare(strict_types=1);


namespace Style34\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Style34\Entity\Profile\Profile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class InstallBaseCommand
 * @package Style34\Command
 */
class InstallBaseCommand extends Command {

    /** @var SymfonyStyle $io */
    protected $io;

    /** @var OutputInterface $output */
    protected $output;

    /** @var ObjectManager $em */
    protected $em;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    protected $passwordEncoder;

    /**
     * InstallBaseCommand constructor
     * @param ObjectManager $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(ObjectManager $em, UserPasswordEncoderInterface $passwordEncoder) {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct();
    }

    /**
     *
     */
    protected function configure() {
        $this->setName('app:install-base')
            ->setDescription('Install the basic database stuff (Roles, Permissions, Users...')
            ->setHelp('This will run some persists to DB and create basic stuff. !IMPORTANT! Run fixtures
            only AFTER this installation!');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;
        $this->io = $io = new SymfonyStyle($input, $output);

        try {
            $io->title('Starting the app installation');

            // Create database
            $io->section('Create database...');
            $this->createDatabase();

            // Run migrations
            $io->section('Running migrations...');
            $this->runMigrations();

            // Create roles
            $io->section('Creating roles...');
            $this->createRoles();

            // Create users
            $io->section('Creating users...');
            $this->registerUsers();

            $io->success('Installation complete!');
        } catch (\Exception $ex) {
            $io->error('Installation failed, maybe it was run twice?');
            $io->error($ex->getMessage());
        }

    }

    /**
     * @throws \Exception
     */
    protected function createDatabase() {
        $this->io->progressStart(1);

        $command = $this->getApplication()->find('doctrine:database:create');

        $arguments = new ArrayInput(array());
        $command->run($arguments, new NullOutput());

        $this->io->progressAdvance(1);
        $this->io->progressFinish();
    }


    /**
     * @throws \Exception
     */
    protected function runMigrations() {
        $this->io->progressStart(1);

        $command = $this->getApplication()->find('doctrine:migrations:migrate');

        $options = array(
            "command" => "doctrine:migrations:migrate",
            "--quiet" => true,
            "--no-interaction" => true
        );

        $arguments = new ArrayInput($options);
        $arguments->setInteractive(false);
        $command->run($arguments, new NullOutput());

        $this->io->progressAdvance(1);
        $this->io->progressFinish();
    }

    /**
     *
     */
    protected function createRoles(){
        $this->io->progressStart(1);
        $this->io->progressAdvance(1);
        $this->io->progressFinish();
    }

    /**
     *
     */
    protected function registerUsers() {

        /** @var array $users Username, mail, password */
        $users = array(
            array('admin', 'admin@style34.net', 'rootpass'),
            array('RandomGuy', 'randomguy@style34.net', 'randomguy'),
            array('Test', 'test@style34.net', 'test'),
            array('Tester', 'tester@style34.net', 'test'),
            array('Banned', 'banned@style34.net', 'test'),
            array('NotAmember', 'nam@style34.net', 'test'),
        );

        $this->io->progressStart(count($users));

        foreach ($users as $user) {
            $profile = new Profile();
            $profile->setUsername($user[0]);
            $profile->setEmail($user[1]);
            $profile->setCreatedAt(new \DateTime());
            $profile->setPassword($this->passwordEncoder->encodePassword($profile, $user[2]));

            $this->em->persist($profile);
            $this->em->flush();

            $this->io->progressAdvance(1);
        }

        $this->io->progressFinish();

    }
}