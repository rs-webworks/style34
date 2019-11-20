<?php declare(strict_types=1);

namespace EryseClient\Client\Install\Command;

use BrowscapPHP\BrowscapUpdater;
use BrowscapPHP\Helper\IniLoader;
use DateTime;
use Doctrine\Bundle\MigrationsBundle\Command\MigrationsMigrateDoctrineCommand;
use EryseClient\Client\Token\Repository\TokenTypeRepository;
use EryseClient\Server\UserRole\Entity\UserRole;
use EryseClient\Client\Token\Entity\TokenType;
use EryseClient\Common\Utility\LoggerTrait;
use EryseClient\Server\User\Entity\User;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Service\UserService;
use EryseClient\Utility\EntityManagersTrait;
use Exception;
use Psr\SimpleCache\CacheInterface;
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
 * @package EryseClient\Command
 */
class InstallCommand extends Command
{
    use EntityManagersTrait;
    use LoggerTrait;

    /** @var SymfonyStyle $io */
    protected $io;

    /** @var OutputInterface $output */
    protected $output;

    /** @var CacheInterface $cache */
    protected $cacheInterface;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    protected $passwordEncoder;

    /** @var UserService */
    private $userService;

    /** @var UserRepository */
    private $userRepository;

    /** @var TokenTypeRepository */
    private $tokenTypeRepository;

