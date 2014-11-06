<?php

namespace Rutorika\Dashboard\Controllers;

use Rutorika\Dashboard\Entities\Entity;

/**
 * Class CrudController
 *
 * @TODO сделать все сообщнения через \Lang
 *
 * @package Rutorika\Dashboard\Controllers
 */
class CrudController extends BaseController
{
    protected $_entity;
    protected $_name;
    protected $_parentName; // entity parent name
    protected $_rules = []; // entity validation rules
    protected $_createRules = null;
    protected $_updateRules = null;

    protected $_afterSaveRoute = 'self'; // 'self' (default) | 'index' | 'parent'
    protected $_afterDeleteRoute = 'parent'; // 'parent' (default) | 'index'

    protected $_viewPath = 'dashboard';

    public function __construct(){}

    /**
     * @param Entity|null $parentEntity
     */
    public function index($parentEntity = null)
    {
        $entityClass = $this->_getEntityClass();
        $parentProperty = $this->_getParentProperty();
        $entitiesName = str_plural(camel_case($this->_name)); // множественное число имени сущностей (напр. `articles`)

        $entities = $parentEntity === null ? $entityClass::all() : $entityClass::where($parentProperty, $parentEntity->id);

        $viewParams = [$entitiesName => $entities];

        $this->_populateIndexView($viewParams);
    }

    /**
     * view form view
     *
     * @param int $id
     */
    public function view($id)
    {
        $entity = $this->_getEntity($id);
        $parentName = $this->_parentName;

        $viewParams = [camel_case($this->_name) => $entity];
        if ($parentName) {
            $viewParams[camel_case($parentName)] = $entity->$parentName;
        }
        $this->_populateCreateView($viewParams);
    }

    /**
     * create form view
     *
     * @param Entity|null $parentEntity
     */
    public function create($parentEntity = null)
    {
        $entity = $this->_getEntity();
        $parentName = $this->_parentName;

        $viewParams = [camel_case($this->_name) => $entity];
        if ($parentName) {
            $this->_setParent($entity, $parentEntity);
            $viewParams[camel_case($parentName)] = $parentEntity;
        }
        $this->_populateCreateView($viewParams);
    }

    /**
     * @param null|int $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store($id = null)
    {
        $action = $id === null ? 'create' : 'update';
        $input = $this->_getInput();
        $validator = $this->_getValidator($input, $action, $id);

        if ($validator->fails()) {
            \Flash::error('Проверьте правильность введённых данных');
            return \Redirect::back()->withInput()->withErrors($validator);
        } else {
            $entity = $this->_getEntity($id);
            $entity->fill($input);
            $entity->save();

            $this->_onEntitySaved($entity, $input);

            \Flash::success('Сохранено');
            return $this->_redirectTo($this->_afterSaveRoute, $entity);
        }
    }

    /**
     * @param Entity $entity
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($entity)
    {
        $entity->delete();
        \Flash::success('Удалено');

        if ($this->_parentName) {
            $parentName = $this->_parentName;
            $parent = $entity->$parentName;
            return $this->_redirectTo($this->_afterDeleteRoute, null, $parent);
        } else {
            return $this->_redirectTo('index');
        }
    }

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
     * @param array $viewParams
     */
    protected function _populateCreateView($viewParams = [])
    {
        $viewParams['name'] = $this->_name;
        $this->_populateView("{$this->_viewPath}.{$this->_name}.create", $viewParams);
    }

    /**
     * @param array $viewParams
     */
    protected function _populateIndexView($viewParams = [])
    {
        $viewParams['name'] = $this->_name;
        $this->_populateView("{$this->_viewPath}.{$this->_name}.index", $viewParams);
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
     * @return Entity
     */
    protected function _getEntityClass()
    {
        return $this->_entity;
    }

    /**
     * @param null|string $routeName
     * @param Entity $entity
     * @param Entity $parent
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function _redirectTo($routeName = null, $entity = null, $parent = null)
    {
        switch ($routeName) {
            case 'self':
                return \Redirect::route(".{$this->_name}.view", $entity->id);
                break;
            case 'parent':
                $parentName = $this->_parentName;
                $route = ".{$parentName}.view";

                $parentId = $parent ? $parent->id : $entity->$parentName->id;
                return \Redirect::route($route, $parentId);
                break;
            case 'index':
                $parentName = $this->_parentName;
                $route = ".{$this->_name}.index";

                if ($parent || $parentName)  {
                    $parentId = $parent ? $parent->id : $entity->$parentName->id;
                    return \Redirect::route($route, $parentId);
                } else {
                    return \Redirect::route($route);
                }
                break;
            default:
                return \Redirect::route(".{$this->_name}.view", $entity->id);
                break;
        }
    }

    /**
     * @param Entity $entity
     * @param array $input
     */
    protected function _onEntitySaved($entity, $input = []) {}
}