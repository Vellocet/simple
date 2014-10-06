<?php

namespace Vellocet\SimpleBundle\Service;
use Symfony\Component\Validator\Constraints;
use Vellocet\SimpleBundle\Exception\ValidationException;
use Vellocet\SimpleBundle\Exception\NotFoundException;
use Vellocet\SimpleBundle\Helper\ValidationHelper;
use Vellocet\SimpleBundle\Entity\Category;

/**
 * Manages and persists categories
 * Class: CategoryService
 *
 */
class CategoryService 
{
    /**
     * em
     * Entity manager for persistance
     * @var Docrtine\Orm\EntityManagerInterface
     */
    private $em;

    /**
     * validator
     * Validator for input
     * @var mixed
     */
    private $validator;

    const MAX_NAME_LEN = 15;

    /**
     * __construct
     * Constructor for DI
     *
     * @param Doctrine\ORM\EntityManagerInterface $em
     * @param Symfony\Component\Validator\ValidatorInterface $validator
     */
    public function __construct(
        \Doctrine\ORM\EntityManagerInterface $em, 
        \Symfony\Component\Validator\ValidatorInterface $validator
    )
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * createCategory
     * Creates a category and saves to database
     * @param array $params
     */
    public function createCategory($params)
    {
        // Validates parameters, throws ValidationException
        $this->validateParams($params);

        // Create and persist category
        $category = new Category;
        $category->setName($params['name']);
        $this->em->persist($category);
        $this->em->flush();

        return $category;
    }

    public function getCategory($id)
    {
        $category = $this->em->getRepository("VellocetSimpleBundle:Category")->find(1);
        if ($category === null)
        {
            throw new NotFoundException("Category not found");
        }

        return $category;
    }

    public function updateCategory($id, $params)
    {
        $category = $this->em->getRepository("VellocetSimpleBundle:Category")->find(1);
        if ($category === null)
        {
            throw new NotFoundException("Category not found");
        }

        $this->validateParams($params);

        $category->setName($params['name']);
        $this->em->flush();

        return $category;
    }

    public function deleteCategory($id)
    {
        $category = $this->em->getRepository("VellocetSimpleBundle:Category")->find(1);
        if ($category === null)
        {
            throw new NotFoundException("Category not found");
        }

        $this->em->remove($category);
        $this->em->flush();
    }

    private function validateParams($params) {
        $constraints = new Constraints\Collection(array(
            'name' => array(
                new Constraints\NotBlank(array(
                    'message' => 'Name must not be blank'
                )),
                new Constraints\Length(array(
                    'maxMessage' => 'Name must not be longer than ' . self::MAX_NAME_LEN,
                    'max' => self::MAX_NAME_LEN
                )),
            ),
        ));

        // Validate and check for errors
        $violationList = $this->validator->validateValue($params, $constraints);
        $errors = ValidationHelper::buildErrors($violationList);

        if (count($errors) > 0)
        {
            throw new ValidationException($errors);
        }
    }
}
