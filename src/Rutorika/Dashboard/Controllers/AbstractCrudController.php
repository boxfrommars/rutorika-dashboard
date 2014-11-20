<?php

namespace Rutorika\Dashboard\Controllers;

use Rutorika\Dashboard\Entities\Entity;

/**
 * Class AbstractCrudController
 *
 * @TODO сделать все сообщнения через \Lang
 *
 * @package Rutorika\Dashboard\Controllers
 */
abstract class AbstractCrudController extends BaseController
{
    protected $_entity;
    protected $_name;
    protected $_parentName; // entity parent name
    protected $_rules = []; // entity validation rules
    protected $_createRules = null;
    protected $_updateRules = null;
    protected $_perPage = null;

    /**
     * @param Entity|null $parentEntity
     */
    public abstract function index($parentEntity = null);

    /**
     * view form view
     *
     * @param int $id
     */
    public abstract function view($id);

    /**
     * create form view
     *
     * @param Entity|null $parentEntity
     */
    public abstract function create($parentEntity = null);

    /**
     * @param null|int $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public abstract function store($id = null);


    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public abstract function destroy($id);

    /**
     * Устанавливаем в сущность родительскую сущность, например $entity->book_id = $book->id
     *
     * @param Entity $entity
     * @param Entity $parentEntity
     */
    protected function _setParent($entity, $parentEntity)
    {
        $parentProperty = $this->_getParentProperty();
        $entity->$parentProperty = $parentEntity->id;
    }

    /**
     * атрибут сущности указывающий на родителя, например `book_id`
     *
     * @return string
     */
    protected function _getParentProperty()
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
     * @param array $input
     * @param string $action update|create
     * @param null $id
     * @return \Illuminate\Validation\Validator
     */
    protected function _getValidator($input, $action = 'update', $id = null)
    {
        $rulesName = '_' . $action . 'Rules';
        $rules = $this->$rulesName !== null ? $this->$rulesName : $this->_rules;

        // заменяем в рулзах строки вида %code% на соответствующие значения $input ($input['code'])
        $replacer = array_merge($input, ['id' => $id]);
        $rules = array_map(function($rule) use ($replacer) {
            return str_replace(
                array_map(function($key){ return "%{$key}%"; }, array_keys($replacer)), // обрамляем ключи знаками процента
                array_dot(array_values($replacer)), // если значение было multidimensional vfccbdjv, превращаем в плоский с точка-нотацией
                $rule
            );
        }, $rules);

        return \Validator::make($input, $rules);
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
        $parentAttribute = $this->_getParentProperty();

        $query = $parentEntity === null ? $entityClass::query() : $entityClass::where($parentAttribute, $parentEntity->id);

        if ($this->_perPage) {
            return $query->paginate($this->_perPage);
        } else {
            return $query->get();
        }
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