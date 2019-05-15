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
 * MoviePanes class which 
 * extends RecursiveIteratorIterator
 * 
 * @category HTML_Element_Builder
 * @package  SMTMovieRental
 * @author   Tom Green <P460247@tafe.wa.edu.au>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://github.com/AUSSIEFIDDY/SMTMovieRental
 **/ 
class MoviePanes extends RecursiveIteratorIterator
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
     * This function inserts the movie data into the 
     * Movie Pane element
     *
     * @return MoviePane element
     */
    function current()
    {
        $title = parent::current();
        $newTitle = str_replace('#', '|', $title);
        $newTitle = str_replace('&', ']', $newTitle);
        return '<div class="moviepane"><a href="movie.php?movie=' . 
        $newTitle . '">' . $title . '</a></div>';
    }
}

/**
 * MakeQuerySafe function to avoid SQL injection
 * 
 * @param String $str The Query String to be made safe
 * 
 * @return String $str The safe Query String
 */
function makeQuerySafe($str)
{
    $str = str_replace(';', '', $str);
    $str = str_replace('"', '', $str);
    $str = str_replace('<', '', $str);
    $str = str_replace('>', '', $str);
    return $str;
}

$title = ' - SMT Movie Rental!';
$username = 'root'; //Username variable
$password = ''; //Password variable

// Echo the begginning of the html for the page

echo '<!DOCTYPE html>
<html lang="en">

<head>
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

    <div class="maincontent">';

