<?php

namespace Vellocet\SimpleBundle\Tests\Service;
use Vellocet\SimpleBundle\Service\CategoryService;
use Vellocet\SimpleBundle\Exception\ValidationException;
use Vellocet\SimpleBundle\Entity\Category;

class CategoryServiceTest extends \Vellocet\SimpleBundle\Tests\TestBase
{
    public function testCreate()
    {
        $categoryName = 'TestCategory';
        $categoryService = $this->getCategoryService();

        $params = array(
            'name' => $categoryName
        );

        $category = $categoryService->createCategory($params);

        $this->assertEquals(1, $category->getId());
        $this->assertEquals($categoryName, $category->getName());

        $loadedCategory = $this->em->getRepository("VellocetSimpleBundle:Category")->find(1);

        $this->assertEquals($category, $loadedCategory);
    }

    public function testCreateBlankName()
    {
        $categoryName = '';
        $categoryService = $this->getCategoryService();

        $params = array(
            'name' => $categoryName
        );

        try
        {
            $categoryService->createCategory($params);
        }
        catch (ValidationException $e)
        {
            $errors = $e->getErrors();
        }
        $this->assertTrue(isset($errors['name']));
        $this->assertEquals('Name must not be blank', $errors['name']);
    }

    public function testCreateLongName()
    {
        $categoryName = 'aaaaa.aaaaa.aaaaa';
        $categoryService = $this->getCategoryService();

        $params = array(
            'name' => $categoryName
        );

        try
        {
            $categoryService->createCategory($params);
        }
        catch (ValidationException $e)
        {
            $errors = $e->getErrors();
        }

        $this->assertTrue(isset($errors['name']));
        $this->assertEquals('Name must not be longer than ' . $categoryService::MAX_NAME_LEN, $errors['name']);
    }

    public function testUpdate()
    {
        $this->createCategory();
        $categoryName = 'UpdatedValue';
        $categoryService = $this->getCategoryService();

        $params = array(
            'name' => $categoryName
        );

        $category = $categoryService->updateCategory(1, $params);

        $this->assertEquals(1, $category->getId());
        $this->assertEquals($categoryName, $category->getName());

        $loadedCategory = $this->em->getRepository("VellocetSimpleBundle:Category")->find(1);

        $this->assertEquals($category, $loadedCategory);
    }

    /**
     * @expectedException Vellocet\SimpleBundle\Exception\NotFoundException
     */
    public function testUpdateCategoryNotFound()
    {
        $params = array(
            'name' => 'Test' 
        );

        $categoryService = $this->getCategoryService();
        $categoryService->updateCategory(1, $params);
    }

    public function testUpdateBlankName()
    {
        $this->createCategory();
        $categoryName = '';
        $categoryService = $this->getCategoryService();

        $params = array(
            'name' => $categoryName
        );

        try
        {
            $categoryService->updateCategory(1,$params);
        }
        catch (ValidationException $e)
        {
            $errors = $e->getErrors();
        }
        $this->assertTrue(isset($errors['name']));
        $this->assertEquals('Name must not be blank', $errors['name']);
    }

    public function testUpdateLongName()
    {
        $this->createCategory();
        $categoryName = 'aaaaa.aaaaa.aaaaa';
        $categoryService = $this->getCategoryService();

        $params = array(
            'name' => $categoryName
        );

        try
        {
            $categoryService->updateCategory(1,$params);
        }
        catch (ValidationException $e)
        {
            $errors = $e->getErrors();
        }

        $this->assertTrue(isset($errors['name']));
        $this->assertEquals('Name must not be longer than ' . $categoryService::MAX_NAME_LEN, $errors['name']);
    }

    public function testDeleteCategory()
    {
        $this->createCategory();
        $categoryService = $this->getCategoryService();

        try
        {
            $categoryService->deleteCategory(1);
        }
        catch (ValidationException $e)
        {
            $errors = $e->getErrors();
        }

        $loadedCategory = $this->em->getRepository("VellocetSimpleBundle:Category")->find(1);

        $this->assertNull($loadedCategory);
    }

    /**
     * @expectedException Vellocet\SimpleBundle\Exception\NotFoundException
     */
    public function testDeleteCategoryNotFound()
    {
        $categoryService = $this->getCategoryService();
        $categoryService->deleteCategory(1);
    }


    public function testGetCategory()
    {
        $this->createCategory();
        $categoryService = $this->getCategoryService();
        $category = $categoryService->getCategory(1);
        $this->assertEquals(1, $category->getId());
        $this->assertEquals('Test', $category->getName());
    }

    /**
     * @expectedException Vellocet\SimpleBundle\Exception\NotFoundException
     */
    public function testGetCategoryNotFound()
    {
        $categoryService = $this->getCategoryService();
        $categoryService->getCategory(1);
    }

    private function createCategory()
    {
        $category = new Category;
        $category->setName('Test');

        $this->em->persist($category);
        $this->em->flush();
    }

    private function getCategoryService()
    {
        return new CategoryService($this->em, $this->validator);
    }
}
