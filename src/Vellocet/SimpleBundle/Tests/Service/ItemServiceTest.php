<?php

namespace Vellocet\SimpleBundle\Service;

namespace Vellocet\SimpleBundle\Tests\Service;
use Vellocet\SimpleBundle\Service\ItemService;
use Vellocet\SimpleBundle\Exception\ValidationException;
use Vellocet\SimpleBundle\Entity\Category;
use Vellocet\SimpleBundle\Entity\Item;

class ItemServiceTest extends \Vellocet\SimpleBundle\Tests\TestBase
{
    public function testCreateItem()
    {
        $itemName = 'Item1';
        $price = '9.99';

        $category = $this->createCategory("Cat1");
        $params = array(
            'name' => $itemName,
            'price' => $price,
            'category' => "1"
        );

        $itemService = $this->getItemService();
        $item = $itemService->createItem($params);

        $this->assertEquals(1, $item->getId());
        $this->assertEquals($itemName, $item->getName());
        $this->assertEquals($price, $item->getPrice());
        $this->assertEquals($category, $item->getCategory());

        $loadedItem = $this->em->getRepository("VellocetSimpleBundle:Item")->find(1);

        $this->assertEquals($item, $loadedItem);

    }

    public function testCreateItemNameBlank()
    {
        $itemName = '';
        $price = '9.99';

        $category = $this->createCategory("Cat1");
        $params = array(
            'name' => $itemName,
            'price' => $price,
            'category' => "1"
        );

        $itemService = $this->getItemService();
        try
        {
            $item = $itemService->createItem($params);
        }
        catch (ValidationException $e)
        {
            $errors = $e->getErrors();
        }
        $this->assertTrue(isset($errors['name']));
        $this->assertEquals('Name must not be blank', $errors['name']);
    }

    public function testCreateItemNameTooLong()
    {
        $itemName = 'aaaaa.aaaaa.aaaaa';
        $price = '9.99';

        $category = $this->createCategory("Cat1");
        $params = array(
            'name' => $itemName,
            'price' => $price,
            'category' => "1"
        );

        $itemService = $this->getItemService();
        try
        {
            $item = $itemService->createItem($params);
        }
        catch (ValidationException $e)
        {
            $errors = $e->getErrors();
        }
        $this->assertTrue(isset($errors['name']));
        $this->assertEquals('Name must not be longer than ' . $itemService::MAX_NAME_LEN, $errors['name']);
    }

    public function testCreateItemPriceBlank()
    {
        $itemName = 'Test';
        $price = '';

        $category = $this->createCategory("Cat1");
        $params = array(
            'name' => $itemName,
            'price' => $price,
            'category' => "1"
        );

        $itemService = $this->getItemService();
        try
        {
            $item = $itemService->createItem($params);
        }
        catch (ValidationException $e)
        {
            $errors = $e->getErrors();
        }
        $this->assertTrue(isset($errors['price']));
        $this->assertEquals('Price must not be blank', $errors['price']);
    }

    public function testCreateItemCategoryBlank()
    {
        $itemName = 'Test';
        $price = '9.99';

        $category = $this->createCategory("Cat1");
        $params = array(
            'name' => $itemName,
            'price' => $price,
            'category' => ''
        );

        $itemService = $this->getItemService();
        try
        {
            $item = $itemService->createItem($params);
        }
        catch (ValidationException $e)
        {
            $errors = $e->getErrors();
        }
        $this->assertTrue(isset($errors['category']));
        $this->assertEquals('Category must not be blank', $errors['category']);

    }

    public function testUpdateItem()
    {
        $category = $this->createCategory("Cat1");
        $item = $this->createItem($category);
        $newCategory = $this->createCategory("Cat2");

        $newName = "NewName";
        $newPrice = 10.99;

        $params = array(
            'name' => $newName,
            'price' => $newPrice,
            'category' => $newCategory->getId()
        );

        $itemService = $this->getItemService();
        $item = $itemService->updateItem(1, $params);

        $this->assertEquals(1, $item->getId());
        $this->assertEquals($newName, $item->getName());
        $this->assertEquals($newPrice, $item->getPrice());
        $this->assertEquals($newCategory, $item->getCategory());

        $loadedItem = $this->em->getRepository("VellocetSimpleBundle:Item")->find(1);

        $this->assertEquals($item, $loadedItem);
    }

    /**
     * @expectedException Vellocet\SimpleBundle\Exception\NotFoundException
     */
    public function testUpdateItemNotFound()
    {
        $category = $this->createCategory("Cat1");
        $newCategory = $this->createCategory("Cat2");

        $newName = "NewName";
        $newPrice = 10.99;

        $params = array(
            'name' => $newName,
            'price' => $newPrice,
            'category' => $newCategory->getId()
        );

        $itemService = $this->getItemService();
        $item = $itemService->updateItem(1, $params);
    }


