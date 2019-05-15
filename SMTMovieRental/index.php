<?php
/**
 * Homepage for SMTMovieRental
 * 
 * PHP version 7
 * 
 * LICENSE: MIT License
 *
 * Copyright (c) 2019 AUSSIEFIDDY
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * @category    Homepage
 * @package     SMTMovieRental
 * @author      Tom Green <P460247@tafe.wa.edu.au>
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     SVN: 7
 * @link        https://github.com/AUSSIEFIDDY/SMTMovieRental
 */

/**
 * ViewsPane class which 
 * extends RecursiveIteratorIterator
 * 
 * @category HTML_Element_Builder
 * @package  SMTMovieRental
 * @author   Tom Green <P460247@tafe.wa.edu.au>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/AUSSIEFIDDY/SMTMovieRental
 **/ 
class ViewsPane extends RecursiveIteratorIterator
{
    /**
     * Functions to create ViewsPane elements
     *
     * @param data $it Is the data to be displayed
     */
    function __construct($it)
    {
        parent::__construct($it, self::LEAVES_ONLY);
    }

    /**
     * This function inserts the movie title into the 
     * ViewsPane element
     *
     * @return $current
     */
    function current()
    {
        $movie = parent::current();
        $newMovie = str_replace('#', '|', $movie);
        $newMovie = str_replace('&', ']', $newMovie);
        $current = "";
        if (preg_match('/[^0-9]/', parent::current())) {
            $current = "<td><a href=\"movie.php?movie=".
                $newMovie."\">" . $movie . "</a></td>";
        } else {
            $current = "<td>".parent::current()."</td>";
        }
        return $current;
    }

    /**
     * This function begins the row
     *
     * @return echo <tr>
     */
    function beginChildren()
    {
        echo "<tr>";
    }

    /**
     * This function ends the row
     *
     * @return echo </tr>\n
     */
    function endChildren()
    {
        echo "</tr>" . "\n";
    }
}

/**
 * MovieChart class which 
 * extends RecursiveIteratorIterator
 * 
 * @category GD_Graph_Builder
 * @package  SMTMovieRental
 * @author   Tom Green <P460247@tafe.wa.edu.au>
 * @license  https://www.google.com/search?q=Unlicensed Unlicensed
 * @link     https://www.southmetrotafe.wa.edu.au/
 **/ 
class MovieChart extends RecursiveIteratorIterator
{
    //Create arrays to store the movie data
    var $titles = array();
    var $views = array();
    var $combined = array();
    var $i = 0;
    var $it;

    /**
     * Constructor
     *
     * @param data $it Is the data to be displayed
     */
    function __construct($it)
    {
        $this->it = $it;
        parent::__construct($it, self::LEAVES_ONLY);
    }

