<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2012, Manuel Pichler <mapi@pdepend.org>.
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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

use \PHP\Depend\AST\ASTFunction;
use \PHP\Depend\AST\ASTMethod;
use \PHP\Depend\Metrics\NodeAware;
use \PHP\Depend\Metrics\ProjectAware;
use \PHP\Depend\Metrics\AbstractCachingAnalyzer;

/**
 * This class calculates the Cyclomatic Complexity Number(CCN) for the project,
 * methods and functions.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Metrics
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2012 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 *
 * @todo       2.0 Generate file, namespace and class ccn
 * @todo       2.0 Generate trait method ccn
 */
class PHP_Depend_Metrics_CyclomaticComplexity_Analyzer
    extends AbstractCachingAnalyzer
    implements NodeAware, ProjectAware
{
    /**
     * Type of this analyzer class.
     */
    const CLAZZ = __CLASS__;

    /**
     * Metrics provided by the analyzer implementation.
     */
    const M_CYCLOMATIC_COMPLEXITY_1 = 'ccn',
        M_CYCLOMATIC_COMPLEXITY_2   = 'ccn2';

    /**
     * The project Cyclomatic Complexity Number.
     *
     * @var integer
     */
    private $ccn = 0;

    /**
     * Extended Cyclomatic Complexity Number(CCN2) for the project.
     *
     * @var integer
     */
    private $ccn2 = 0;

    /**
     * Returns the cyclomatic complexity for the given <b>$node</b>.
     *
     * @param \PHP\Depend\AST\ASTNode|string $node
     *
     * @return integer
     */
    public function getCCN($node)
    {
        $metrics = $this->getNodeMetrics($node);
        if (isset($metrics[self::M_CYCLOMATIC_COMPLEXITY_1])) {

            return $metrics[self::M_CYCLOMATIC_COMPLEXITY_1];
        }
        return 0;
    }

    /**
     * Returns the extended cyclomatic complexity for the given <b>$node</b>.
     *
     * @param \PHP\Depend\AST\ASTNode|string $node
     *
     * @return integer
     */
    public function getCCN2($node)
    {
        $metrics = $this->getNodeMetrics($node);
        if (isset($metrics[self::M_CYCLOMATIC_COMPLEXITY_2])) {

            return $metrics[self::M_CYCLOMATIC_COMPLEXITY_2];
        }
        return 0;
    }

    /**
     * This method will return an <b>array</b> with all generated metric values
     * for the given node or node identifier. If there are no metrics for the
     * requested node, this method will return an empty <b>array</b>.
     *
     * <code>
     * array(
     *     'loc'    =>  42,
     *     'ncloc'  =>  17,
     *     'cc'     =>  12
     * )
     * </code>
     *
     * @param \PHP\Depend\AST\ASTNode|string $node
     *
     * @return array
     */
    public function getNodeMetrics($node)
    {
        $nodeId = (string) is_object($node) ? $node->getId() : $node;

        if (isset($this->metrics[$nodeId])) {
            return $this->metrics[$nodeId];
        }
        return array();
    }

    /**
     * Provides the project summary metrics as an <b>array</b>.
     *
     * @return array
     */
    public function getProjectMetrics()
    {
        return array(
            self::M_CYCLOMATIC_COMPLEXITY_1  => $this->ccn,
            self::M_CYCLOMATIC_COMPLEXITY_2  => $this->ccn2
        );
    }

    private $data = array();

    /**
     * Visits a function node.
     *
     * @param \PHP\Depend\AST\ASTFunction $function
     * @param array $data
     *
     * @return void
     */
    public function visitASTFunctionBefore(ASTFunction $function, $data)
    {
        $this->data[] = $data;

        return array(
            self::M_CYCLOMATIC_COMPLEXITY_1  => 1,
            self::M_CYCLOMATIC_COMPLEXITY_2  => 1
        );
    }

    /**
     * Visits a function node after it's children were traversed.
     *
     * @param \PHP\Depend\AST\ASTFunction $function
     * @param array $data
     *
     * @return array
     */
    public function visitASTFunctionAfter(ASTFunction $function, $data)
    {
        $this->metrics[$function->getId()] = $data;
        /* TODO 2.0 Fix result caching
        if (false === $this->restoreFromCache($function)) {
            $this->calculateComplexity($function);
        }
        */
        $this->updateProjectMetrics($function->getId());

        return (array)array_pop($this->data);
    }

    /**
     * Visits a method before it's children will be traversed.
     *
     * @param \PHP\Depend\AST\ASTMethod $method
     * @param array $data
     *
     * @return array
     */
    public function visitASTMethodBefore(ASTMethod $method, $data)
    {
        $this->data[] = $data;

        return array(
            self::M_CYCLOMATIC_COMPLEXITY_1  => 1,
            self::M_CYCLOMATIC_COMPLEXITY_2  => 1
        );
    }

    /**
     * Visits a method after it's children were traversed.
     *
     * @param \PHP\Depend\AST\ASTMethod $method
     * @param array $data
     *
     * @return void
     */
    public function visitASTMethodAfter(ASTMethod $method, $data)
    {
        $this->metrics[$method->getId()] = $data;
        /* TODO 2.0 Fix result caching
        if (false === $this->restoreFromCache($method)) {
            $this->calculateComplexity($method);
        }
        */
        $this->updateProjectMetrics($method->getId());

        return (array) array_pop($this->data);
    }

    /**
     * Stores the complexity of a node and updates the corresponding project
     * values.
     *
     * @param string $nodeId Identifier of the analyzed item.
     *
     * @return void
     */
    private function updateProjectMetrics($nodeId)
    {
        $this->ccn += $this->metrics[$nodeId][self::M_CYCLOMATIC_COMPLEXITY_1];
        $this->ccn2 += $this->metrics[$nodeId][self::M_CYCLOMATIC_COMPLEXITY_2];
    }

    /**
     * Visits a boolean AND-expression.
     *
     * @param PHPParser_Node_Expr_BooleanAnd $node
     * @param array                          $data
     *
     * @return array
     */
    public function visitExprBooleanAndBefore(PHPParser_Node_Expr_BooleanAnd $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];
        return $data;
    }

    /**
     * Visits a boolean OR-expression.
     *
     * @param PHPParser_Node_Expr_BooleanOr $node
     * @param array                         $data
     *
     * @return array
     */
    public function visitExprBooleanOrBefore(PHPParser_Node_Expr_BooleanOr $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];
        return $data;
    }

    /**
     * Visits a switch label.
     *
     * @param PHPParser_Node_Stmt_Case $node
     * @param array                    $data
     *
     * @return array
     */
    public function visitStmtCaseBefore(PHPParser_Node_Stmt_Case $node, $data)
    {

        if ($node->cond) {
            ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
            ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];
        }
        return $data;
    }

    /**
     * Visits a catch statement.
     *
     * @param PHPParser_Node_Stmt_Catch $node
     * @param array                     $data
     *
     * @return array
     */
    public function visitStmtCatchBefore(PHPParser_Node_Stmt_Catch $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return $data;
    }

    /**
     * Visits an elseif statement.
     *
     * @param PHPParser_Node_Stmt_ElseIf $node
     * @param array                      $data
     *
     * @return array
     */
    public function visitStmtElseIfBefore(PHPParser_Node_Stmt_ElseIf $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return $data;
    }

    /**
     * Visits a for statement.
     *
     * @param PHPParser_Node_Stmt_For $node
     * @param array                   $data
     *
     * @return array
     */
    public function visitStmtForBefore(PHPParser_Node_Stmt_For $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return $data;
    }

    /**
     * Visits a foreach statement.
     *
     * @param PHPParser_Node_Stmt_Foreach $node
     * @param array                       $data
     *
     * @return array
     */
    public function visitStmtForeachBefore(PHPParser_Node_Stmt_Foreach $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return $data;
    }

    /**
     * Visits an if statement.
     *
     * @param PHPParser_Node_Stmt_If $node
     * @param array                  $data
     *
     * @return array
     */
    public function visitStmtIfBefore(PHPParser_Node_Stmt_If $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return $data;
    }

    /**
     * Visits a logical AND expression.
     *
     * @param PHP_Depend_AST_ASTNodeI $node
     * @param array                   $data
     *
     * @return array
     */
    public function visitExprLogicalAndBefore(PHPParser_Node_Expr_LogicalAnd $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];
        return $data;
    }

    /**
     * Visits a logical OR expression.
     *
     * @param PHP_Depend_AST_ASTNodeI $node
     * @param array                   $data
     *
     * @return array
     */
    public function visitExprLogicalOrBefore(PHPParser_Node_Expr_LogicalOr $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];
        return $data;
    }

    /**
     * Visits a ternary operator.
     *
     * @param PHPParser_Node_Expr_Ternary $node
     * @param array                       $data
     *
     * @return array
     */
    public function visitExprTernaryBefore(PHPParser_Node_Expr_Ternary $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return $data;
    }

    /**
     * Visits a while-statement.
     *
     * @param PHP_Depend_AST_ASTNodeI $node
     * @param array                   $data
     *
     * @return array
     */
    public function visitStmtWhileBefore(PHPParser_Node_Stmt_While $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return $data;
    }

    /**
     * Visits a do/while-statement.
     *
     * @param PHPParser_Node_Stmt_Do $node
     * @param array                  $data
     *
     * @return array
     */
    public function visitStmtDoBefore(PHPParser_Node_Stmt_Do $node, $data)
    {
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_1];
        ++$data[self::M_CYCLOMATIC_COMPLEXITY_2];

        return $data;
    }
}
