<?php

namespace AppBundle\Service;

use AppBundle\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

class StudentService
{
    /** @var EntityManagerInterface $manager  */
    protected $manager;


    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->manager->getConfiguration()->setSQLLogger(null);
    }

    public function getStudentsDuplicated()
    {
        $this->manager->getConfiguration()->setSQLLogger(null);
        $students = $this->manager
            ->getRepository(Student::class)->createQueryBuilder('s')
            ->select('s.name')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();

        $names = array_column($students, 'name');
        $cntArray = array_count_values($names);

        $names = array();
        $dpNames = array();
        $unqNames = array();
        $i = 0;
        foreach($cntArray as $key => $val){
            if($val == 1){
                $i++;
                $unqNames[$i-1] = $key;
            } else {
                $i++;
                $dpNames[$i-1] = $key;
            }
        }

        $names['duplicatedNames'] = $dpNames;
        $names['uniqueNames'] = $unqNames;

        return $names;


    }

    public function updatePathStudents($studentsNames)
    {
        $this->manager->getConfiguration()->setSQLLogger(null);
        gc_enable();
        gc_collect_cycles();

        $i = 0;
        foreach ($studentsNames['uniqueNames'] as $name) {
            $i++;
            $students = $this->manager->getRepository(Student::class)
                ->findBy(['name' => $name],['name' => 'ASC']);
            foreach ($students as $key => $student)
            {
                /** @var Student $student */
                $name = preg_replace('/\s+/', '_', strtolower($student->getName()));
                if($key > 0) {
                    $student->setPath(sprintf("%s_%s", $name, $key));
                } else {
                    $student->setPath(sprintf("%s", $name));
                }
                $this->manager->persist($student);

            }
            if ($i > 50) {

                gc_enable();
                gc_collect_cycles();
                $i = 0;
            }
        }

        foreach ($studentsNames['duplicatedNames'] as $name) {
            $students = $this->manager->getRepository(Student::class)
                ->findBy(['name' => $name],['name' => 'ASC']);
            foreach ($students as $key => $student)
            {
                /** @var Student $student */
                print_r($key);
                $name = preg_replace('/\s+/', '_', strtolower($student->getName()));
                if($key > 0) {
                    $student->setPath(sprintf("%s_%s", $name, $key));
                } else {
                    $student->setPath(sprintf("%s", $name));
                }
                $this->manager->persist($student);

            }
        }

        $this->manager->flush();
    }
}