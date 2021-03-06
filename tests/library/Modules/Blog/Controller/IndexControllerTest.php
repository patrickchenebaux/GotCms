<?php
/**
 * This source file is part of GotCms.
 *
 * GotCms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GotCms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along
 * with GotCms. If not, see <http://www.gnu.org/licenses/lgpl-3.0.html>.
 *
 * PHP Version >=5.3
 *
 * @category Gc_Tests
 * @package  Modules
 * @author   Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license  GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.html
 * @link     http://www.got-cms.com
 */

namespace Modules\Blog\Controller;

use Modules\Blog\Bootstrap;
use Gc\Document\Model as DocumentModel;
use Gc\DocumentType\Model as DocumentTypeModel;
use Gc\Layout\Model as LayoutModel;
use Gc\User\Model as UserModel;
use Gc\View\Model as ViewModel;
use Gc\Registry;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-06 at 14:00:49.
 *
 * @group Modules
 * @category Gc_Tests
 * @package  Modules
 */
class IndexControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IndexController
     */
    protected $object;

    /**
     * @var Bootstrap
     */
    protected $boostrap;

    /**
     * @var Model
     */
    protected $document;

    /**
     * @var ViewModel
     */
    protected $view;

    /**
     * @var LayoutModel
     */
    protected $layout;

    /**
     * @var UserModel
     */
    protected $user;

    /**
     * @var DocumentTypeModel
     */
    protected $documentType;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->view = ViewModel::fromArray(
            array(
                'name' => 'View Name',
                'identifier' => 'View identifier',
                'description' => 'View Description',
                'content' => 'View Content'
            )
        );
        $this->view->save();

        $this->layout = LayoutModel::fromArray(
            array(
                'name' => 'Layout Name',
                'identifier' => 'Layout identifier',
                'description' => 'Layout Description',
                'content' => 'Layout Content'
            )
        );
        $this->layout->save();

        $this->user = UserModel::fromArray(
            array(
                'lastname' => 'User test',
                'firstname' => 'User test',
                'email' => 'pierre.rambaud86@gmail.com',
                'login' => 'test',
                'user_acl_role_id' => 1,
            )
        );

        $this->user->setPassword('test');
        $this->user->save();

        $this->documentType = DocumentTypeModel::fromArray(
            array(
                'name' => 'Document Type Name',
                'description' => 'Document Type description',
                'icon_id' => 1,
                'defaultview_id' => $this->view->getId(),
                'user_id' => $this->user->getId(),
            )
        );

        $this->documentType->save();

        $this->document = DocumentModel::fromArray(
            array(
                'name' => 'Document name',
                'url_key' => 'url-key',
                'status' => DocumentModel::STATUS_ENABLE,
                'show_in_nav' => true,
                'user_id' => $this->user->getId(),
                'document_type_id' => $this->documentType->getId(),
                'view_id' => $this->view->getId(),
                'layout_id' => $this->layout->getId(),
                'parent_id' => null
            )
        );

        $this->document->save();

        $this->boostrap = new Bootstrap();
        $this->boostrap->install();
        $this->object = new IndexController(
            Registry::get('Application')->getRequest(),
            Registry::get('Application')->getResponse()
        );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->boostrap->uninstall();
        $this->document->delete();
        $this->view->delete();
        $this->layout->delete();
        $this->documentType->delete();
        $this->user->delete();
        unset($this->document);
        unset($this->object);
        unset($this->view);
        unset($this->layout);
        unset($this->documentType);
        unset($this->user);
    }

    /**
     * Test
     *
     * @return void
     */
    public function testIndexAction()
    {
        $this->assertInternalType('array', $this->object->indexAction());
    }

    /**
     * Test
     *
     * @return void
     */
    public function testDocumentCommentActionWithRedirect()
    {
        $this->setExpectedException('Zend\Mvc\Exception\DomainException');
        $this->assertInternalType('array', $this->object->documentCommentAction());
    }

    /**
     * Test
     *
     * @return void
     */
    public function testDocumentComment()
    {
        Registry::get('Application')->getRequest()->getQuery()->set('id', $this->document->getId());
        $this->assertInternalType('array', $this->object->documentCommentAction());
    }

    /**
     * Test
     *
     * @return void
     */
    public function testDocumentCommentWithPostData()
    {
        $this->setExpectedException('Zend\Mvc\Exception\DomainException');
        $request = Registry::get('Application')->getRequest();
        $request->setMethod('POST');
        $request->getQuery()->set('id', $this->document->getId());

        $post = $request->getPost();

        $post->set(
            'comment',
            array(
                '1' => array(
                    'delete' => true
                ),
                '2' => array(
                    'message' => true,
                    'wrong-name' => false
                ),
            )
        );

        $this->assertNUll($this->object->documentCommentAction());

        $request->setMethod('GET');
    }
}
