<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace ADmad\Sequence\Shell\Task;

use Cake\Database\Schema\TableSchema;

/**
 * Task class for generating model files.
 *
 * @property \Bake\Shell\Task\FixtureTask $Fixture
 * @property \Bake\Shell\Task\BakeTemplateTask $BakeTemplate
 * @property \Bake\Shell\Task\TestTask $Test
 */
class ModelTask extends \Bake\Shell\Task\ModelTask
{

    /**
     * @param TableSchema $schema
     * @param array $allFields
     * @param array $checkFields
     * @return mixed|null
     */
    private function _getSortField(TableSchema $schema, array $allFields = [], array $checkFields = []) {
        foreach ($checkFields as $checkField) {
            if (in_array($checkField, $allFields) && $schema->getColumnType($checkField) === 'integer') {
                return $checkField;
            }
        }

        return null;
    }

    /**
     * Get behaviors
     *
     * @param \Cake\ORM\Table $model The model to generate behaviors for.
     * @return array Behaviors
     */
    public function getBehaviors($model)
    {
        $behaviors = [];
        $schema = $model->getSchema();
        $fields = $schema->columns();

        if (empty($fields)) {
            return [];
        }

        $sortField = $this->_getSortField($schema, $fields, ['position', 'sort']);

        if (!is_null($sortField)) {
            $behaviors['ADmad/Sequence.Sequence'] = [
                'order' => "'$sortField'", // Field to use to store integer sequence. Default "position".
                //'scope' => ['group_id'], // Array of field names to use for grouping records. Default [].
                'start' => 1, // Initial value for sequence. Default 1.
            ];
        }

        return array_merge($behaviors, parent::getBehaviors($model));
    }

}
