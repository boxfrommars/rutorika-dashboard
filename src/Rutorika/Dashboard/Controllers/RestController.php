<?php

namespace Rutorika\Dashboard\Controllers;

use Rutorika\Dashboard\Entities\Entity;

/**
 * Class RestController
 *
 * @package Rutorika\Dashboard\Controllers
 */
class RestController extends AbstractCrudController
{
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
        $action = $id === null ? 'create' : 'update';
        $input = $this->_getInput();

        $validator = $this->_getValidator($input, $action, $id);

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
     * create form view
     *
     * @param Entity|null $parentEntity
     */
    public function create($parentEntity = null)
    {
        // TODO: Implement create() method.
    }
}