    /**
     * This function inserts Movie Data into arrays and then uses the data
     * to create a GD chart
     *
     * @return <td>parent::current</td>
     */
    function current()
    {
        $current = parent::current();
        if ($this->i % 2 === 0) {
            $this->i++;
            array_push($this->titles, $current);
        } else {
            array_push($this->views, $current);
            if ($this->i == 19) {
                $this->combined = array_combine($this->titles, $this->views);
                $reportWidth = 1400;//width of the report image
                $reportHeight = 500;//height of the report image

                //graph dimensions
                $graphTop = 60;
                $graphLeft = 20;
                $graphBottom = 440;
                $graphRight = 1300;
                $graphHeight = $graphBottom - $graphTop;
                $graphWidth = $graphRight - $graphLeft;

                //dimensions of the graph elements
                $lineWidth = 1;
                $barWidth = 20;

                //title string and dimens
                $title = "Top Views Chart";
                $titleFontSize = 20;
                $titleY = $graphTop - 20;
                $titleX = $graphWidth / 2 - 75;

                //path to font
                $font = getcwd().'\Font\Aller_Rg.ttf';
                $fontSize = 8;//default font size

                $labelMargin = 8;//label margin

                $yMaxValue = 30;//maximum size of the y axis

                $yLabelSpan = 60;//label span

                //create the report image
                $report = imagecreate($reportWidth, $reportHeight);

                //set background colour to white
                $backgroundColor = imagecolorallocate($report, 204, 197, 185);
                //set the axis colour to dark grey
                $axisColor = imagecolorallocate(
                    $report, 85, 85, 85
                );
                //set label colour to the axis colour
                $labelColor = $axisColor;
                //set the graph colour to light grey
                $graphColor = imagecolorallocate(
                    $report, 212, 212, 212
                );
                //set the bars to dark purple
                $barColor = imagecolorallocate(
                    $report, 128, 0, 128
                );
                //fill the report with the background colour
                imagefill(
                    $report, 0, 0, $backgroundColor
                );
                //set the line thickness
                imagesetthickness($report, $lineWidth);

                //place the title at the top of the report
                imagettftext(
                    $report, $titleFontSize, 0, $titleX, 
                    $titleY, $labelColor, $font, $title
                );

                //loop to generate the y axis of the graph
                for ($i=0; $i <= $yMaxValue; $i += $yLabelSpan) {
                    $y = $graphHeight / $yMaxValue + 50;

                    imageline($report, $graphLeft, $y, $graphRight, $y, $graphColor);

                    $labelBox = imagettfbbox($fontSize, 0, $font, strval($i));
                    $labelWidth = $labelBox[4] - $labelBox[0];
                    $labelX = $graphLeft - $labelWidth - $labelMargin + 20;
                    $labelY = $y + $fontSize / 2 + 10;
                    //y axis label 'Views'
                    imagettftext(
                        $report, $fontSize, 0, $labelX, 
                        $labelY, $labelColor, $font, "Views"
                    );
                }

                //create the line boundries of the graph
                imageline(
                    $report, $graphLeft, $graphTop, 
                    $graphLeft, $graphBottom, $axisColor
                );
                imageline(
                    $report, $graphLeft, $graphBottom, 
                    $graphRight, $graphBottom, $axisColor
                );

                //set the bar spacing to the graphwidth / combined count
                $barSpacing = $graphWidth / count($this->combined);
                //bar x axis
                $barX = $graphLeft + $barSpacing / 2;
                //foreach loop to add the bars to the graph
                foreach ($this->combined as $key => $value) {
                    //create the bar
                    $x1 = $barX - $barWidth / 2;
                    $y1 = $graphBottom - $value / $yMaxValue * $graphHeight;
                    $x2 = $barX + $barWidth / 2;
                    $y2 = $graphBottom - 1;
                    imagefilledrectangle($report, $x1, $y1, $x2, $y2, $barColor);
                    //create the label for the bar
                    $labelBox = imagettfbbox($fontSize, 0, $font, $key);
                    $labelWidth = $labelBox[4] - $labelBox[0];
                    $labelX = $barX - $labelWidth / 2;
                    $labelY = $graphBottom + $labelMargin + $fontSize;
                    //label displaying the number that occurred
                    imagettftext(
                        $report, $fontSize, 0, $labelX, 
                        $labelY, $labelColor, $font, $key
                    );
                    //line to seperate the number and occurence
                    imageline(
                        $report, $graphLeft, $labelY + 2, 
                        $graphRight + 90, $labelY + 2, $axisColor
                    );
                    //label displaying the number of occurrences
                    imagettftext(
                        $report, $fontSize, 0, $labelX, 
                        $labelY + 15, $labelColor, $font, $value
                    );
                    //next bar x axis
                    $barX += $barSpacing;
                }

                //create the x axis labels
                imagettftext(
                    $report, $fontSize, 0, $labelX + 120, 
                    $labelY, $labelColor, $font, "Title"
                );
                imagettftext(
                    $report, $fontSize, 0, $labelX + 120, 
                    $labelY + 15, $labelColor, $font, "Views"
                );

                //output the views_graph png image
                imagepng(
                    $report, "IMG/Generated/views_graph.png"
                );
                echo '<img src="IMG/Generated/views_graph.png"
                alt="Views">';
            } else {
                $this->i++;
            }
        }
    }
}