if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $search = makeQuerySafe($search);
    $title = $search . $title;
    echo '<title>' . $title . '</title>';
    try
    {
        /**
         * $conn = PHP-Data-Object with the params
         * (database location, $username, $password)
         */
        $conn = new PDO(
            'mysql:host=localhost;dbname=movies_db',
            $username, $password
        );

        // Initialize Error handling

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the sql statement and bind $ID to :ID

        $stmt = $conn->prepare(
            'SELECT Title FROM movies WHERE Title LIKE "%' .
            $search . '%" ORDER BY length(Title), Title'
        );

        // Execute the Statement

        $stmt->execute();
        /**
         * $result = statement with fetch mode that
         * returns each row of the table as an array
         */
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $success = false;
        /**
         * Foreach array obtained by $result,
         * (in the case of QuoteOfToday, 1) echo the result
         */
        foreach (
        new MoviePanes(
            new RecursiveArrayIterator(
                $stmt->fetchAll()
            )
        ) as $k => $v) {
            if ($success == false) {
                echo '<h1>Search Results for ' . $search . '!</h1>';
                echo '<h3>Not what you were looking for? Try our 
		        <a href="search.php">Advanced Search!</a></h3>';
            }
            echo $v;
            $success = true;
        }

        if ($success == false) {
            echo '<h1>No Movies were found using that searchterm.</h1>
            <p>You are being redirected.</p>';
            header("refresh:4; url=search.php");
        }
    }

    catch(PDOException $e) //Catch PDOException
    {
        echo 'ERROR: ' . $e->getMessage();
    }

    $conn = null; //End connection
} else if (isset($_GET['category'])) {

    // Using GET

    $category_value = $_GET['category'];
    $category_value = makeQuerySafe($category_value);
    $title = $category_value . $title;
    try
    {
        /**
         * $conn = PHP-Data-Object with the params
         * (database location, $username, $password)
         */
        $conn = new PDO(
            'mysql:host=localhost;dbname=movies_db', 
            $username, $password
        );

        // Initialize Error handling

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the sql statement and bind $ID to :ID

        $stmt = $conn->prepare(
            'SELECT Title FROM movies WHERE Genre LIKE "%' .
            $category_value . '%" ORDER BY length(Title), Title'
        );

        // Execute the Statement

        $stmt->execute();
        /**
         * $result = statement with fetch mode that
         * returns each row of the table as an array
         */
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $success = false;
        /**
         * Foreach array obtained by $result,
         * (in the case of QuoteOfToday, 1) echo the result
         */
        foreach (
        new MoviePanes(
            new RecursiveArrayIterator(
                $stmt->fetchAll()
            )
        ) as $k => $v
        ) {
            if ($success == false) {
                echo '<title>' . $title . '</title>';
                echo "<h1>Our " . $category_value . " Movies!</h1>";
            }
            $success = true;
            echo $v;
        }

        if ($success == false) {
            echo '<h1>No Movies were found using that searchterm.</h1>
            <p>You are being redirected.</p>';
            header("refresh:4; url=search.php");
        }
    }

    catch(PDOException $e) //Catch PDOException
    {
        echo 'ERROR: ' . $e->getMessage();
    }

    $conn = null; //End connection
} else if (isset($_POST['title']) && isset($_POST['studio'])) {
    $mTitle = $_POST['title'];
    $mStudio = $_POST['studio'];
    $mYear = $_POST['year'];
    $mRating = null;
    $mGenre = null;
    $mTitle = makeQuerySafe($mTitle);
    $mStudio = makeQuerySafe($mStudio);
    $mYear = makeQuerySafe($mYear);
    if (isset($_POST['rating'])) {
        $mRating = $_POST['rating'];
    }

    if (isset($_POST['genre'])) {
        $mGenre = $_POST['genre'];
    }

    $titleSearch = true;
    $studioSearch = true;
    $yearSearch = true;
    $ratingSearch = true;
    $genreSearch = true;
    if ($mTitle == null) {
        $titleSearch = false;
    }

    if ($mStudio == null) {
        $studioSearch = false;
    }

    if ($mYear == null) {
        $yearSearch = false;
    }

    if ($mRating == null) {
        $ratingSearch = false;
    }

    if ($mGenre == null) {
        $genreSearch = false;
    }

    $statement = 'SELECT Title FROM movies WHERE ';
    $searchParameters = '';
    if ($titleSearch == true) {
        $statement = $statement . 'Title LIKE "%' . $mTitle . '%"';
        $searchParameters = $searchParameters . 'Title: ' . $mTitle . ' | ';
        if ($studioSearch == true 
            || $yearSearch == true 
            || $ratingSearch == true 
            || $genreSearch == true
        ) {
            $statement = $statement . ' && ';
        }
    }

    if ($studioSearch == true) {
        $statement = $statement . 'Studio LIKE "%' . $mStudio . '%"';
        $searchParameters = $searchParameters . 'Studio: ' . $mStudio . ' | ';
        if ($yearSearch == true || $ratingSearch == true || $genreSearch == true) {
            $statement = $statement . ' && ';
        }
    }

    if ($yearSearch == true) {
        $statement = $statement . 'Year = "' . $mYear . '"';
        $searchParameters = $searchParameters . 'Year: ' . $mYear . ' | ';
        if ($ratingSearch == true || $genreSearch == true) {
            $statement = $statement . ' && ';
        }
    }

    if ($ratingSearch == true) {
        $statement = $statement . 'Rating = "' . $mRating . '"';
        $searchParameters = $searchParameters . 'Rating: ' . $mRating . ' | ';
        if ($genreSearch == true) {
            $statement = $statement . ' && ';
        }
    }

    if ($genreSearch == true) {
        $statement = $statement . 'Genre LIKE "%' . $mGenre . '%"';
        $searchParameters = $searchParameters . 'Genre: ' . $mGenre . ' | ';
    }
    
    if ($titleSearch == false 
        && $studioSearch == false 
        && $yearSearch == false 
        && $ratingSearch == false 
        && $genreSearch == false
    ) {
        echo '<h1>You must input data into a field on the Advanced Search!</h1>
        <h2>Please try again.</h2>
        <p>You are being redirected.</p>';
        header("refresh:4; url=search.php");
        return;
    }

    $statement = $statement . ';';
    echo '<h1>Search Results:</h1>';
    echo '<p>Search Parameters:</p>';
    echo '<p>' . $searchParameters . '</p><br />';
    try
    {
        /**
         * $conn = PHP-Data-Object with the params
         * (database location, $username, $password)
         */
        $conn = new PDO(
            'mysql:host=localhost;dbname=movies_db', 
            $username, $password
        );

        // Initialize Error handling

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the sql statement and bind $ID to :ID

        $stmt = $conn->prepare($statement);

        // Execute the Statement

        $stmt->execute();
        /**
         * $result = statement with fetch mode that
         * returns each row of the table as an array
         */
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        /**
         * Foreach array obtained by $result,
         * (in the case of QuoteOfToday, 1) echo the result
         */
        foreach (
        new MoviePanes(
            new RecursiveArrayIterator(
                $stmt->fetchAll()
            )
        ) as $k => $v
        ) {
            echo $v;
        }
    }

    catch(PDOException $e) //Catch PDOException
    {
        echo 'ERROR: ' . $e->getMessage();
    }

    $conn = null; //End connection
} else {
    $title = 'Movies ' . $title;
    echo '<title>' . $title . '</title>';
    echo '<h1>Our Advanced Movie Search</h1>';
    echo '<form action="search.php" method="POST">
        Title: <input type="text" name="title">
        <br />
        Studio: <input type="text" name="studio">
        <br />
        Year: <input type="text" name="year">
        <br />
        Rating: <select name="rating">
            <option value="" selected disabled hidden>Choose a Rating</option>
            <option value="G">G</option>
            <option value="PG">PG</option>
            <option value="PG-13">PG-13</option>
            <option value="NC-17">NC-17</option>
            <option value="R">R</option>
            <option value="NR">NR</option>
            <option value="VAR">VAR</option>
            <option value="UNK">UNK</option>
            <option value="UR">UR</option>
            <option value="R/NR">R/NR</option>
            <option value="UR/R">UR/R</option>
            <option value="R/UR">R/UR</option>
        </select>
        <br />
        Genre: <select name="genre">
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
        <br />
        <input type="submit" value="Submit">
    </form>
    <br>
    <a href="search.php?category">Or View All Movies!</a>';
}

echo '</div>

<div class="footer">
    <a class="footer" href="mailto:P460247@tafe.wa.edu.au">Tom Green</a>
</div>
</body>

</html>'
?>
