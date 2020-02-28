<?php declare(strict_types=1);

namespace EryseClient\Client\Install\Command;

use DateTime;
use Doctrine\Bundle\MigrationsBundle\Command\MigrationsMigrateDoctrineCommand;
use EryseClient\Client\Profile\Entity\ProfileEntity;
use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Client\Profile\Role\Entity\RoleEntity;
use EryseClient\Client\Profile\Role\Repository\RoleRepository;
use EryseClient\Common\Utility\LoggerAwareTrait;
use EryseClient\Server\Token\Type\Entity\TypeEntity;
use EryseClient\Server\Token\Type\Repository\TypeRepository;
use EryseClient\Server\User\Entity\UserEntity;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Service\UserService;
use EryseClient\Server\User\Role\Entity\RoleEntity as UserRole;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Class InstallCommand.
 * Install only app-required data, not testing fixtures! For dev/test data use fixtures in DataFixtures.
 *
 *
 */
class InstallCommand extends Command
{
    use LoggerAwareTrait;

    /** @var SymfonyStyle $io */
    private $io;

    /** @var OutputInterface $output */
    private $output;

    /** @var CacheInterface $cache */
    private $cacheInterface;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /** @var UserService */
    private $userService;

    /** @var UserRepository */
    private $userRepository;

    /** @var TypeRepository */
    private $tokenTypeRepository;

    /** @var RoleRepository */
    private $profileRoleRepository;

    /** @var ProfileRepository */
    private $profileRepository;

    /**
     * InstallBaseCommand constructor
     *
     * @param CacheInterface $cache
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param TypeRepository $tokenTypeRepository
     * @param RoleRepository $profileRoleRepository
     * @param ProfileRepository $profileRepository
     */
    public function __construct(
        CacheInterface $cache,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository,
        TypeRepository $tokenTypeRepository,
        RoleRepository $profileRoleRepository,
        ProfileRepository $profileRepository
    ) {
        $this->cacheInterface = $cache;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->tokenTypeRepository = $tokenTypeRepository;
        $this->profileRoleRepository = $profileRoleRepository;
        $this->profileRepository = $profileRepository;
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
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force option will recreate database from new. All data will be lost!'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->io = $io = new SymfonyStyle($input, $output);

        try {
            $io->title('Starting the app installation');

            // Prepare databases
            if ($input->getOption('drop')) {
                $io->section('Clearing databases...');
                $this->dropSchema();
            } elseif ($input->getOption('force')) {
                $io->section('Forcing new database...');
                $this->recreateDatabase();

                $io->section('Creating databases...');
                $this->createDatabase();
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

//            // Build browsercap cache
//            $io->section('Installing browsercap (this takes long...)');
//            $this->fetchBrowsercap();

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

            return 1;
        }

        return 0;
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
                    "--em" => "eryseClient",
                ]
            ),
            new ArrayInput(
                [
                    '--full-database' => true,
                    '--force' => true,
                    "--em" => "eryseServer",
                ]
            ),
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
     *
     */
    protected function recreateDatabase(): void
    {
        $this->io->progressStart(1);

        $command = $this->getApplication()
            ->find('doctrine:database:drop');

        $options = [
            "command" => "doctrine:database:drop",
            "--connection" => "eryseClient",
        ];

        $arguments = new ArrayInput($options);
        $command->run($arguments, new NullOutput());

        $options = [
            "command" => "doctrine:database:drop",
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
                "--configuration" => "./bin/migrations_client.yaml",
            ],
            [
                "command" => "doctrine:migrations:migrate",
                "--quiet" => true,
                "--no-interaction" => true,
                "--em" => "eryseServer",
                "--configuration" => "./bin/migrations_server.yaml",
            ],
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
     *
     */
    protected function createRoles(): void
    {
        $roles = [
            [RoleEntity::ADMIN, '#CB2910'],
            [RoleEntity::MODERATOR, '#00B639'],
            [RoleEntity::MEMBER, '#1D1D1D'],
            [RoleEntity::INACTIVE, '#626262'],
            [RoleEntity::BANNED, '#A2A2A2'],
            [RoleEntity::DELETED, '#A2A2A2'],
        ];

        $this->io->progressStart(count($roles));

        foreach ($roles as $role) {
            [$name, $color] = $role;

            $r = new RoleEntity();
            $r->setName($name);
            $r->setColor($color);

            $this->profileRoleRepository->save($r);

            $this->io->progressAdvance(1);
        }

        $this->io->progressFinish();
    }

    /**
     * @throws Exception
     */
    protected function registerUsers(): void
    {
        /** @var array $users Username, mail, password */
        $users = [
            ['admin', 'admin@EryseClient.net', 'rootpass', UserRole::ADMIN, RoleEntity::ADMIN],
            ['spravce', 'spravce@EryseClient.net', 'null', UserRole::DELETED, RoleEntity::DELETED],
            ['moderator', 'moderator@EryseClient.net', 'null', UserRole::DELETED, RoleEntity::DELETED],
            ['mod', 'mod@EryseClient.net', 'null', UserRole::DELETED, RoleEntity::DELETED],
            ['administrator', 'administrator@EryseClient.net', 'null', UserRole::DELETED, RoleEntity::DELETED],
        ];

        $this->io->progressStart(count($users));

        foreach ($users as $user) {
            [$username, $email, $password, $userRole, $profileRole] = $user;

            /**
             * TODO: server user should be created in server app
             */
            $user = new UserEntity();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setCreatedAt(new DateTime());

            if ($userRole === UserRole::DELETED) {
                $user->setDeletedAt(new DateTime());
            }

            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setRole($userRole);
            $user->setLastIp('127.0.0.1');
            $user->setRegisteredAs(serialize([$user->getUsername(), $user->getEmail()]));

            $this->userRepository->saveNew($user);

            $profile = new ProfileEntity();
            $profile->setUserId($user->getId());
            $profile->setRole($this->profileRoleRepository->findOneByName($profileRole));

            $this->profileRepository->save($profile);

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
            [TypeEntity::USER['ACTIVATION']],
            [TypeEntity::USER['REQUEST_RESET_PASSWORD']],
        ];

        $this->io->progressStart(count($types));

        foreach ($types as $type) {
            [$name] = $type;

            $tt = new TypeEntity();
            $tt->setName($name);

            $this->tokenTypeRepository->save($tt);
            $this->io->progressAdvance(1);
        }

        $this->io->progressFinish();
    }

//    protected function fetchBrowsercap(): void
//    {
//        $this->io->progressStart(2);
//        $browscap_updater = new BrowscapUpdater($this->cacheInterface, $this->logger);
//        $this->io->progressAdvance(1);
//        $browscap_updater->update(IniLoader::PHP_INI_FULL);
//        $this->io->progressFinish();
//    }

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