//echo the beginning of the page
echo '<!DOCTYPE html>
<html lang="en">
    <head>
        <title>SMT Movie Rental!</title>
        <link rel="stylesheet" type="text/css" href="CSS/index.css">
        <script type="text/javascript" src="JS/Rotator.js"></script> 
        <meta charset="UTF-8">
    </head>

    <body>
        <div class="headingbox">
            <img id="logo" src="IMG/logo_0.png" alt="Logo">
            <script>rotateHTMLElement("logo", 40);</script>
            <h1 class="title">SMT MOVIE RENTAL!</h1>
            <h2>The Best Movie Rental Site Ever!</h2>
        </div>

        <div class="menubar">
            <a class="active" href="index.php">Home</a>
            <a href="search.php">Movies</a>
            <a>Categories: 
                <select name="category" 
                onchange="location = \'search.php?category=\' + this.value">
                    <option value="" 
                    selected disabled hidden>Choose a Category!</option>
                    <option value="Action">Action</option>
                    <option value="Adventure">Adventure</option>
                    <option value="Animation">Animation</option>
                    <option value="Anime">Anime</option>
                    <option value="Ballet">Ballet</option>
                    <option value="Comedy">Comedy</option>
                    <option value="Dance">Dance</option>
                    <option value="Documentary">Documentary</option>
                    <option value="Drama">Drama</option>
                    <option value="Family">Family</option>
                    <option value="Fantasy">Fantasy</option>
                    <option value="Foreign">Foreign</option>
                    <option value="Horror">Horror</option>
                    <option value="Late Night">Late Night</option>
                    <option value="Music">Music</option>
                    <option value="Musical">Musical</option>
                    <option value="Mystery">Mystery</option>
                    <option value="Opera">Opera</option>
                    <option value="Other">Other</option>
                    <option value="Satire">Satire</option>
                    <option value="SciFi">SciFi</option>
                    <option value="Special Interest">Special Interest</option>
                    <option value="Thriller">Thriller</option>
                </select>
            </a>
            <a href="contact.htm">Contact</a>
            <div class="search-container">
                <form action="search.php" method="POST">
                    <input type="text" placeholder="Search Our Titles..."
                        name="search" required>
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>

        <hr>

        <div class="maincontent">
            <h1>Welcome to SMT Movie Rental!</h1>
            <h2>We have the largest range of Movies on the internet!</h2>
            <h3>Our Top 10 Most Viewed Movies!:</h3>';
            
if (isset($_GET['chart']) && $_GET['chart'] == 'true') {
    echo '<a href="index.php">View As Table</a><br>';
    try {
        $username = 'root';//Username variable
        $password = '';//Password variable
    
        /**
         * $conn = PHP-Data-Object with the params 
         * (database location, $username, $password)
         *  */ 
        $conn = new PDO(
            'mysql:host=localhost;dbname=movies_db',
            $username, $password
        );
    
        // Initialize Error handling
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        $stmt = $conn->prepare(
            'SELECT Title, ViewNum FROM movies ORDER BY ViewNum DESC LIMIT 10'
        );
    
        // Execute the Statement
        $stmt->execute();
    
        /**
         * $result = statement with fetch mode that 
         * returns each row of the table as an array
         *  */ 
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

        /**
         * Foreach array obtained by $result, 
         * echo the chart when the function current
         *  */     
        foreach (
            new MovieChart(
                new RecursiveArrayIterator(
                    $stmt->fetchAll()
                )
            ) 
        as $k => $v) {
            echo $v;
        }
    }
    catch(PDOException $e) //Catch PDOException
    {
        echo $e->getMessage();
    }
    
    $conn = null; //End connection

    echo '</table></div>';
} else {
    echo '<a href="index.php?chart=true">View As Chart</a><br>';
    try {
        $username = 'root';//Username variable
        $password = '';//Password variable
    
        /**
         * $conn = PHP-Data-Object with the params 
         * (database location, $username, $password)
         *  */ 
        $conn = new PDO(
            'mysql:host=localhost;dbname=movies_db',
            $username, $password
        );
    
        // Initialize Error handling
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        $stmt = $conn->prepare(
            'SELECT Title, ViewNum FROM movies ORDER BY ViewNum DESC LIMIT 10'
        );
    
        // Execute the Statement
        $stmt->execute();
    
        /**
         * $result = statement with fetch mode that 
         * returns each row of the table as an array
         *  */ 
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
        echo '<table>
            <tr><th>Title</th>
            <th>Views</th></tr>';

        /**
         * Foreach array obtained by $result, 
         * (in the case of QuoteOfToday, 1) echo the result
         *  */ 
        foreach (
            new ViewsPane(
                new RecursiveArrayIterator(
                    $stmt->fetchAll()
                )
            ) 
        as $k => $v) {
            echo $v;
        }
    }
    catch(PDOException $e) //Catch PDOException
    {
        echo $e->getMessage();
    }
    
    $conn = null; //End connection

    echo '</table></div>';
}

echo '<div class="footer">
    <a class="footer" href="mailto:P460247@tafe.wa.edu.au">Tom Green</a>
</div>
</body>

</html>';
?>
