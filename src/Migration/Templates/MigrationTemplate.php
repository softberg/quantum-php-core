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
 * @since 3.0.0
 */

namespace Quantum\Migration\Templates;

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
    public static function create(string $className, string $tableName): string
    {
        return '<?php

use Quantum\Libraries\Database\Factories\TableFactory;


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
    public static function alter(string $className, string $tableName): string
    {
        return '<?php

use Quantum\Libraries\Database\Factories\TableFactory;

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
    public static function rename(string $className, string $tableName): string
    {
        return '<?php

use Quantum\Libraries\Database\Factories\TableFactory;

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
    public static function drop(string $className, string $tableName): string
    {
        return '<?php

use Quantum\Libraries\Database\Factories\TableFactory;

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
