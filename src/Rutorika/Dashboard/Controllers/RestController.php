<?php

namespace Rutorika\Dashboard\Controllers;

use Rutorika\Dashboard\Entities\Entity;

/**
 * Class RestController
 *
 * @package Rutorika\Dashboard\Controllers
 */
class RestController extends BaseController
{
    protected $_entity;
    protected $_name;
    protected $_parentName; // entity parent name
    protected $_rules = []; // entity validation rules

    public function __construct(){}

    public function index($parentEntity = null)
    {
        return $this->_getEntities($parentEntity);
    }

    public function view($id)
    {
        return $this->_getEntity($id);
    }

    /**
     * @param null $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store($id = null)
    {
        $rules = $this->_rules;
        $input = $this->_getInput();

        $validator = \Validator::make($input, $rules);

        if ($validator->fails()) {
            return \Response::json(['success' => false, 'errors' => $validator->errors()], 422);
        } else {
            $entity = $this->_getEntity($id);
            $entity->fill($input);
            $entity->save();

            $this->_onEntitySaved($entity, $input);

            return $entity;
        }
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $entity = $this->_getEntity($id);
        $entity->delete();
        return ['success' => true];
    }

    /**
     * атрибут сущности указывающий на родителя, например `book_id`
     *
     * @return string
     */
    protected function _getParentAttribute()
    {
        return $this->_parentName . '_id';
    }

    /**
     * @return array
     */
    protected function _getInput()
    {
        return \Input::all();
    }

    /**
     * Получаем новую сущность, если id === null или уще имеющуюся в другом случае
     *
     * @param null|int $id
     * @return Entity
     */
    protected function _getEntity($id = null)
    {
        /** @var Entity $entityClass */
        $entityClass = $this->_getEntityClass();
        return $id !== null ? $entityClass::findOrFail($id) : new $entityClass;
    }

    /**
     * @param Entity $parentEntity
     * @return $this|\Illuminate\Database\Eloquent\Collection|static[]
     */
    protected function _getEntities($parentEntity = null)
    {
        $entityClass = $this->_getEntityClass();
        $parentAttribute = $this->_getParentAttribute();

        return $parentEntity === null ? $entityClass::all() : $entityClass::where($parentAttribute, $parentEntity->id);
    }

    /**
     * @return Entity
     */
    protected function _getEntityClass()
    {
        return $this->_entity;
    }

    /**
     * @param Entity $entity
     * @param array $input
     */
    protected function _onEntitySaved($entity, $input = []) {}
}