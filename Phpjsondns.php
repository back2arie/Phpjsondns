<?php

class Phpjsondns {

    private $hostname;
    private $type;
    private $typeCons;
    private $result;
    private $error;

    public function __construct($hostname = '', $type = '')
    {
        $this->setHostname($hostname);
        $this->setType($type);
    }

    public function setHostname($hostname = '')
    {
        if(!empty($hostname))
        {
            $this->hostname = $hostname;
        }
    }

    public function setType($type = '')
    {
        if(!empty($type))
        {
            if(is_null($this->type))
            {
                $this->type = strtoupper($type);
            }
            else
            {
                $this->type = strtoupper('any');
            }
        }
    }

    public function getHostname()
    {
        return $this->hostname;
    }

    public function getType()
    {
        return $this->type;
    }

    private function checkType()
    {
        if(is_null($this->getHostname()) OR is_null($this->getType()))
        {
            $this->setError('Please set hostname and type');
            return FALSE;
        }

        $typeCons = 'DNS_'.$this->getType();

        if (!defined($typeCons))
        {
            $this->setError('Unknown record type '.$this->getType());
        }
        else
        {
            $this->typeCons = constant($typeCons);
        }
    }

    private function setError($error = '')
    {
        $this->error = $error;
    }

    private function getError()
    {
        if(empty($this->error)) return FALSE;
        else return $this->error;
    }

    private function getResult()
    {
        $result['request']['hostname'] = $this->getHostname();
        $result['request']['type'] = $this->getType();

        if(!$this->getError())
        {
            $result['result'] = $this->result;
        }
        else
        {
            $result['error'] = $this->getError();
        }

        return $result;
    }

    private function dnsQuery()
    {
        if($this->getError())
        {
            return FALSE;
        }

        $query = dns_get_record($this->getHostname(), $this->typeCons);

        if(!$query)
        {
            $this->setError('Cannot complete query, check hostname');
        }
        else
        {
            $this->result = $query;
        }
    }

    private function toJson($data = array())
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function get($hostname = '', $type = '')
    {
        $this->setHostname($hostname);
        $this->setType($type);
        $this->checkType();
        $this->dnsQuery();
        $result = $this->getResult();
        return $this->toJson($result);
    }
}
