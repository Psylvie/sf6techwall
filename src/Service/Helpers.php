<?php

    namespace App\Service;

    use App\Entity\User;
    use Psr\Log\LoggerInterface;
    use Symfony\Component\Security\Core\Security;

    class Helpers
    {
    private $langue;



        public function __construct(private LoggerInterface $logger, Security $security)
    {

    }

    public function sayCoucou(): string
    {
    $this->logger->info('je dis coucou');
    return 'hey coucou!! ';
    }
    public function getUser(): User{
    return $this->security->getUser();
    }
    }