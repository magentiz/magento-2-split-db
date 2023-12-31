<?php
/**
 * Copyright © Magentiz. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Magentiz\SplitDb\Adapter\Pdo;

use Magento\Framework\DB\Adapter\Pdo\Mysql as OriginalMysqlPdo;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\DB\SelectFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils;
use \Magento\Framework\Registry;
use Magento\Framework\App\State;

class Mysql extends OriginalMysqlPdo implements AdapterInterface
{
    /**
     * @var CloneMysql
     */
    private $readConnection;
    /**
     * @var State
     */
    private $state;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * Mysql constructor.
     * @param State $state
     * @param Registry $registry
     * @param StringUtils $string
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     * @param SelectFactory $selectFactory
     * @param array $config
     * @param SerializerInterface|null $serializer
     */
    public function __construct(
        State $state,
        Registry $registry,
        StringUtils $string,
        DateTime $dateTime,
        LoggerInterface $logger,
        SelectFactory $selectFactory,
        array $config = [],
        SerializerInterface $serializer = null
    ) {
        $this->state = $state;
        $this->_registry = $registry;
        if (isset($config['slaves']) && isset($config['is_split'])) {
            // Keep the same slave throughout the request
            $slaveIndex = rand(0, (count($config['slaves']) - 1));
            $slaveConfig = $config['slaves'][$slaveIndex];
            unset($config['slaves']);
            if ($config['is_split']) {
                $slaveConfig = array_merge(
                    $config,
                    $slaveConfig
                );
                $this->readConnection = ObjectManager::getInstance()->create(CloneMysql::class, [
                    'string' => $string,
                    'dateTime' => $dateTime,
                    'logger' => $logger,
                    'selectFactory' => $selectFactory,
                    'config' => $slaveConfig,
                    'serializer' => $serializer,
                ]);
            }
        }

        parent::__construct(
            $string,
            $dateTime,
            $logger,
            $selectFactory,
            $config,
            $serializer
        );
    }

    /**
     * @return bool
     */
    protected function isAdmin()
    {
        try {
            $areaCode = $this->state->getAreaCode();
        } catch (\Exception $e) {
            $areaCode = null;
        }
        return ($areaCode == \Magento\Framework\App\Area::AREA_ADMINHTML);
    }

    /**
     * Check if query is readonly
     * @param string $sql
     * @return bool
     */
    protected function canUseReader($sql)
    {
        if ($this->isAdmin()) {
            return false;
        }
        if ($this->_registry->registry('useWriter')) {
            return false;
        }
        if (!$this->readConnection) {
            return false;
        }
        // For certain circumstances we want to for using the writer
        if (php_sapi_name() == 'cli') {
            return false;
        }

        $writerSqlIdentifiers = [
            'INSERT ',
            'UPDATE ',
            'DELETE ',
            'DROP ',
            'CREATE ',
            'search_tmp',
            'GET_LOCK',
            'TRUNCATE'
        ];
        foreach ($writerSqlIdentifiers as $writerSqlIdentifier) {
            if (stripos(substr($sql, 0, 20), $writerSqlIdentifier) !== false) {
                if ($writerSqlIdentifier != 'GET_LOCK') {
                    $this->_registry->register('useWriter', true);
                }
                return false;
            }
        }

        if (stripos(substr($sql, 0, 120), 'FOR UPDATE') !== false) {
            return false;
        }

        // Ignore use read connection for table quote
        if (preg_match('/\bfrom\b.*?\bquote+([\w-]+)\b/i', $sql)) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function multiQuery($sql, $bind = [])
    {
        if ($this->canUseReader($sql)) {
            return $this->readConnection->multiQuery($sql, $bind);
        }
        return parent::multiQuery($sql, $bind);
    }

    /**
     * {@inheritdoc}
     */
    public function query($sql, $bind = [])
    {
        if ($this->canUseReader($sql)) {
            return $this->readConnection->query($sql, $bind);
        }
        return parent::query($sql, $bind);
    }
}