    /**
     * InstallBaseCommand constructor
     * @param CacheInterface $cache
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param TokenTypeRepository $tokenTypeRepository
     */
    public function __construct(
        CacheInterface $cache,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository,
        TokenTypeRepository $tokenTypeRepository
    ) {
        $this->cacheInterface = $cache;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->tokenTypeRepository = $tokenTypeRepository;
        parent::__construct();
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setName('app:install')
            ->setDescription('Install the basic database stuff (Roles, Permissions, Users...')
            ->setHelp(
                'This will run some persists to DB and create basic stuff. !IMPORTANT! Run fixtures
            only AFTER this installation!'
            )
            ->addOption(
                'drop',
                'd',
                InputOption::VALUE_NONE,
                'Reinstall option, this will drop schema instead of creating database. You will lost any DB data.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->output = $output;
        $this->io = $io = new SymfonyStyle($input, $output);

        try {
            $io->title('Starting the app installation');

            // Prepare databases
            if ($input->getOption('drop')) {
                $io->section('Clearing databases...');
                $this->dropSchema();
            } else {
                $io->section('Creating databases...');
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

            // Build browsercap cache
            $io->section('Installing browsercap (this takes long...)');
            $this->fetchBrowsercap();

            // Build browsercap cache
            $io->section('Clearing cache...');
            $this->clearCache();

            $io->success('Installation complete!');
        } catch (Exception $ex) {
            $io->newLine();
            $io->newLine();
            $io->error($ex->getMessage());
            $io->text('Maybe you are trying to do reinstall? Then use:');
            $io->newLine();
            $io->text('    <info>app:install --drop</info> to clear database (you will loose all data)');
        }
    }

    /**
     * @throws Exception
     */
    protected function dropSchema(): void
    {
        $command = $this->getApplication()
            ->find('doctrine:schema:drop');

        $arguments = [
            new ArrayInput(
                [
                    '--full-database' => true,
                    '--force' => true,
                    "--em" => "eryseClient"
                ]
            ),
            new ArrayInput(
                [
                    '--full-database' => true,
                    '--force' => true,
                    "--em" => "eryseServer"
                ]
            )
        ];

        $this->io->progressStart(count($arguments));

        foreach ($arguments as $args) {
            $command->run($args, new NullOutput());
            $this->io->progressAdvance(1);
        }

        $this->io->progressFinish();
    }

    /**
     * @throws Exception
     */
    protected function createDatabase(): void
    {
        $this->io->progressStart(1);

        $command = $this->getApplication()
            ->find('doctrine:database:create');

        $options = [
            "command" => "doctrine:database:create",
            "--connection" => "eryseClient",
        ];

        $arguments = new ArrayInput($options);
        $command->run($arguments, new NullOutput());

        $options = [
            "command" => "doctrine:database:create",
            "--connection" => "eryseServer",
        ];

        $arguments = new ArrayInput($options);
        $command->run($arguments, new NullOutput());

        $this->io->progressAdvance(1);
        $this->io->progressFinish();
    }

    /**
     * @throws Exception
     */
    protected function runMigrations(): void
    {
        $arguments = [
            [
                "command" => "doctrine:migrations:migrate",
                "--quiet" => true,
                "--no-interaction" => true,
                "--em" => "eryseClient",
                "--configuration" => "./bin/dm_eryseClient.yaml"
            ],
            [
                "command" => "doctrine:migrations:migrate",
                "--quiet" => true,
                "--no-interaction" => true,
                "--em" => "eryseServer",
                "--configuration" => "./bin/dm_eryseServer.yaml"
            ]
        ];

        $this->io->progressStart(count($arguments));

        foreach ($arguments as $args) {
            $command = new MigrationsMigrateDoctrineCommand();
            $command->setApplication($this->getApplication());

            $arguments = new ArrayInput($args);
            $arguments->setInteractive(false);

            $command->run($arguments, new NullOutput());
            $this->io->progressAdvance(1);
        }

        $this->io->progressFinish();
    }

    /**
     * TODO: Save profile roles, not user roles
     */
//    protected function createRoles(): void
//    {
//        $roles = [
//            [UserRole::ADMIN, '#CB2910'],
//            [UserRole::MODERATOR, '#00B639'],
//            [UserRole::MEMBER, '#1D1D1D'],
//            [UserRole::INACTIVE, '#626262'],
//            [UserRole::VERIFIED, '#626262'],
//            [UserRole::BANNED, '#A2A2A2'],
//            [UserRole::DELETED, '#A2A2A2'],
//        ];
//
//        $this->io->progressStart(count($roles));
//
//        foreach ($roles as $role) {
//            list($name, $color) = $role;
//
//            $r = new UserRole();
//            $r->setName($name);
//            $r->setColor($color);
//
//            $this->clientEm->persist($r);
//            $this->io->progressAdvance(1);
//        }
//
//        $this->clientEm->flush();
//        $this->io->progressFinish();
//    }

    /**
     * @throws Exception
     */
    protected function registerUsers(): void
    {
        /** @var array $users Username, mail, password */
        $users = [
            ['admin', 'admin@EryseClient.net', 'rootpass', UserRole::ADMIN],
            ['spravce', 'spravce@EryseClient.net', 'null', UserRole::DELETED],
            ['moderator', 'moderator@EryseClient.net', 'null', UserRole::DELETED],
            ['mod', 'mod@EryseClient.net', 'null', UserRole::DELETED],
            ['administrator', 'administrator@EryseClient.net', 'null', UserRole::DELETED]
        ];

        $this->io->progressStart(count($users));

        foreach ($users as $user) {
            list($username, $email, $password, $role) = $user;

            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setCreatedAt(new DateTime());

            if ($role === UserRole::DELETED) {
                $user->setDeletedAt(new DateTime());
            }

            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setRole($role);
            $user->setLastIp('127.0.0.1');
            $user->setRegisteredAs(serialize([$user->getUsername(), $user->getEmail()]));

            $this->userRepository->saveNew($user);
            $this->io->progressAdvance(1);
        }

        $this->io->progressFinish();
    }

    /**
     *
     */
    protected function createTokenTypes(): void
    {
        $types = [
            [TokenType::USER['ACTIVATION']],
            [TokenType::USER['REQUEST_RESET_PASSWORD']],
        ];

        $this->io->progressStart(count($types));

        foreach ($types as $type) {
            list($name) = $type;

            $tt = new TokenType();
            $tt->setName($name);

            $this->tokenTypeRepository->save($tt);
            $this->io->progressAdvance(1);
        }

        $this->io->progressFinish();
    }

    protected function fetchBrowsercap(): void
    {
        $this->io->progressStart(2);
        $browscap_updater = new BrowscapUpdater($this->cacheInterface, $this->logger);
        $this->io->progressAdvance(1);
        $browscap_updater->update(IniLoader::PHP_INI_FULL);
        $this->io->progressFinish();
    }

    /**
     * @throws Exception
     */
    protected function clearCache(): void
    {
        $this->io->progressStart(1);
        $command = $this->getApplication()
            ->find('cache:clear');
        $arguments = new ArrayInput([]);

        $command->run($arguments, new NullOutput());

        $this->io->progressAdvance(1);
        $this->io->progressFinish();
    }
}
