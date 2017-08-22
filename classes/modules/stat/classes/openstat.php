<?php

class openstat
{
    private $str;
    private $data = array();
    private $error = false;

    public function __construct($str)
    {
        if (!strpos($str, ';')) {
            $str = str_replace(array('*', '-'), array('+', '/'), $str);
            $str = base64_decode($str);
        }

        $this->str = $str;

        $openstat = explode(';', $str, 4);

        if (empty($openstat[0]) || empty($openstat[2])) {
            throw new Exception('Имя сервиса и рекламного объявления обязательные параметры');
        }

        if (sizeof($openstat) == 4) {
            $this->parse($openstat);
        } else {
            throw new Exception('Число аргументов != 4');
        }
    }

    public function getServiceId()
    {
        return $this->data['service_id'];
    }

    public function getCampaignId()
    {
        return $this->data['campaign_id'];
    }

    public function getAdId()
    {
        return $this->data['ad_id'];
    }

    public function getSourceId()
    {
        return $this->data['source_id'];
    }

    private function parse($openstat)
    {
        list($openstat_service, $openstat_campaign, $openstat_ad, $openstat_source) = $openstat;

        $this->appendData($openstat_service, 'service');
        $this->appendData($openstat_campaign, 'campaign');
        $this->appendData($openstat_ad, 'ad');
        $this->appendData($openstat_source, 'source');
    }

    private function appendData($data, $type)
    {
		$connection = ConnectionPool::getInstance()->getConnection();
    	$type = $connection->escape($type);
    	
        $qry = "SELECT   `id` FROM `cms_stat_sources_openstat_" . $type . "` WHERE `name` = '" . $connection->escape($data) . "'";
		$result = $connection->queryResult($qry);
		$result->setFetchType(IQueryResult::FETCH_ASSOC);
		$row = $result->fetch();

        if (isset($row['id'])) {
            $this->data[$type . '_id'] = $row['id'];
        } else {
            $qry = "INSERT INTO `cms_stat_sources_openstat_" . $type . "` (`name`) VALUES ('" . $connection->escape($data) . "')";
			$connection->query($qry);
            $this->data[$type . '_id'] = $connection->insertId();
        }
    }
}

?>