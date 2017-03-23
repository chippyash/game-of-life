<?php
/**
 * Conway's Game of Life
 * An exercise in Matrix programming
 *
 * @author    Ashley Kitson
 * @copyright Ashley Kitson, 017, UK
 * @license   GPL-V3.0, See License.md
 */
namespace Chippyash\Gol;


use Chippyash\Logic\Matrix\LogicalMatrix;
use Chippyash\Logic\Matrix\Operation\AndMatrix;
use Chippyash\Logic\Matrix\Operation\OrMatrix;
use Chippyash\Matrix\Formatter\Ascii;
use Chippyash\Matrix\Matrix;
use Chippyash\Matrix\Transformation\Resize;
use Chippyash\Matrix\Transformation\Rotate;
use Chippyash\Matrix\Transformation\Rowreduce;
use Chippyash\Matrix\Transformation\Shift;
use Chippyash\Matrix\Transformation\Transpose;
use Chippyash\Type\Number\IntType;

class GameGrid
{
    /**
     * @var LogicalMatrix
     */
    protected $grid;

    /**
     * Size of grid
     *
     * @var int
     */
    protected $size;

    public function __construct(IntType $size)
    {
        $this->size = $size();

        //we create a grid with a one vector boundary as this makes computations easier
        $this->grid = new LogicalMatrix(array_fill(0, $this->size + 2, array_fill(0, $this->size + 2, false)));
    }

    /**
     * Initialize a shape into the grid
     *
     * @param LogicalMatrix $vector    Vector matrix of shape to initialise with
     * @param IntType|null  $offsetRow Number of rows to shift the initialisation vector down
     * @param IntType|null  $offsetCol Number of columns to shift the initialisation vector right
     *
     * @return GameGrid
     */
    public function initialize(LogicalMatrix $vector, IntType $offsetRow = null, IntType $offsetCol = null)
    {
        $offsetRow = (is_null($offsetRow) ? 0 : $offsetRow()) + 1;
        $offsetCol = (is_null($offsetCol) ? 0 : $offsetCol()) + 1;

        return $this->mapOntoGrid($vector, $offsetRow, $offsetCol);
    }

    /**
     * Take a game step
     *
     * X1 = (X & (N == 2)) | (N == 3);
     * where
     *  X = current state
     *  X1 = next state
     *  N = census
     *
     * @return $this
     */
    public function step()
    {
        //get census of alive neighbours
        $census = $this->census();
        //(N == 2)
        $n2 = $this->filter($census, 2);
        //(N == 3)
        $n3 = $this->filter($census, 3);

        $lAnd       = new AndMatrix();
        $lOr        = new OrMatrix();
        $this->grid = $lOr->operate($lAnd->operate($this->grid, $n2), $n3);

//        $this->debugMat($this->grid);

        return $this;
    }

    /**
     * @return LogicalMatrix
     */
    public function getGrid()
    {
        return $this->contract($this->grid);
    }

    /**
     * Filter census for required values
     *
     * @param array $census
     * @param int   $value
     *
     * @return LogicalMatrix
     */
    protected function filter(array $census, $value)
    {
        $filtered = array_map(function ($row) use ($value)
        {
            return array_map(function ($item) use ($value)
            {
                return ($item == $value);
            },
                $row
            );
        },
            $census
        );

        return $this->expand(new LogicalMatrix($filtered));
    }

    /**
     * Expand a matrix by one all round
     *
     * @param LogicalMatrix $mA
     *
     * @return LogicalMatrix
     */
    protected function expand(LogicalMatrix $mA)
    {
        $fRes = new Resize();
        $fRot = new Rotate();

        return
            $fRot(
                $fRes(
                    $fRot(
                        $fRes($mA, []),
                        Rotate::ROT_180), []),
                Rotate::ROT_180
            );
    }

    /**
     * Contract the matrix by one all round its sides
     *
     * @param LogicalMatrix $mA
     *
     * @return float|int
     */
    protected function contract(LogicalMatrix $mA)
    {
        $fRed = new Rowreduce();
        $fRot = new Rotate();

        return
            $fRot(
                $fRed(
                    $fRot(
                        $fRed(
                            $fRot(
                                $fRed(
                                    $fRot(
                                        $fRed($mA, [$this->size + 1, 1])
                                    ),
                                    [$this->size + 1, 1]
                                )
                            ), [$this->size, 1])
                    ), [$this->size, 1])
            );
    }

    /**
     * Gather the population census from the current grid
     *
     * @return array
     */
    protected function census()
    {
        //We know we have a square grid so can lay our bounds within it
        $p      = range(1, $this->size);
        $data   = $this->grid->toArray();
        $census = [];
        foreach ($p as $r)
        {
            foreach ($p as $c)
            {
                $census[$r - 1][$c - 1] =
                    $data[$r - 1][$c - 1]
                    + $data[$r][$c - 1]
                    + $data[$r + 1][$c - 1]
                    + $data[$r - 1][$c]
                    + $data[$r - 1][$c + 1]
                    + $data[$r][$c + 1]
                    + $data[$r + 1][$c + 1]
                    + $data[$r + 1][$c];
            }
        }

        return $census;
    }

    /**
     * Map an initial state on to the grid, taking into account size of glyph
     * and any movement from origin required
     *
     * @param LogicalMatrix $matrix
     * @param               $offsetRow
     * @param               $offsetCol
     *
     * @return $this
     */
    protected function mapOntoGrid(LogicalMatrix $matrix, $offsetRow, $offsetCol)
    {
        $resizeRow  = $this->grid->rows() - $matrix->rows();
        $resizeCols = $this->grid->columns() - $matrix->columns();

        $resized = $matrix('Resize', ['rows' => $resizeRow, 'cols' => $resizeCols, 'defaultValue' => false]);

        if (($offsetCol + $offsetRow) !== 0)
        {
            $fS = new Shift();
            if ($offsetCol != 0)
            {
                $resized = $fS($resized, [$offsetCol, false]);
            }
            if ($offsetRow != 0)
            {
                $fT      = new Transpose();
                $resized = $fT($fS($fT($resized), [$offsetRow, false]));
            }
        }

        $g          = $this->grid;
        $this->grid = $g('OrMatrix', $resized);

//        $this->debugMat($this->grid);

        return $this;
    }

    /**
     * Display an ascii matrix from the supplied array
     * Use this to display state whilst performing tests etc
     *
     * @param array $a
     * @codeCoverageIgnore
     */
    private function debugArr(array $a)
    {
        foreach ($a as &$r)
        {
            $r = array_values($r);
        }
        $this->debugMat(new Matrix($a));
    }

    /**
     * Display a matrix from the supplied matrix
     * Use this to display state whilst performing tests etc
     *
     * @param Matrix $mA
     * @codeCoverageIgnore
     */
    private function debugMat(Matrix $mA)
    {
        echo $mA->setFormatter(new Ascii())->display();
    }
}