    public function testUpdateItemNameBlank()
    {
        $category = $this->createCategory("Cat1");
        $item = $this->createItem($category);
        $newCategory = $this->createCategory("Cat2");

        $newName = "";
        $newPrice = 10.99;

        $params = array(
            'name' => $newName,
            'price' => $newPrice,
            'category' => $newCategory->getId()
        );

        $itemService = $this->getItemService();
        try
        {
            $item = $itemService->updateItem(1, $params);
        }
        catch (ValidationException $e)
        {
            $errors = $e->getErrors();
        }
        $this->assertTrue(isset($errors['name']));
        $this->assertEquals('Name must not be blank', $errors['name']);
    }

    public function testUpdateItemNameTooLong()
    {
        $category = $this->createCategory("Cat1");
        $item = $this->createItem($category);
        $newCategory = $this->createCategory("Cat2");

        $newName = 'aaaaa.aaaaa.aaaaa';
        $newPrice = 10.99;

        $params = array(
            'name' => $newName,
            'price' => $newPrice,
            'category' => $newCategory->getId()
        );

        $itemService = $this->getItemService();
        try
        {
            $item = $itemService->updateItem(1, $params);
        }
        catch (ValidationException $e)
        {
            $errors = $e->getErrors();
        }
        $this->assertTrue(isset($errors['name']));
        $this->assertEquals('Name must not be longer than ' . $itemService::MAX_NAME_LEN, $errors['name']);
    }

    public function testUpdateItemPriceBlank()
    {
        $category = $this->createCategory("Cat1");
        $item = $this->createItem($category);
        $newCategory = $this->createCategory("Cat2");

        $newName = "Test";
        $newPrice = "";

        $params = array(
            'name' => $newName,
            'price' => $newPrice,
            'category' => $newCategory->getId()
        );

        $itemService = $this->getItemService();
        try
        {
            $item = $itemService->updateItem(1, $params);
        }
        catch (ValidationException $e)
        {
            $errors = $e->getErrors();
        }
        $this->assertTrue(isset($errors['price']));
        $this->assertEquals('Price must not be blank', $errors['price']);
    }

    public function testUpdateItemCategoryBlank()
    {
        $category = $this->createCategory("Cat1");
        $item = $this->createItem($category);
        $newCategory = $this->createCategory("Cat2");

        $newName = "Test";
        $newPrice = 10.99;

        $params = array(
            'name' => $newName,
            'price' => $newPrice,
            'category' => ''
        );

        $itemService = $this->getItemService();
        try
        {
            $item = $itemService->updateItem(1, $params);
        }
        catch (ValidationException $e)
        {
            $errors = $e->getErrors();
        }
        $this->assertTrue(isset($errors['category']));
        $this->assertEquals('Category must not be blank', $errors['category']);
    }

    public function testGetItem()
    {
        $category = $this->createCategory("Cat1");
        $item = $this->createItem($category);

        $itemService = $this->getItemService();
        $loadedItem = $itemService->getItem(1);

        $this->assertEquals($item, $loadedItem);
    }

    /**
     * @expectedException Vellocet\SimpleBundle\Exception\NotFoundException
     */
    public function testGetItemNotFound()
    {
        $itemService = $this->getItemService();
        $item = $itemService->getItem(1);
    }

    public function testDeleteItem()
    {
        $category = $this->createCategory("Cat1");
        $this->createItem($category, 'Test');
        $itemService = $this->getItemService();

        try
        {
            $itemService->deleteItem(1);
        }
        catch (ValidationException $e)
        {
            $errors = $e->getErrors();
        }

        $loadedItem = $this->em->getRepository("VellocetSimpleBundle:Item")->find(1);

        $this->assertNull($loadedItem);
    }

    /**
     * @expectedException Vellocet\SimpleBundle\Exception\NotFoundException
     */
    public function testDeleteItemNotFound()
    {
        $itemService = $this->getItemService();
        $itemService->deleteItem(1);
    }


    private function getItemService()
    {
        return new ItemService($this->em, $this->validator);
    }

    private function createCategory($name)
    {
        $category = new Category;
        $category->setName($name);

        $this->em->persist($category);
        $this->em->flush();
        return $category;
    }

    private function createItem($category)
    {
        $item = new Item;
        $item->setName("Test");
        $item->setPrice(9.99);
        $item->setCategory($category);

        $this->em->persist($item);
        $this->em->flush();
        return $item;
    }

}
