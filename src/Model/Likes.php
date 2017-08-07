<?php

namespace Bolt\Extension\TwoKings\Likes\Model;

use Silex\Application;

/**
 *
 */
class Likes {

    protected $contenttype;
    protected $contentid;
    protected $data;

    /* @var Silex\Application $app */
    protected $app;

    /** @var string $table Name of the likes table */
    private $table = 'bolt_likes';

    /**
     *
     */
    public function __construct(Application $app, $contenttype, $contentid)
    {
        $this->contenttype = $contenttype;
        $this->contentid = $contentid;
        $this->app = $app;
        $this->fetch();
    }

    /**
     *
     */
    private function fetch()
    {
        $stmt = $this->app['db']->prepare("SELECT * FROM :table WHERE contenttype = :ct AND contentid = :id LIMIT 1;");
        $stmt->bindValue('table', $this->table);
        $stmt->bindValue('ct', $this->contenttype);
        $stmt->bindValue('id', $this->contentid);
        $stmt->execute();
        $this->data = $stmt->fetch();

        if (!$this->data) {
            $this->data = [
               'id'          => null,
               'contenttype' => $this->contenttype,
               'contentid'   => $this->contentid,
               'totals'      => [],
               'ips'         => []
            ];
        } else {
            $this->data['totals'] = json_decode($this->data['totals'], true);
            $this->data['ips']    = json_decode($this->data['ips'], true);
        }
    }

    /**
     *
     */
    public function set($type, $value)
    {
        $ip = $this->getIP();

        if (!isset($this->data['totals'][$type])) {
            $this->data['totals'][$type] = 0;
        }

        // If we have it in the latest scores, don't count it again.
        if (isset($this->data['ips'][$ip])) {
            $this->data['totals'][$type]--;
        }

        if ($value) {
            $this->data['ips'][$ip] = $type;
            $this->data['totals'][$type]++;
        } else {
            unset($this->data['ips'][$ip]);
        }

        $this->persist();
    }

    /**
     *
     */
    private function getIP()
    {
        return $this->app['request']->getClientIp();
    }

    /**
     *
     */
    private function persist()
    {
        $data = $this->data;
        $data['totals'] = json_encode($data['totals']);
        $data['ips']    = json_encode($data['ips']);

        if ($this->data['id'] === null) {
            $this->app['db']->insert($this->table, $data);
        } else {
            $this->app['db']->update($this->table, $data, ['id' => $data['id']]);
        }
    }

    /**
     *
     */
    public function getData($includeIPs = false)
    {
        if (array_key_exists($this->getIP(), $this->data['ips'])) {
            $this->data['current'] = true;
        } else {
            $this->data['current'] = false;
        }

        if (!$includeIPs) {
            unset($this->data['ips']);
        }

        return $this->data;
    }

}
