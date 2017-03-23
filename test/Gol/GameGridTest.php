<?php
/**
 * Conway's Game of Life
 * An exercise in Matrix programming
 *
 * @author    Ashley Kitson
 * @copyright Ashley Kitson, 2017, UK
 * @license   GPL-V3.0, See License.md
 */

namespace Chippyash\Test\Gol;


use Chippyash\Gol\GameGrid;
use Chippyash\Logic\Matrix\LogicalMatrix;
use Chippyash\Type\Number\IntType;


class GameGridTest extends \PHPUnit_Framework_TestCase
{
    const INITIAL_GRID_SIZE = 7;

    /**
     * @var GameGrid
     */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new GameGrid(new IntType(self::INITIAL_GRID_SIZE));
    }

    public function testConstructionCreatesAGameGridOfTheCorrectSize()
    {
        $test = $this->sut->getGrid();
        $this->assertEquals(self::INITIAL_GRID_SIZE, $test->columns());
        $this->assertEquals(self::INITIAL_GRID_SIZE, $test->rows());
    }

    public function testInitializingWithSameSizeMatrixWillCreateInitialMatrix()
    {
        $initial = new LogicalMatrix([
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 1, 1, 1, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0]
        ]);
        $test = $this->sut->initialize($initial, new IntType(0), new IntType(0))->getGrid();

        $this->assertEquals($initial->toArray(), $test->toArray());
    }

    public function testInitializingWithTwoSameSizeMatrixWillCreateInitialMatrix()
    {
        $initial1 = new LogicalMatrix([
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 1, 1, 1, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0]
        ]);
        $initial2 = new LogicalMatrix([
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 1, 0, 0, 0],
            [0, 0, 0, 1, 0, 0, 0],
            [0, 0, 0, 1, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0]
        ]);
        $expected = new LogicalMatrix([
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 1, 0, 0, 0],
            [0, 0, 1, 1, 1, 0, 0],
            [0, 0, 0, 1, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0]
        ]);
        $test = $this->sut
            ->initialize($initial1, new IntType(0), new IntType(0))
            ->initialize($initial2, new IntType(0), new IntType(0))
            ->getGrid();


        $this->assertEquals($expected->toArray(), $test->toArray());
    }

    public function testYouCanShiftAnInitializationVectorIntoTheGrid()
    {
        $initial = new LogicalMatrix([
            [1, 1, 1],
        ]);
        $test = $this->sut->initialize($initial, new IntType(3), new IntType(2))->getGrid();
        $expected = new LogicalMatrix([
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 1, 1, 1, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0]
        ]);
        $this->assertEquals($expected->toArray(), $test->toArray());
    }

    public function testAGliderWillMove()
    {
        $initial = new LogicalMatrix([
            [0, 1, 0],
            [0, 0, 1],
            [1, 1, 1]
        ]);
        $test = $this->sut->initialize($initial, new IntType(2), new IntType(2))->getGrid();
        $initialExpected = new LogicalMatrix([
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 1, 0, 0, 0],
            [0, 0, 0, 0, 1, 0, 0],
            [0, 0, 1, 1, 1, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0]
        ]);
        $this->assertEquals($initialExpected->toArray(), $test->toArray());
        $expectedStep1 = new LogicalMatrix([
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 1, 0, 1, 0, 0],
            [0, 0, 0, 1, 1, 0, 0],
            [0, 0, 0, 1, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0]
        ]);
        $test = $this->sut->step()->getGrid();
        $this->assertEquals($expectedStep1->toArray(), $test->toArray());

        $expectedStep2 = new LogicalMatrix([
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 1, 0, 0],
            [0, 0, 1, 0, 1, 0, 0],
            [0, 0, 0, 1, 1, 0, 0],
            [0, 0, 0, 0, 0, 0, 0]
        ]);
        $test = $this->sut->step()->getGrid();
//        echo $test->setFormatter(new Ascii())->display();
        $this->assertEquals($expectedStep2->toArray(), $test->toArray());
    }
}
