<?php

namespace suffi\Simple\Ext\Logger;

use Gelf\Message;
use Gelf\Publisher;
use Gelf\Transport\TcpTransport;
use Gelf\Transport\UdpTransport;
use suffi\Simple\Core\Logger;
use suffi\Simple\Core\Simple;

/**
 * Class GraylogLogger
 *
 * Класс для логирования в Graylog
 *
 * <pre>
 * 'Logger' => [
 *     'class' => 'suffi\Simple\Ext\Logger\GraylogLogger',
 *     'setters' => [
 *         'host' => '11.111.111.111',
 *         'port' => '12201',
 *         'prefix' => 'project_name',
 *     ]
 * ]
 *
 * </pre>
 *
 * @see https://github.com/bzikarsky/gelf-php
 */
class GraylogLogger extends Logger
{
    const CONNECT_TYPE_UPD = 'upd';
    const CONNECT_TYPE_TCP = 'tcp';

    /**
     * Хост
     * @var string
     */
    private $host = '';

    /**
     * Порт
     * @var string
     */
    private $port = '';

    /**
     * Префикс
     * @var string
     */
    private $prefix = '';


    /**
     * Тип соединения
     * @var string
     */
    private $connectType = self::CONNECT_TYPE_UPD;

    /** @var \Gelf\Publisher */
    private $publisher = null;

    /**
     * Тип соединения
     * @param string $connectType
     */
    public function setConnectType(string $connectType)
    {
        $this->connectType = $connectType;
    }

    /**
     * Установка хоста
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Установка порта
     * @param string $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Установка префикса
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Returns publisher
     *
     * @return \Gelf\Publisher
     */
    public function getPublisher()
    {
        try {
            if (!$this->publisher) {
                switch ($this->connectType) {
                    case self::CONNECT_TYPE_TCP:
                        $oTransport = new TcpTransport($this->host, $this->port);
                        break;
                    case self::CONNECT_TYPE_UPD:
                    default:
                        $oTransport = new UdpTransport($this->host, $this->port);
                        break;
                }


                $this->publisher = new Publisher();
                $this->publisher->addTransport($oTransport);
            }
        } catch (\Throwable $e) {
        }
        return $this->publisher;
    }

    public function log($level, $message, array $context = array())
    {
        try {
            if (!$context) {
                $fullMessage = $message;
            } else {
                $fullMessage = print_r($context, true);
            }
            $fullMessage .= "\r\n" . 'ip => ' . $this->getIP();
            $publisher = $this->getPublisher();
            if ($publisher) {
                $oMessage = new Message();
                $oMessage->setShortMessage($message)
                    ->setLevel($level)
                    ->setFullMessage($this->prefix . ' ' . $fullMessage)
                    ->setFacility(LOG_DEBUG);
                $publisher->publish($oMessage);
            }
        } catch (\Throwable $e) {
        }
    }

    /**
     * Ip запроса
     * @return string
     */
    protected function getIP()
    {
        return Simple::getRequest()->getIp();
    }
}
