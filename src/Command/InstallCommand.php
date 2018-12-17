<?php declare(strict_types=1);


namespace Style34\Command;

use Doctrine\Common\Persistence\ObjectManager;
use Style34\Entity\Address\State;
use Style34\Entity\Profile\Profile;
use Style34\Entity\Profile\Role;
use Style34\Entity\Token\TokenType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class InstallCommand.
 * Install only app-required data, not testing fixtures! For dev/test data use fixtures in DataFixtures.
 * @package Style34\Command
 */
class InstallCommand extends Command
{

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
    public function __construct(ObjectManager $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setName('app:install')
            ->setDescription('Install the basic database stuff (Roles, Permissions, Users...')
            ->setHelp('This will run some persists to DB and create basic stuff. !IMPORTANT! Run fixtures
            only AFTER this installation!')
            ->addOption(
                'drop',
                'd',
                InputOption::VALUE_NONE,
                'Reinstall option, this will drop schema instead of creating database. You will lost any DB data.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->io = $io = new SymfonyStyle($input, $output);

        try {
            $io->title('Starting the app installation');

            // Prepare database
            if ($input->getOption('drop')) {
                $io->section('Clearing database...');
                $this->dropSchema();
            } else {
                $io->section('Creating database...');
                $this->createDatabase();
            }

            // Run migrations
            $io->section('Running migrations...');
            $this->runMigrations();

            // Create roles
            $io->section('Creating roles...');
            $this->createRoles();

            // Create users
            $io->section('Creating users...');
            $this->registerUsers();

            // Create token types
            $io->section('Creating token types...');
            $this->createTokenTypes();

            $io->success('Installation complete!');
        } catch (\Exception $ex) {
            $io->newLine();
            $io->newLine();
            $io->error($ex->getMessage());
            $io->text('Maybe you are trying to do reinstall? Then use:');
            $io->newLine();
            $io->text('    <info>app:install --drop</info> to clear database (you will loose all data)');
        }

    }

    /**
     * @throws \Exception
     */
    protected function dropSchema()
    {
        $this->io->progressStart(1);

        $command = $this->getApplication()->find('doctrine:schema:drop');

        $arguments = new ArrayInput(array(
            '--full-database' => true,
            '--force' => true)
        );
        $command->run($arguments, new NullOutput());

        $this->io->progressAdvance(1);
        $this->io->progressFinish();
    }

    /**
     * @throws \Exception
     */
    protected function createDatabase()
    {
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
    protected function runMigrations()
    {
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
    protected function createRoles()
    {
        $roles = array(
            [Role::ADMIN, '#CB2910'],
            [Role::MODERATOR, '#00B639'],
            [Role::MEMBER, '#1D1D1D'],
            [Role::INACTIVE, '#626262'],
            [Role::USER, '#888888'],
            [Role::VERIFIED, '#626262'],
            [Role::BANNED, '#A2A2A2']
        );

        $this->io->progressStart(count($roles));

        foreach ($roles as $role) {
            list($name, $color) = $role;

            $r = new Role();
            $r->setName($name);
            $r->setColor($color);

            $this->em->persist($r);
            $this->io->progressAdvance(1);
        }

        $this->em->flush();
        $this->io->progressFinish();
    }

    /**
     * @throws \Exception
     */
    protected function registerUsers()
    {
        /** @var array $users Username, mail, password */
        $users = array(
            ['admin', 'admin@style34.net', 'rootpass', Role::ADMIN],
            ['spravce', 'spravce@style34.net', '', Role::BANNED],
            ['moderator', 'moderator@style34.net', '', Role::BANNED],
            ['mod', 'mod@style34.net', '', Role::BANNED],
            ['administrator', 'administrator@style34.net', '', Role::BANNED]
        );

        $this->io->progressStart(count($users));

        foreach ($users as $user) {
            list($username, $email, $password, $role) = $user;

            $profile = new Profile();
            $profile->setUsername($username);
            $profile->setEmail($email);
            $profile->setCreatedAt(new \DateTime());
            $profile->setPassword($this->passwordEncoder->encodePassword($profile, $password));
            $profile->addRole($role);
            $profile->setLastIp('127.0.0.1');
            $profile->setRegisteredAs(serialize(array($profile->getUsername(), $profile->getEmail())));

            $this->em->persist($profile);
            $this->io->progressAdvance(1);
        }

        $this->em->flush();
        $this->io->progressFinish();
    }

    /**
     *
     */
    protected function createTokenTypes()
    {
        $types = array(
            [TokenType::PROFILE['ACTIVATION']],
        );

        $this->io->progressStart(count($types));

        foreach ($types as $type) {
            list($name) = $type;

            $tt = new TokenType();
            $tt->setName($name);

            $this->em->persist($tt);
            $this->io->progressAdvance(1);
        }

        $this->em->flush();
        $this->io->progressFinish();
    }
}