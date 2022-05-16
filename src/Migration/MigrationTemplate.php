<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.7.0
 */

namespace Quantum\Migration;

/**
 * Class MigrationTable
 * @package Quantum\Migration
 */
class MigrationTemplate
{

    /**
     * Create migration template
     * @param string $className
     * @param string $tableName
     * @return string
     */
    public static function create($className, $tableName)
    {
        return '<?php

use Quantum\Migration\QtMigration;
use Quantum\Factory\TableFactory;
use Quantum\Libraries\Database\Schema\Type;
use Quantum\Libraries\Database\Schema\Key;


class ' . ucfirst($className) . ' extends QtMigration
{
    
    public function up(?TableFactory $tableFactory) {
        $table = $tableFactory->create(\'' . $tableName . '\');
    }
    
    public function down(?TableFactory $tableFactory)
    {
        $tableFactory->drop(\'' . $tableName . '\');
    }

}
       
        ';
    }

    /**
     * Alter migration template
     * @param string $className
     * @param string $tableName
     * @return string
     */
    public static function alter($className, $tableName)
    {
        return '<?php

use Quantum\Migration\QtMigration;
use Quantum\Factory\TableFactory;
use Quantum\Libraries\Database\Type;
use Quantum\Libraries\Database\Schema\Key;

class ' . ucfirst($className) . ' extends QtMigration
{
    
    public function up(?TableFactory $tableFactory) {
        $table = $tableFactory->get(\'' . $tableName . '\');
    }
    
    public function down(?TableFactory $tableFactory)
    {
        $table = $tableFactory->get(\'' . $tableName . '\');
    }

}
       
        ';
    }

    /**
     * Rename migration template
     * @param string $className
     * @param string $tableName
     * @return string
     */
    public static function rename($className, $tableName)
    {
        return '<?php

use Quantum\Migration\QtMigration;
use Quantum\Factory\TableFactory;

class ' . ucfirst($className) . ' extends QtMigration
{
    
    public function up(?TableFactory $tableFactory) {
        $tableFactory->rename(\'' . $tableName . '\', $newName);
    }
    
    public function down(?TableFactory $tableFactory)
    {
        $tableFactory->rename($newName, \'' . $tableName . '\');
    }

}
       
        ';
    }

    /**
     * Drop migration template
     * @param string $className
     * @param string $tableName
     * @return string
     */
    public static function drop($className, $tableName)
    {
        return '<?php

use Quantum\Migration\QtMigration;
use Quantum\Factory\TableFactory;

class ' . ucfirst($className) . ' extends QtMigration
{
    
    public function up(?TableFactory $tableFactory) {
        $tableFactory->drop(\'' . $tableName . '\');
    }
    
    public function down(?TableFactory $tableFactory)
    {
        //
    }

}
       
        ';
    }

}
