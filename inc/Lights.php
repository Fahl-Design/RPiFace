<?php

/**
 * Class lights
 */
class lights
{
    /**
     * Database settings
     *
     * @var array
     */
    private static $dbConfig = [
        'DB_HOST' => 'localhost',
        'DB_USER' => 'root',
        'DB_PWD' => 'root',
        'DB_NAME' => 'pihome',
        'DB_PREFIX' => 'pi_',
    ];

    /**
     * Data attributes
     *
     * @var array
     */
    private $data = [
        'pdo' => null,
        'lights' => [],
        'validCodes' => ['A', 'B', 'C']
    ];

    /**
     * Magic __set
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Magic __get
     *
     * @param $name
     * @return null|mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefinierte Eigenschaft für __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' Zeile ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    /**
     * Construct
     */
    public function __construct()
    {
        $this->dbConnect();
    }

    /**
     * getCutStrip
     *
     * @param $cs
     * @param $ml
     * @param $end
     * @return string
     */
    public function getCutStrip($cs, $ml, $end)
    {
        $cutstrip = $cs;
        $maxlaenge = $ml;
        $cutstrip = (strlen($cutstrip) > $maxlaenge) ? substr($cutstrip, 0, $maxlaenge) . $end : $cutstrip;
        return $cutstrip;
    }

    /**
     * connect to db
     *
     * @return \PDO
     */
    public function dbConnect()
    {
        $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::MYSQL_ATTR_READ_DEFAULT_FILE => '/etc/my.cnf'
        ];

        $pdo = new PDO('mysql:dbname=' . self::$dbConfig['DB_NAME'] . ';host=' . self::$dbConfig['DB_HOST'], self::$dbConfig['DB_USER'], self::$dbConfig['DB_PWD'], $options);

        $this->__set('pdo', $pdo);

        return $pdo;
    }

    /**
     * getLightObjects
     *
     * @return array
     */
    public function getLightObjects()
    {
        /** @var \PDO $pdo */
        $pdo = $this->pdo;
        $sql = $pdo->prepare('SELECT * FROM  ' . self::$dbConfig['DB_PREFIX'] . 'devices  WHERE active = 1 ORDER BY sort DESC ');
        $lights = $pdo->query($sql->queryString)->fetchAll();

        $this->lights = $this->hydrateLight($lights);

        return $this->hydrateLight($lights);
    }

    /**
     * hydrateLight
     *
     * @param array $lights
     * @return array
     */
    public function hydrateLight(array $lights)
    {
        $rs = [];

        foreach ($lights as $lightItem) {
            // build objects
            $light = new stdClass();
            $light->id = $lightItem['id'];
            $light->room = $lightItem['room'];
            $light->name = $lightItem['device'];
            $light->active = $lightItem['active'];
            $light->status = $lightItem['status'];
            $light->sort = $lightItem['sort'];
            $light->code = $lightItem['code'];

            $rs[] = $light;
        }
        return $rs;
    }

    /**
     * getRoomById
     *
     * @param $id
     * @return object
     */
    public function getRoomById($id)
    {
        /** @var \PDO $pdo */
        $pdo = $this->pdo;
        $sql = $pdo->prepare('SELECT * FROM  ' . self::$dbConfig['DB_PREFIX'] . 'rooms WHERE id = ' . $id);
        $room = $pdo->query($sql->queryString)->fetchObject();

        return $room;
    }

    /**
     * getById
     *
     * @param $id
     * @return object
     */
    public function getById($id)
    {
        /** @var \PDO $pdo */
        $pdo = $this->pdo;
        $sql = $pdo->prepare('SELECT * FROM  ' . self::$dbConfig['DB_PREFIX'] . 'devices WHERE id = ' . $id);
        $light = $pdo->query($sql->queryString)->fetchObject();

        return $light;
    }

    /**
     * switchState
     *
     * @param stdClass $light
     * @return bool|string
     */
    public function switchState(stdClass $light)
    {
        /** @var \PDO $pdo */
        $pdo = $this->pdo;
        $sql = '';
        if ($light->status === '1') {
            // switch off
            $sql = $pdo->prepare('UPDATE `' . self::$dbConfig['DB_PREFIX'] . 'devices` SET `status` = 0 WHERE `id` = ' . $light->id);
            $light->status = 0;
        } else {
            // switch on
            $sql = $pdo->prepare('UPDATE `' . self::$dbConfig['DB_PREFIX'] . 'devices` SET `status` = 1 WHERE `id` = ' . $light->id);
            $light->status = 1;
        }

        if ($pdo->query($sql->queryString)->execute() === true) {


            return $this->switchSh($light->code, (int)$light->status);
        } else {
            return false;
        }
    }

    /**
     * allOff
     *
     */
    public function allOff()
    {
        $lights = $this->lights;
    }

    /**
     * switchSh
     *
     * @param $letter
     * @param $status
     * @return bool|string
     */
    private function switchSh($letter, $status)
    {
        settype($status, 'int');

        if ($status === 1) {
            $statusMode = 'ON';
        } else {
            $statusMode = 'OFF';
        }

        if (in_array($letter, $this->validCodes) === true) {

            return shell_exec("/home/www/inc/mumbiSet.sh {$letter} {$statusMode}");
        } else {
            return false;
        }
    }
}

