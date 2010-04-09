<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 * @since      0.9.12
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTIssetExpression.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTIssetExpression} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 * @since      0.9.12
 */
class PHP_Depend_Code_ASTIssetExpressionTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testIssetExpressionGraphWithMultipleVariables
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIssetExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIssetExpressionGraphWithMultipleVariables()
    {
        $expr = $this->_getFirstIssetExpressionInFunction(__METHOD__);
        $vars = $expr->findChildrenOfType(PHP_Depend_Code_ASTVariable::CLAZZ);
        $this->assertEquals(3, count($vars));
    }

    /**
     * testIssetExpressionGraphWithStaticProperty
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIssetExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIssetExpressionGraphWithStaticProperty()
    {
        $expr = $this->_getFirstIssetExpressionInFunction(__METHOD__);
        $vars = $expr->findChildrenOfType(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ);
        $this->assertEquals(1, count($vars));
    }

    /**
     * testIssetExpressionGraphWithArrayProperty
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIssetExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIssetExpressionGraphWithArrayProperty()
    {
        $expr = $this->_getFirstIssetExpressionInFunction(__METHOD__);
        $vars = $expr->findChildrenOfType(PHP_Depend_Code_ASTMemberPrimaryPrefix::CLAZZ);
        $this->assertEquals(1, count($vars));
    }

    /**
     * testIssetExpressionHasExpectedStartLine
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIssetExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIssetExpressionHasExpectedStartLine()
    {
        $expr = $this->_getFirstIssetExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getStartLine());
    }

    /**
     * testIssetExpressionHasExpectedStartColumn
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIssetExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIssetExpressionHasExpectedStartColumn()
    {
        $expr = $this->_getFirstIssetExpressionInFunction(__METHOD__);
        $this->assertEquals(9, $expr->getStartColumn());
    }

    /**
     * testIssetExpressionHasExpectedEndLine
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIssetExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIssetExpressionHasExpectedEndLine()
    {
        $expr = $this->_getFirstIssetExpressionInFunction(__METHOD__);
        $this->assertEquals(4, $expr->getEndLine());
    }

    /**
     * testIssetExpressionHasExpectedEndColumn
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIssetExpression
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIssetExpressionHasExpectedEndColumn()
    {
        $expr = $this->_getFirstIssetExpressionInFunction(__METHOD__);
        $this->assertEquals(30, $expr->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTIssetExpression
     */
    private function _getFirstIssetExpressionInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTIssetExpression::CLAZZ
        );
    }
}