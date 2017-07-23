<?php

namespace AppBundle\Service;

use AppBundle\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

class StudentService
{
    /** @var EntityManagerInterface $manager  */
    protected $manager;
    private $cache = [];

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->manager->getConfiguration()->setSQLLogger(null);
    }

    public function getStudents()
    {
        $this->manager->getConfiguration()->setSQLLogger(null);
        $students = $this->manager
            ->getRepository(Student::class)->createQueryBuilder('s')
            ->select('s')->getQuery()->iterate();
        return $students;
    }

    /**
     * @param string $subject
     *
     * @return mixed
     */
    public function encodeString($subject)
    {
        $sanitized = preg_replace('/\W/u', '_', $subject);
        $lower = mb_strtolower($sanitized);
        $trimmed = trim($lower, '_');
        return preg_replace('/_{2,}/u', '_', $trimmed);
    }

    /**
     * @param string $path
     *
     * @return mixed|string
     */
    public function updatePathStudents($path)
    {
        $path = $this->encodeString($path);
        if (isset($this->cache[$path])) {
            $i = 1;
            do {
                $pathCheck = $path . '_' . $i++;
            } while (isset($this->cache[$pathCheck]));
            $path = $pathCheck;
        }

        $this->cache[$path] = true;
        return $path;
    }
}
