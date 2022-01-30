<?php

namespace Quantum\Migration;

class MigrationTemplate
{

    public static function create($className, $tableName)
    {
        return '<?php

use Quantum\Migration\QtMigration;
use Quantum\Factory\TableFactory;


class ' . $className . ' extends QtMigration
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

class ' . $className . ' extends QtMigration
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

}