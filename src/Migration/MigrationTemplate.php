<?php

namespace Quantum\Migration;

class MigrationTemplate
{

    public static function create($className, $tableName)
    {
        return '<?php

use Quantum\Migration\QtMigration;
use Quantum\Factory\TableFactory;
use Quantum\Libraries\Database\Type;


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

    public static function alter($className, $tableName)
    {
        return '<?php

use Quantum\Migration\QtMigration;
use Quantum\Factory\TableFactory;
use Quantum\Libraries\Database\Type;

class ' . ucfirst($className) . ' extends QtMigration
{
    
    public function up(?TableFactory $tableFactory) {
        $table = $tableFactory->get(\'' . $tableName . '\');
    }
    
    public function down(?TableFactory $tableFactory)
    {
        //
    }

}
       
        ';
    }

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
