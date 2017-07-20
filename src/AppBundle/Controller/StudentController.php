<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Student;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Student controller.
 *
 * @Route("student")
 */
class StudentController extends Controller
{

    /**
     * Finds and displays a student entity.
     *
     * @Route("/{path}", name="student_show")
     * @Method("GET")
     *
     * @
     */
    public function showAction(Student $student)
    {
        $response = new Response();

        $response = $this->render('student/show.html.twig', array(
            'student' => $student,
        ));
        $response->setSharedMaxAge(30);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}
