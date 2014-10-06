<?php

namespace Vellocet\SimpleBundle\Service;
use Symfony\Component\Validator\Constraints;
use Vellocet\SimpleBundle\Exception\ValidationException;
use Vellocet\SimpleBundle\Exception\NotFoundException;
use Vellocet\SimpleBundle\Helper\ValidationHelper;
use Vellocet\SimpleBundle\Entity\Category;
use Vellocet\SimpleBundle\Entity\Item;

/**
 * Manages and persists items 
 * Class: ItemService
 *
 */
class ItemService 
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

    public function createItem($params)
    {
        $this->validateParams($params);

        $category = $this->em->getRepository("VellocetSimpleBundle:Category")->find($params['category']);
        if ($category === null)
        {
            throw new NotFoundException("Category not found");
        }

        $item = new Item;
        $item->setName($params['name']);
        $item->setPrice($params['price']);
        $item->setCategory($category);
        $this->em->persist($item);
        $this->em->flush();

        return $item;
    }

    public function getItem($id)
    {
        $item = $this->em->getRepository("VellocetSimpleBundle:Item")->find($id);
        if ($item === null)
        {
            throw new NotFoundException("Item not found");
        }

        return $item;
    }

    public function updateItem($id, $params)
    {
        $item = $this->em->getRepository("VellocetSimpleBundle:Item")->find($id);
        if ($item === null)
        {
            throw new NotFoundException("Item not found");
        }

        $this->validateParams($params);

        $category = $this->em->getRepository("VellocetSimpleBundle:Category")->find($params['category']);
        if ($category === null)
        {
            throw new NotFoundException("Category not found");
        }

        $item->setName($params["name"]);
        $item->setPrice($params["price"]);
        $item->setCategory($category);
        $this->em->flush();
        return $item;
    }

    public function deleteItem($id)
    {
        $item = $this->em->getRepository("VellocetSimpleBundle:Item")->find($id);
        if ($item === null)
        {
            throw new NotFoundException("Item not found");
        }

        $this->em->remove($item);
        $this->em->flush();
    }

    private function validateParams($params)
    {
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
            'price' => array(
                new Constraints\NotBlank(array(
                    'message' => 'Price must not be blank'
                )),
            ),
            'category' => array(
                new Constraints\NotBlank(array(
                    'message' => 'Category must not be blank'
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
