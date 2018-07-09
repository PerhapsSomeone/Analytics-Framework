<?php
/**
 * Created by PhpStorm.
 * User: marc
 * Date: 09.07.18
 * Time: 16:29
 */

class db
{
    public static function logVisit($anonIP, $browser, $country, $countrycode, $plaftform, $ineu, $page)
    {
        $conn = self::getConn();
        echo "called";
        $stmt = $conn->prepare("INSERT INTO `visitors` (`ip`, `browser`, `platform`, `country`, `page`, `countrycode`, `ineu`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$anonIP, $browser, $browser, $country, $page, $countrycode, $ineu]);
    }

    public static function getConn()
    {
        $host = '127.0.0.1';
        $db = 'analytics';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, $user, $pass, $opt);

        return $pdo;
    }

    public static function recent50visitors()
    {
        $conn = self::getConn();

        $data = $conn->query('SELECT * FROM visitors ORDER BY date DESC LIMIT 50')->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }
}

class analytics
{

    public static function autoEnterVisitor()
    {
        $browserArr = self::getBrowser();

        $ip = self::getAnonIP($_GET["ip"]);
        $platform = $browserArr["platform"];
        $browserName = $browserArr["name"];
        $country = $_GET["country"];
        $countrycode = $_GET["countrycode"];
        $in_eu = $_GET["ineu"];
        $page = $_GET["page"];

        db::logVisit($ip, $browserName, $country, $countrycode, $platform, $in_eu, $page);
    }

    public static function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        } else {
            $ub = "Unknown";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';

        preg_match_all($pattern, $u_agent, $matches);

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
        );
    }

    public static function getAnonIP($ip)
    {
        return hash("sha512", $ip);
    }

    public static function getPageURL()
    {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
}