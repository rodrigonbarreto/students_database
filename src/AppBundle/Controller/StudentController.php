<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Student;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
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
     * @Cache(maxage="900", public=true)
     *
     * @Method("GET")
     */
    public function showAction(Student $student)
    {
        $response = $this->render('student/show.html.twig', array(
            'student' => $student,
        ));
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}
