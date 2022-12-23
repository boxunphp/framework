<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\View;

use Fw\View\View;
use PHPUnit\Framework\TestCase;

/**
 * @group View
 */
class ViewTest extends TestCase
{
    /**
     * @var View
     */
    protected $view;

    protected function setUp(): void
    {
        $this->view = View::getInstance();
        $this->view->setRootPath(__DIR__ . '/views');
    }

    public function testRender()
    {
        $tpl = 'Default';

        $this->view->assign('name', 'ABC');
        $html = $this->view->fetch($tpl);

        $this->assertEquals('I\'m ABC', $html);
    }

    public function testTemplate()
    {
        $this->assertEquals(__DIR__ . '/views/Common/Header.phtml', $this->view->template('Common/Header'));
    }

    /**
     * @group View/escape
     */
    public function testEscape()
    {
        $cases = [
            ['I &#039;m Fw', 'I \'m Fw'],
            ['&lt;i&gt;HTML&lt;/i&gt;', '<i>HTML</i>'],
        ];
        foreach($cases as $case) {
            $expected = $case[0];
            $actual = $case[1];
            self::assertEquals($expected, $this->view->escape($actual));
        }
        self::assertEquals('null', $this->view->escape($null, 'null'));
    }
}
