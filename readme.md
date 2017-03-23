# Conway's Game of Life
## Proof of concept using Logical Matrices

The GOL is not a particularly useful thing apart from being pretty and
enabling us to waste countless hours.  John Conway has said as much.  But
it has nevertheless remained intriguing to many.

This is a proof of concept using my own Logical Matrix library
to prove that it can be done, and by way of an example of how to use
that library.

To see things in action you need to run the unit test and then uncomment
the lines in src\Gol\GameGrid that output the matrix display.

Run `composer install` to bring in dependencies.

The basic matrix algorithm I got from [Mathworks](http://blogs.mathworks.com/cleve/2012/09/10/game-of-life-part-2-sparse-matrices/)
and simply translated it into LogicalMatrix syntax. You can find the core of
the program in the step() method.

The rest of it is really about loading the game grid with something
to start with.

Have a play, have fun.

references: [Wikpedia](https://en.wikipedia.org/wiki/Conway's_Game_of_Life)

Also, search for "conway's game of life"in google search, it plays before your eyes !