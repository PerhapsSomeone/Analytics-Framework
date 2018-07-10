<?php
/**
 * Created by PhpStorm.
 * User: marc
 * Date: 09.07.18
 * Time: 16:29
 */

class config
{

    public static function setupSentry()
    {

        require "Raven/Autoloader.php";

        Raven_Autoloader::register();

        $key = self::getSentryKey();

        $sentryClient = new Raven_Client($key);

        $error_handler = new Raven_ErrorHandler($sentryClient);

        $error_handler->registerExceptionHandler();
        $error_handler->registerErrorHandler();
        $error_handler->registerShutdownFunction();
    }

    private static function getSentryKey()
    {
        $config = file_get_contents("config.json");
        $json = json_decode($config, true);
        return $json["sentry_key"];
    }
}

class db
{
    /*
    This function inserts the data of the visitor in the DB.
    It requires all data. Use the analytics::autoEnterVisitor() function if you are unsure how it works.
    */
    public static function logVisit($anonIP, $browser, $country, $countrycode, $platform, $ineu, $page)
    {
        $conn = self::getConn();
        echo "called";
        $stmt = $conn->prepare("INSERT INTO `visitors` (`ip`, `browser`, `platform`, `country`, `page`, `countrycode`, `ineu`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$anonIP, $browser, $platform, $country, $page, $countrycode, $ineu]);
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

    /*
    Fetch an associative array of the 50 most recent DB entries.
    */
    public static function recent50visitors()
    {
        $conn = self::getConn();

        $data = $conn->query('SELECT * FROM visitors ORDER BY date DESC LIMIT 50')->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }
}

class analytics
{
    /*
    Fully automatic way to store visitor data. No arguments needed.
    */
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

    /*
    Return an assocative array of user platform and browser info based on the User Agent. 
    */
    public static function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';

        //First get the platform
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent
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

        // see how many matches there are
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

        // Return associative array of browser data.
        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
        );
    }

    /*
    GDPR forbids it to directly store IPs. They are hashed in SHA512.
    */
    public static function getAnonIP($ip)
    {
        return hash("sha512", $ip);
    }
}
