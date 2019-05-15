<?php
/**
 * Movie Script that displays a movie from
 * the Database.
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
 * @category Display_Script
 * @package  SMTMovieRental
 * @author   Tom Green <P460247@tafe.wa.edu.au>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  SVN: 7
 * @link     https://github.com/AUSSIEFIDDY/SMTMovieRental
 */

/**
 * MovieData class which 
 * extends RecursiveIteratorIterator
 * 
 * @category HTML_Table_Builder
 * @package  SMTMovieRental
 * @author   Tom Green <P460247@tafe.wa.edu.au>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/AUSSIEFIDDY/SMTMovieRental
 **/ 
class MovieData extends RecursiveIteratorIterator
{
    /**
     * Functions to create the table structure
     *
     * @param data $it Is the data to be displayed
     */
    function __construct($it)
    {
        parent::__construct($it, self::LEAVES_ONLY);
    }

    /**
     * This function inserts the movie data into the table
     *
     * @return <td>parent::current</td>
     */
    function current()
    {
        return "<td>" . parent::current() . "</td>";
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

//Using GET
$movie_value = $_GET['movie'];
$movie_value = str_replace('|', '#', $movie_value);
$movie_value = str_replace(']', '&', $movie_value);
$movie_value = str_replace(';', '', $movie_value);

//Echo the begginning of the html for the page
echo '<!DOCTYPE html>
<html lang="en">

<head>
    <title>'.$movie_value.' - SMT Movie Rental!</title>
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
        <a href="index.php">Home</a>
        <a class="active" href="search.php">Movies</a>
        <a>Categories: 
        <select name="category" 
            onchange="location = \'search.php?category=\' + this.value">
            <option value="" selected disabled hidden>Choose a Category!</option>
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
        <a href="contact.htm">Contact</a></li>
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
        <div class="datapane">
        <h1>'.$movie_value.'</h1>';
        
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

    // Prepare the sql statement and bind $ID to :ID
    $stmt = $conn->prepare(
        'SELECT Title, Studio, Status, Sound, Versions, RecRetPrice, 
        Rating, Year, Genre, Aspect FROM movies WHERE Title = "'.
        $movie_value.'"'
    );

    // Execute the Statement
    $stmt->execute();

    /**
     * $result = statement with fetch mode that 
     * returns each row of the table as an array
     *  */ 
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

    $success = false;

    /**
     * Foreach array obtained by $result, 
     * (in the case of QuoteOfToday, 1) echo the result
     *  */ 
    foreach (new MovieData(new RecursiveArrayIterator($stmt->fetchAll())) 
    as $k => $v) {
        if ($success == false) {
            $success = true;
            echo '<table>
            <tr><th>Title</th>
            <th>Studio</th>
            <th>Status</th>
            <th>Sound</th>
            <th>Versions</th>
            <th>Retail Price</th>
            <th>Rating</th>
            <th>Year</th>
            <th>Genre</th>
            <th>Aspect</th></tr>';
        }
        echo $v;
    }

    if ($success == false) {
        echo $movie_value.' was not found.'; 
    }

    $stmt = $conn->prepare(
        'UPDATE movies SET ViewNum = ViewNum + 1 WHERE Title = "'.$movie_value.'"'
    );

    $stmt->execute();
}
catch(PDOException $e) //Catch PDOException
{
    echo 'Invalid Category';
}

$conn = null; //End connection
echo '</table></div></div>

<div class="footer">
    <a class="footer" href="mailto:P460247@tafe.wa.edu.au">Tom Green</a>
</div>
</body>

</html>'
?>
