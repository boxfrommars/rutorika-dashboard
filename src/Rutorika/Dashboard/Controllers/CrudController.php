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
class CrudController extends AbstractCrudController
{
    protected $_afterSaveRoute = 'self'; // 'self' (default) | 'index' | 'parent'
    protected $_afterDeleteRoute = 'parent'; // 'parent' (default) | 'index'

    protected $_viewPath = 'dashboard';
    /**
     * @param Entity|null $parentEntity
     */
    public function index($parentEntity = null)
    {
        $entitiesName = str_plural(camel_case($this->_name)); // множественное число имени сущностей (напр. `articles`)

        $viewParams = [
            $entitiesName => $this->_getEntities($parentEntity)
        ];
        if ($this->_parentName) {
            $viewParams[camel_case($this->_parentName)] = $parentEntity;
        }

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
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $entity = $this->_getEntity($id);
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
}