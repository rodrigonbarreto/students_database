<?php

namespace AppBundle\Command;

use AppBundle\Entity\Student;
use AppBundle\Service\StudentService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddPathToStudentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('add-path-to-student')
            ->setDescription('Fill in “path” property of Student entity in a way, path contains student’s name in lower case, all non-letters are replaced with underscore ')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var StudentService $studentService */
        $studentService = $this->getContainer()->get('app.exercise_service');
        $em =$this->getContainer()->get('doctrine.orm.entity_manager');

        $time_start = microtime(true);
        $students = $studentService->getStudents();
        $i = 0;
        foreach ($students as list($student)) {
            /** @var Student $student */
            $path = $studentService->updatePathStudents($student->getName());
            $student->setPath($path);
            if ($i > 2000) {
                $em->flush();
                $em->clear();
                gc_collect_cycles();
                $i = 0 ;
            }
            ++$i;
        }

        $em->flush();
        $time_end = microtime(true);
        $memoryUsage = round(memory_get_usage() / (1024 * 1024));
        $time = $time_end - $time_start;
        $output->writeln(sprintf('Memory usage: %s MB',$memoryUsage));
        $output->writeln(sprintf('Time: %s s',$time));

    }

}
