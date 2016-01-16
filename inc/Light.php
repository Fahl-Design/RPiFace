<?php

/**
 * Class light
 */
class light
{
    /**
     * VALID_CODES
     *
     * Can be used to hide lamps
     */
    const VALID_CODES = '"A", "C"';
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
        'statusAll' => false
    ];

    /**
     * Construct
     * @param bool $new
     */
    public function __construct($new = true)
    {
        $this->dbConnect();
        if ($new === true) {
            $this->getLightObjects();
            $this->updateStatusAll();
        } else {
            // hydrate mode
        }
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
        $sql = $pdo->prepare('SELECT * FROM  ' . self::$dbConfig['DB_PREFIX'] . 'devices WHERE code IN (' . self::VALID_CODES . ') ORDER BY sort DESC ');
        $lights = $pdo->query($sql->queryString)->fetchAll();

        $this->lights = $this->hydrateLight($lights);

        return $this->lights;
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
            $light = new light(false);
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

    private function updateStatusAll()
    {
        $rs = 0;

        foreach ($this->lights as $light) {
            /** @var light $light */
            if ((int)$light->status === 1)
                $rs++;
        }

        if (count($this->lights) === $rs) {
            $this->statusAll = true;
        } else {
            $this->statusAll = false;
        }

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
     * @return light
     */
    public function getById($id)
    {
        foreach ($this->lights as $light) {
            if ($light->id == $id) {
                /** @var light $light */
                return $light;
            }
        }
        return null;
    }

    /**
     * getByCode
     *
     * @param $code
     * @return light
     */
    public function getByCode($code)
    {
        foreach ($this->lights as $light) {
            if ($light->code == $code) {
                /** @var light $light */
                return $light;
            }
        }
        return null;
    }

    /**
     * allOff
     *
     */
    public function allOff()
    {
        $return = [];
        foreach ($this->lights as $light) {
            /** @var light $light */
            $return[] = [
                'light' => $light->code,
                'state' => str_replace("\n", '', $light->switchState('OFF'))
            ];
        }
        return $return;
    }

    /**
     * forceAll
     *
     * @param $state
     * @return bool|string
     */
    private function forceAll($state)
    {
        foreach ($this->lights as $light) {
            /** @var light $light */
            return $light->switchState($state);
        }
    }

    /**
     * switchState
     *
     * @param null $state
     * @return bool|string
     */
    public function switchState($state = null)
    {
        if (($this->status == '1' && empty($state)) || $state === 'OFF') {
            // switch off
            return $this->switchOff();
        } else if (($this->status == '0' && empty($state)) || $state === 'ON') {
            // switch on
            return $this->switchOn();
        }
    }

    /**
     * switchOff
     * @return bool|string
     */
    public function switchOff()
    {
        if ($this->switchSQLState() === true) {
            return shell_exec("/home/www/inc/mumbiSet.sh {$this->code} OFF");
        } else {
            return false;
        }
    }

    /**
     * switchSQLState
     *
     * @return bool
     */
    private function switchSQLState()
    {
        if ($this->status == '0') {
            $sql = $this->pdo->prepare('UPDATE `' . self::$dbConfig['DB_PREFIX'] . 'devices` SET `status` = 1 WHERE `id` = ' . $this->id);
            $this->status = '1';
        } else {
            $sql = $this->pdo->prepare('UPDATE `' . self::$dbConfig['DB_PREFIX'] . 'devices` SET `status` = 0 WHERE `id` = ' . $this->id);
            $this->status = '0';

        }

        return $this->pdo->query($sql->queryString)->execute();
    }

    /**
     * switchOn
     *
     * @return bool|string
     */
    public function switchOn()
    {
        if ($this->switchSQLState() === true) {
            return shell_exec("/home/www/inc/mumbiSet.sh {$this->code} ON");
        } else {
            return false;
        }
    }

    /**
     * allOn
     *
     */
    public function allOn()
    {
        $return = [];
        foreach ($this->lights as $light) {
            /** @var light $light */

            $return[] = [
                'light' => $light->code,
                'state' => str_replace("\n", '', $light->switchState('ON'))
            ];
        }

        return $return;
    }
}

